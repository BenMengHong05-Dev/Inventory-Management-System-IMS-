<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AssetReturnController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $sql = "
            SELECT ar.*, a.asset_code, a.asset_name,
                   e.name_en as returned_by_name,
                   u.name as receiver_name,
                   wl.code as location_code
            FROM asset_returns ar
            JOIN assets a ON ar.asset_id = a.id
            LEFT JOIN employees e ON ar.returned_by = e.id
            JOIN users u ON ar.received_by = u.id
            LEFT JOIN warehouse_locations wl ON ar.location_id = wl.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (a.asset_code LIKE ? OR a.asset_name LIKE ? OR e.name_en LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term, $term]);
        }

        $sql .= " ORDER BY ar.id DESC";

        $returns = raw_paginate($sql, $params, 15);

        return view('assets.returns.index', compact('returns', 'search'));
    }

    public function create(Request $request)
    {
        $assetId = $request->get('asset_id');

        $assignedAssets = DB::select("
            SELECT a.id, a.asset_code, a.asset_name, a.current_employee_id, e.name_en as employee_name,
                   aa.id as assignment_id
            FROM assets a
            JOIN asset_assignments aa ON a.id = aa.asset_id AND aa.actual_return_date IS NULL
            LEFT JOIN employees e ON a.current_employee_id = e.id
            WHERE a.status IN ('ASSIGNED', 'IN_USE') AND a.deleted_at IS NULL
            ORDER BY a.asset_name ASC
        ");

        $locations = DB::select("SELECT id, code FROM warehouse_locations WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY code ASC");

        return view('assets.returns.create', compact('assignedAssets', 'locations', 'assetId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|integer',
            'return_date' => 'required|date',
            'return_condition' => 'required|in:NEW,GOOD,FAIR,DAMAGED,CRITICAL',
            'location_id' => 'nullable|integer',
            'note' => 'nullable|string',
        ]);

        $userId = session('user_id');

        $asset = DB::select("SELECT * FROM assets WHERE id = ? LIMIT 1", [$request->asset_id])[0];

        // Find active assignment
        $assignments = DB::select("
            SELECT id, employee_id, department_id, assignment_date
            FROM asset_assignments
            WHERE asset_id = ? AND actual_return_date IS NULL
            ORDER BY id DESC
            LIMIT 1
        ", [$request->asset_id]);

        $assignmentId = !empty($assignments) ? $assignments[0]->id : null;
        $prevHolderId = !empty($assignments) ? $assignments[0]->employee_id : $asset->current_employee_id;
        $deptId = !empty($assignments) ? $assignments[0]->department_id : $asset->current_department_id;

        DB::transaction(function () use ($request, $asset, $assignmentId, $prevHolderId, $deptId, $userId) {
            $uuid = (string) Str::uuid();

            // 1. Insert Return Record
            DB::insert("
                INSERT INTO asset_returns (
                    uuid, asset_id, asset_assignment_id, returned_by, return_date,
                    return_condition, received_by, location_id, note, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ", [
                $uuid,
                $request->asset_id,
                $assignmentId,
                $prevHolderId,
                $request->return_date,
                $request->return_condition,
                $userId,
                $request->location_id ?: null,
                $request->note
            ]);

            $returnId = DB::getPdo()->lastInsertId();

            // 2. Close active assignment
            if ($assignmentId) {
                DB::update("
                    UPDATE asset_assignments
                    SET actual_return_date = ?, updated_at = NOW()
                    WHERE id = ?
                ", [$request->return_date, $assignmentId]);
            }

            // 3. Update Asset: status back to IN_STOCK (or MAINTENANCE if damaged/critical)
            $newStatus = in_array($request->return_condition, ['DAMAGED', 'CRITICAL']) ? 'MAINTENANCE' : 'IN_STOCK';

            DB::update("
                UPDATE assets
                SET status = ?,
                    condition = ?,
                    current_employee_id = NULL,
                    current_department_id = NULL,
                    location_id = ?,
                    updated_at = NOW()
                WHERE id = ?
            ", [
                $newStatus,
                $request->return_condition,
                $request->location_id ?: null,
                $request->asset_id
            ]);

            // 4. Record Immutable Assignment History
            DB::insert("
                INSERT INTO asset_assignment_histories (
                    asset_id, previous_holder_id, new_holder_id, department_id,
                    assignment_date, return_date, action, user_id, note, created_at
                ) VALUES (?, ?, NULL, ?, ?, ?, 'RETURN', ?, ?, NOW())
            ", [
                $request->asset_id,
                $prevHolderId,
                $deptId,
                $assignments[0]->assignment_date ?? $request->return_date,
                $request->return_date,
                $userId,
                "Returned in {$request->return_condition} condition: " . ($request->note ?: 'No notes')
            ]);

            audit_log('RETURN', 'Asset', 'AssetReturn', $returnId, null, [
                'asset_id' => $request->asset_id,
                'condition' => $request->return_condition,
                'new_status' => $newStatus
            ]);
        });

        return redirect()->route('asset-returns.index')->with('success', 'Asset returned successfully and status updated.');
    }
}
