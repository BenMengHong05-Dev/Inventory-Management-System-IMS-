<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AssetAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $employeeId = $request->get('employee_id');

        $sql = "
            SELECT aa.*, a.asset_code, a.asset_name,
                   e.name_en as employee_name, e.code as employee_code,
                   d.name_en as department_name, wl.code as location_code,
                   u.name as assigned_by_name
            FROM asset_assignments aa
            JOIN assets a ON aa.asset_id = a.id
            LEFT JOIN employees e ON aa.employee_id = e.id
            LEFT JOIN departments d ON aa.department_id = d.id
            LEFT JOIN warehouse_locations wl ON aa.location_id = wl.id
            JOIN users u ON aa.assigned_by = u.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (a.asset_code LIKE ? OR a.asset_name LIKE ? OR e.name_en LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term, $term]);
        }

        if (!empty($employeeId)) {
            $sql .= " AND aa.employee_id = ?";
            $params[] = $employeeId;
        }

        $sql .= " ORDER BY aa.id DESC";

        $assignments = raw_paginate($sql, $params, 15);

        return view('assets.assignments.index', compact('assignments', 'search', 'employeeId'));
    }

    public function create(Request $request)
    {
        $assetId = $request->get('asset_id');
        $assets = DB::select("
            SELECT id, asset_code, asset_name
            FROM assets
            WHERE status = 'IN_STOCK' AND deleted_at IS NULL
            ORDER BY asset_name ASC
        ");

        $employees = DB::select("SELECT id, name_en, code FROM employees WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name_en ASC");
        $departments = DB::select("SELECT id, name_en FROM departments WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name_en ASC");
        $locations = DB::select("SELECT id, code FROM warehouse_locations WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY code ASC");

        return view('assets.assignments.create', compact('assets', 'employees', 'departments', 'locations', 'assetId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|integer',
            'employee_id' => 'nullable|integer',
            'department_id' => 'nullable|integer',
            'location_id' => 'nullable|integer',
            'assignment_date' => 'required|date',
            'expected_return_date' => 'nullable|date|after_or_equal:assignment_date',
            'note' => 'nullable|string',
        ]);

        $userId = session('user_id');

        // Business Rule Checks
        $asset = DB::select("SELECT * FROM assets WHERE id = ? AND deleted_at IS NULL LIMIT 1", [$request->asset_id]);
        if (empty($asset)) {
            return back()->withInput()->withErrors(['asset_id' => 'Asset not found.']);
        }
        $asset = $asset[0];

        if ($asset->status === 'DISPOSED') {
            return back()->withInput()->withErrors(['asset_id' => 'Cannot assign a disposed asset.']);
        }

        if (in_array($asset->status, ['ASSIGNED', 'IN_USE'])) {
            return back()->withInput()->withErrors(['asset_id' => 'Cannot assign an asset that is already assigned or in use. It must be returned first.']);
        }

        DB::transaction(function () use ($request, $asset, $userId) {
            $uuid = (string) Str::uuid();

            // 1. Insert Assignment
            DB::insert("
                INSERT INTO asset_assignments (
                    uuid, asset_id, employee_id, department_id, location_id,
                    assignment_date, expected_return_date, assigned_by, note, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ", [
                $uuid,
                $request->asset_id,
                $request->employee_id ?: null,
                $request->department_id ?: null,
                $request->location_id ?: null,
                $request->assignment_date,
                $request->expected_return_date ?: null,
                $userId,
                $request->note
            ]);

            $assignmentId = DB::getPdo()->lastInsertId();

            // 2. Update Asset Status and Current Holder
            DB::update("
                UPDATE assets
                SET status = 'ASSIGNED',
                    current_employee_id = ?,
                    current_department_id = ?,
                    location_id = ?,
                    updated_at = NOW()
                WHERE id = ?
            ", [
                $request->employee_id ?: null,
                $request->department_id ?: null,
                $request->location_id ?: null,
                $request->asset_id
            ]);

            // 3. Create Immutable Assignment History
            DB::insert("
                INSERT INTO asset_assignment_histories (
                    asset_id, previous_holder_id, new_holder_id, department_id,
                    assignment_date, action, user_id, note, created_at
                ) VALUES (?, ?, ?, ?, ?, 'ASSIGN', ?, ?, NOW())
            ", [
                $request->asset_id,
                $asset->current_employee_id ?: null,
                $request->employee_id ?: null,
                $request->department_id ?: null,
                $request->assignment_date,
                $userId,
                $request->note ?: 'Assigned to employee/department'
            ]);

            audit_log('ASSIGN', 'Asset', 'AssetAssignment', $assignmentId, [
                'status' => $asset->status
            ], [
                'status' => 'ASSIGNED',
                'employee_id' => $request->employee_id,
                'department_id' => $request->department_id
            ]);
        });

        return redirect()->route('asset-assignments.index')->with('success', 'Asset assigned successfully.');
    }
}
