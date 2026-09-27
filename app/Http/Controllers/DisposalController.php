<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DisposalController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $reason = $request->get('reason');

        $sql = "
            SELECT ad.*, a.asset_code, a.asset_name, u.name as approver_name
            FROM asset_disposals ad
            JOIN assets a ON ad.asset_id = a.id
            LEFT JOIN users u ON ad.approved_by = u.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (ad.disposal_number LIKE ? OR a.asset_code LIKE ? OR a.asset_name LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term, $term]);
        }

        if (!empty($reason)) {
            $sql .= " AND ad.reason = ?";
            $params[] = $reason;
        }

        $sql .= " ORDER BY ad.id DESC";

        $disposals = raw_paginate($sql, $params, 15);

        return view('assets.disposals.index', compact('disposals', 'search', 'reason'));
    }

    public function create(Request $request)
    {
        $nextNumber = generate_code('DSP', 'asset_disposals', 'disposal_number');
        $assetId = $request->get('asset_id');
        $assets = DB::select("SELECT id, asset_code, asset_name FROM assets WHERE status NOT IN ('DISPOSED', 'ASSIGNED', 'IN_USE') AND deleted_at IS NULL ORDER BY asset_name ASC");

        return view('assets.disposals.create', compact('nextNumber', 'assets', 'assetId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'disposal_number' => 'required|string|max:50|unique:asset_disposals,disposal_number',
            'asset_id' => 'required|integer',
            'reason' => 'required|in:DAMAGED,OLD,BEYOND_REPAIR,LOST,REPLACED,SOLD,DONATED,OTHER',
            'disposal_date' => 'required|date',
            'disposal_method' => 'nullable|string|max:100',
            'disposal_value' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $userId = session('user_id');

        $asset = DB::select("SELECT * FROM assets WHERE id = ? LIMIT 1", [$request->asset_id])[0];
        if (in_array($asset->status, ['ASSIGNED', 'IN_USE'])) {
            return back()->withInput()->withErrors(['asset_id' => 'Cannot dispose an asset that is currently assigned or in use.']);
        }

        DB::transaction(function () use ($request, $userId) {
            $uuid = (string) Str::uuid();

            DB::insert("
                INSERT INTO asset_disposals (
                    uuid, disposal_number, asset_id, reason, disposal_date,
                    approved_by, disposal_method, disposal_value, note, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ", [
                $uuid,
                strtoupper($request->disposal_number),
                $request->asset_id,
                $request->reason,
                $request->disposal_date,
                $userId,
                $request->disposal_method,
                $request->disposal_value ?: 0,
                $request->note
            ]);

            $disposalId = DB::getPdo()->lastInsertId();

            // Permanently mark asset as DISPOSED
            DB::update("
                UPDATE assets
                SET status = 'DISPOSED',
                    current_employee_id = NULL,
                    current_department_id = NULL,
                    updated_at = NOW()
                WHERE id = ?
            ", [$request->asset_id]);

            audit_log('DISPOSE', 'Asset', 'AssetDisposal', $disposalId, null, $request->all());
        });

        return redirect()->route('disposals.index')->with('success', 'Asset disposed successfully.');
    }
}
