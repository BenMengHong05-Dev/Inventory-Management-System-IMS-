<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $sql = "
            SELECT w.*, u.name as manager_name,
                   (SELECT COUNT(*) FROM warehouse_locations wl WHERE wl.warehouse_id = w.id AND wl.deleted_at IS NULL) as location_count,
                   (SELECT COALESCE(SUM(quantity), 0) FROM inventories inv WHERE inv.warehouse_id = w.id) as total_stock,
                   (SELECT COALESCE(SUM(total_value), 0) FROM inventories inv WHERE inv.warehouse_id = w.id) as total_value
            FROM warehouses w
            LEFT JOIN users u ON w.manager_id = u.id
            WHERE w.deleted_at IS NULL
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (w.code LIKE ? OR w.name LIKE ? OR w.phone LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term, $term]);
        }

        if (!empty($status)) {
            $sql .= " AND w.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY w.id DESC";
        $warehouses = raw_paginate($sql, $params, 15);
        $managers = DB::select("SELECT id, name FROM users WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");

        return view('master.warehouses.index', compact('warehouses', 'managers', 'search', 'status'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'manager_id' => 'nullable|integer',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|in:ACTIVE,INACTIVE',
        ]);

        $existing = DB::select("SELECT id FROM warehouses WHERE code = ? AND deleted_at IS NULL LIMIT 1", [$request->code]);
        if (!empty($existing)) {
            return back()->withInput()->withErrors(['code' => 'A warehouse with this code already exists.']);
        }

        $uuid = (string) Str::uuid();

        DB::insert("
            INSERT INTO warehouses (uuid, code, name, manager_id, phone, address, description, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ", [
            $uuid,
            strtoupper($request->code),
            $request->name,
            $request->manager_id ?: null,
            $request->phone,
            $request->address,
            $request->description,
            $request->status
        ]);

        $warehouseId = DB::getPdo()->lastInsertId();
        audit_log('CREATE', 'Warehouse', 'Warehouse', $warehouseId, null, $request->all());

        return redirect()->route('warehouses.index')->with('success', 'Warehouse created successfully.');
    }

    public function show($id)
    {
        $warehouses = DB::select("
            SELECT w.*, u.name as manager_name
            FROM warehouses w
            LEFT JOIN users u ON w.manager_id = u.id
            WHERE w.id = ? AND w.deleted_at IS NULL
            LIMIT 1
        ", [$id]);

        if (empty($warehouses)) {
            abort(404, 'Warehouse not found.');
        }

        $warehouse = $warehouses[0];

        // Locations in this warehouse
        $locations = DB::select("
            SELECT wl.*,
                   (SELECT COALESCE(SUM(quantity), 0) FROM inventories inv WHERE inv.location_id = wl.id) as total_qty
            FROM warehouse_locations wl
            WHERE wl.warehouse_id = ? AND wl.deleted_at IS NULL
            ORDER BY wl.code ASC
        ", [$id]);

        // Current Inventory in this warehouse
        $inventory = DB::select("
            SELECT inv.*, i.code as item_code, i.name_en as item_name, u.name_en as unit_name, wl.code as location_code
            FROM inventories inv
            JOIN items i ON inv.item_id = i.id
            LEFT JOIN units u ON i.unit_id = u.id
            LEFT JOIN warehouse_locations wl ON inv.location_id = wl.id
            WHERE inv.warehouse_id = ?
            ORDER BY i.name_en ASC
        ", [$id]);

        return view('master.warehouses.show', compact('warehouse', 'locations', 'inventory'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'manager_id' => 'nullable|integer',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|in:ACTIVE,INACTIVE',
        ]);

        $existing = DB::select("SELECT id FROM warehouses WHERE code = ? AND id != ? AND deleted_at IS NULL LIMIT 1", [$request->code, $id]);
        if (!empty($existing)) {
            return back()->withInput()->withErrors(['code' => 'A warehouse with this code already exists.']);
        }

        $old = DB::select("SELECT * FROM warehouses WHERE id = ? LIMIT 1", [$id])[0];

        DB::update("
            UPDATE warehouses
            SET code = ?, name = ?, manager_id = ?, phone = ?, address = ?, description = ?, status = ?, updated_at = NOW()
            WHERE id = ?
        ", [
            strtoupper($request->code),
            $request->name,
            $request->manager_id ?: null,
            $request->phone,
            $request->address,
            $request->description,
            $request->status,
            $id
        ]);

        audit_log('UPDATE', 'Warehouse', 'Warehouse', $id, $old, $request->all());

        return redirect()->route('warehouses.index')->with('success', 'Warehouse updated successfully.');
    }

    public function destroy($id)
    {
        // Prevent deletion if stock exists
        $stockCount = DB::select("SELECT COALESCE(SUM(quantity), 0) as total FROM inventories WHERE warehouse_id = ?", [$id])[0]->total;
        if ($stockCount > 0) {
            return back()->with('error', "Cannot delete warehouse: It currently contains {$stockCount} units of stock.");
        }

        $old = DB::select("SELECT * FROM warehouses WHERE id = ? LIMIT 1", [$id])[0];
        DB::update("UPDATE warehouses SET deleted_at = NOW() WHERE id = ?", [$id]);
        audit_log('DELETE', 'Warehouse', 'Warehouse', $id, $old, null);

        return redirect()->route('warehouses.index')->with('success', 'Warehouse deleted successfully.');
    }
}
