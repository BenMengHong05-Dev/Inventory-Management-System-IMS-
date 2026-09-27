<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $warehouseId = $request->get('warehouse_id');
        $search = $request->get('search');

        $sql = "
            SELECT wl.*, w.name as warehouse_name,
                   (SELECT COALESCE(SUM(quantity), 0) FROM inventories inv WHERE inv.location_id = wl.id) as current_stock
            FROM warehouse_locations wl
            JOIN warehouses w ON wl.warehouse_id = w.id
            WHERE wl.deleted_at IS NULL
        ";
        $params = [];

        if (!empty($warehouseId)) {
            $sql .= " AND wl.warehouse_id = ?";
            $params[] = $warehouseId;
        }

        if (!empty($search)) {
            $sql .= " AND (wl.code LIKE ? OR wl.building LIKE ? OR wl.rack LIKE ? OR wl.shelf LIKE ? OR wl.bin LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term, $term, $term, $term]);
        }

        $sql .= " ORDER BY wl.id DESC";
        $locations = raw_paginate($sql, $params, 15);
        $warehouses = DB::select("SELECT id, name, code FROM warehouses WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");

        return view('master.locations.index', compact('locations', 'warehouses', 'warehouseId', 'search'));
    }

    public function store(Request $request)
    {
        if ($request->filled('status')) {
            $request->merge(['status' => strtoupper($request->status)]);
        } else {
            $request->merge(['status' => 'ACTIVE']);
        }
        if (!$request->filled('building') && $request->filled('aisle')) {
            $request->merge(['building' => $request->aisle]);
        }

        $request->validate([
            'warehouse_id' => 'required|integer',
            'building' => 'nullable|string|max:50',
            'floor' => 'nullable|string|max:50',
            'room' => 'nullable|string|max:50',
            'rack' => 'nullable|string|max:50',
            'shelf' => 'nullable|string|max:50',
            'bin' => 'nullable|string|max:50',
            'code' => 'required|string|max:50',
            'description' => 'nullable|string',
            'status' => 'required|in:ACTIVE,INACTIVE',
        ]);

        $existing = DB::select("SELECT id FROM warehouse_locations WHERE code = ? AND deleted_at IS NULL LIMIT 1", [strtoupper($request->code)]);
        if (!empty($existing)) {
            return back()->withInput()->withErrors(['code' => 'A location with this code already exists.']);
        }

        DB::insert("
            INSERT INTO warehouse_locations (warehouse_id, building, floor, room, rack, shelf, bin, code, description, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ", [
            $request->warehouse_id,
            $request->building,
            $request->floor,
            $request->room,
            $request->rack,
            $request->shelf,
            $request->bin,
            strtoupper($request->code),
            $request->description,
            $request->status
        ]);

        $newId = DB::getPdo()->lastInsertId();
        audit_log('CREATE', 'Location', 'WarehouseLocation', $newId, null, $request->all());

        return redirect()->route('locations.index')->with('success', 'Location created successfully.');
    }

    public function update(Request $request, $id)
    {
        if ($request->filled('status')) {
            $request->merge(['status' => strtoupper($request->status)]);
        } else {
            $request->merge(['status' => 'ACTIVE']);
        }
        if (!$request->filled('building') && $request->filled('aisle')) {
            $request->merge(['building' => $request->aisle]);
        }

        $request->validate([
            'warehouse_id' => 'required|integer',
            'building' => 'nullable|string|max:50',
            'floor' => 'nullable|string|max:50',
            'room' => 'nullable|string|max:50',
            'rack' => 'nullable|string|max:50',
            'shelf' => 'nullable|string|max:50',
            'bin' => 'nullable|string|max:50',
            'code' => 'required|string|max:50',
            'description' => 'nullable|string',
            'status' => 'required|in:ACTIVE,INACTIVE',
        ]);

        $existing = DB::select("SELECT id FROM warehouse_locations WHERE code = ? AND id != ? AND deleted_at IS NULL LIMIT 1", [$request->code, $id]);
        if (!empty($existing)) {
            return back()->withInput()->withErrors(['code' => 'A location with this code already exists.']);
        }

        $old = DB::select("SELECT * FROM warehouse_locations WHERE id = ? LIMIT 1", [$id])[0];

        DB::update("
            UPDATE warehouse_locations
            SET warehouse_id = ?, building = ?, floor = ?, room = ?, rack = ?, shelf = ?, bin = ?, code = ?, description = ?, status = ?, updated_at = NOW()
            WHERE id = ?
        ", [
            $request->warehouse_id,
            $request->building,
            $request->floor,
            $request->room,
            $request->rack,
            $request->shelf,
            $request->bin,
            strtoupper($request->code),
            $request->description,
            $request->status,
            $id
        ]);

        audit_log('UPDATE', 'Location', 'WarehouseLocation', $id, $old, $request->all());

        return redirect()->route('locations.index')->with('success', 'Location updated successfully.');
    }

    public function destroy($id)
    {
        $stockCount = DB::select("SELECT COALESCE(SUM(quantity), 0) as total FROM inventories WHERE location_id = ?", [$id])[0]->total;
        if ($stockCount > 0) {
            return back()->with('error', "Cannot delete location: It currently holds {$stockCount} items.");
        }

        $old = DB::select("SELECT * FROM warehouse_locations WHERE id = ? LIMIT 1", [$id])[0];
        DB::update("UPDATE warehouse_locations SET deleted_at = NOW() WHERE id = ?", [$id]);
        audit_log('DELETE', 'Location', 'WarehouseLocation', $id, $old, null);

        return redirect()->route('locations.index')->with('success', 'Location deleted successfully.');
    }
}
