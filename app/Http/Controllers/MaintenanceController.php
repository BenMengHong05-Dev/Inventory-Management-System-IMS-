<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $sql = "
            SELECT m.*, a.asset_code, a.asset_name, u.name as requester_name
            FROM maintenances m
            JOIN assets a ON m.asset_id = a.id
            JOIN users u ON m.requester_id = u.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (m.maintenance_number LIKE ? OR a.asset_code LIKE ? OR a.asset_name LIKE ? OR m.problem LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term, $term, $term]);
        }

        if (!empty($status)) {
            $sql .= " AND m.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY m.id DESC";

        $maintenances = raw_paginate($sql, $params, 15);

        return view('assets.maintenance.index', compact('maintenances', 'search', 'status'));
    }

    public function create(Request $request)
    {
        $nextNumber = generate_code('MNT', 'maintenances', 'maintenance_number');
        $assetId = $request->get('asset_id');
        $assets = DB::select("SELECT id, asset_code, asset_name FROM assets WHERE status != 'DISPOSED' AND deleted_at IS NULL ORDER BY asset_name ASC");

        return view('assets.maintenance.create', compact('nextNumber', 'assets', 'assetId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'maintenance_number' => 'required|string|max:50|unique:maintenances,maintenance_number',
            'asset_id' => 'required|integer',
            'problem' => 'required|string|max:255',
            'description' => 'nullable|string',
            'request_date' => 'required|date',
            'technician' => 'nullable|string|max:100',
            'service_provider' => 'nullable|string|max:100',
            'warranty_coverage' => 'nullable|boolean',
        ]);

        $userId = session('user_id');

        DB::transaction(function () use ($request, $userId) {
            $uuid = (string) Str::uuid();

            DB::insert("
                INSERT INTO maintenances (
                    uuid, maintenance_number, asset_id, requester_id, problem,
                    description, request_date, technician, service_provider,
                    warranty_coverage, status, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'PENDING', NOW(), NOW())
            ", [
                $uuid,
                strtoupper($request->maintenance_number),
                $request->asset_id,
                $userId,
                $request->problem,
                $request->description,
                $request->request_date,
                $request->technician,
                $request->service_provider,
                $request->boolean('warranty_coverage') ? 1 : 0
            ]);

            $maintenanceId = DB::getPdo()->lastInsertId();

            // Update asset status to MAINTENANCE
            DB::update("UPDATE assets SET status = 'MAINTENANCE', updated_at = NOW() WHERE id = ?", [$request->asset_id]);

            audit_log('CREATE', 'Maintenance', 'Maintenance', $maintenanceId, null, $request->all());
        });

        return redirect()->route('maintenance.index')->with('success', 'Maintenance request logged.');
    }

    public function show($id)
    {
        $maintenances = DB::select("
            SELECT m.*, a.asset_code, a.asset_name, a.serial_number, a.condition as asset_condition,
                   u.name as requester_name
            FROM maintenances m
            JOIN assets a ON m.asset_id = a.id
            JOIN users u ON m.requester_id = u.id
            WHERE m.id = ?
            LIMIT 1
        ", [$id]);

        if (empty($maintenances)) {
            abort(404);
        }

        $maintenance = $maintenances[0];

        return view('assets.maintenance.show', compact('maintenance'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:PENDING,APPROVED,IN_PROGRESS,COMPLETED,CANCELLED',
            'start_date' => 'nullable|date',
            'completion_date' => 'nullable|date',
            'cost' => 'nullable|numeric|min:0',
            'technician' => 'nullable|string|max:100',
            'service_provider' => 'nullable|string|max:100',
            'diagnosis' => 'nullable|string',
            'solution' => 'nullable|string',
            'parts_used' => 'nullable|string',
        ]);

        $old = DB::select("SELECT * FROM maintenances WHERE id = ? LIMIT 1", [$id])[0];

        DB::transaction(function () use ($request, $old, $id) {
            DB::update("
                UPDATE maintenances
                SET status = ?, start_date = ?, completion_date = ?, cost = ?,
                    technician = ?, service_provider = ?, diagnosis = ?,
                    solution = ?, parts_used = ?, updated_at = NOW()
                WHERE id = ?
            ", [
                $request->status,
                $request->start_date ?: null,
                $request->completion_date ?: null,
                $request->cost ?: 0,
                $request->technician,
                $request->service_provider,
                $request->diagnosis,
                $request->solution,
                $request->parts_used,
                $id
            ]);

            // If COMPLETED, return asset status to IN_STOCK or GOOD condition
            if ($request->status === 'COMPLETED') {
                DB::update("
                    UPDATE assets
                    SET status = 'IN_STOCK', condition = 'GOOD', updated_at = NOW()
                    WHERE id = ?
                ", [$old->asset_id]);
            }

            audit_log('UPDATE', 'Maintenance', 'Maintenance', $id, $old, $request->all());
        });

        return redirect()->route('maintenance.show', $id)->with('success', 'Maintenance record updated.');
    }
}
