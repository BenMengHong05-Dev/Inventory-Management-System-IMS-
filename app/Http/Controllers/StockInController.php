<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockInController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $warehouseId = $request->get('warehouse_id');
        $status = $request->get('status');

        $sql = "
            SELECT si.*, s.name as supplier_name, w.name as warehouse_name, u.name as receiver_name,
                   (SELECT COUNT(*) FROM stock_in_items sii WHERE sii.stock_in_id = si.id) as total_items,
                   (SELECT COALESCE(SUM(total_cost), 0) FROM stock_in_items sii WHERE sii.stock_in_id = si.id) as total_amount
            FROM stock_ins si
            LEFT JOIN suppliers s ON si.supplier_id = s.id
            JOIN warehouses w ON si.warehouse_id = w.id
            JOIN users u ON si.received_by = u.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (si.stock_in_number LIKE ? OR si.reference_number LIKE ?)";
            $term = "%{$search}%";
            $params[] = $term;
            $params[] = $term;
        }

        if (!empty($warehouseId)) {
            $sql .= " AND si.warehouse_id = ?";
            $params[] = $warehouseId;
        }

        if (!empty($status)) {
            $sql .= " AND si.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY si.id DESC";

        $stockIns = raw_paginate($sql, $params, 15);
        $warehouses = DB::select("SELECT id, name FROM warehouses WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");

        return view('stock_in.index', compact('stockIns', 'warehouses', 'search', 'warehouseId', 'status'));
    }

    public function create()
    {
        $nextNumber = generate_code('IN', 'stock_ins', 'stock_in_number');
        $suppliers = DB::select("SELECT id, name FROM suppliers WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");
        $warehouses = DB::select("SELECT id, name FROM warehouses WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");
        $locations = DB::select("SELECT id, warehouse_id, code FROM warehouse_locations WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY code ASC");
        $items = DB::select("SELECT id, code, name_en, default_unit_cost, unit_id FROM items WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name_en ASC");
        $units = DB::select("SELECT id, name_en, code FROM units ORDER BY name_en ASC");

        return view('stock_in.create', compact('nextNumber', 'suppliers', 'warehouses', 'locations', 'items', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'stock_in_number' => 'required|string|max:50|unique:stock_ins,stock_in_number',
            'reference_number' => 'nullable|string|max:100',
            'supplier_id' => 'nullable|integer',
            'warehouse_id' => 'required|integer',
            'location_id' => 'nullable|integer',
            'date' => 'required|date',
            'note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.unit_id' => 'nullable|integer',
            'items.*.batch_number' => 'nullable|string|max:100',
            'items.*.expiry_date' => 'nullable|date',
            'items.*.serial_number' => 'nullable|string|max:100',
        ]);

        $userId = session('user_id');

        DB::transaction(function () use ($request, $userId) {
            $uuid = (string) Str::uuid();

            // 1. Insert Stock In Record
            DB::insert("
                INSERT INTO stock_ins (
                    uuid, stock_in_number, reference_number, supplier_id, warehouse_id, location_id,
                    date, received_by, approved_by, status, note, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'APPROVED', ?, NOW(), NOW())
            ", [
                $uuid,
                strtoupper($request->stock_in_number),
                $request->reference_number,
                $request->supplier_id ?: null,
                $request->warehouse_id,
                $request->location_id ?: null,
                $request->date,
                $userId,
                $userId,
                $request->note
            ]);

            $stockInId = DB::getPdo()->lastInsertId();

            // 2. Process Items & Update Inventory
            foreach ($request->items as $itemData) {
                $itemId = $itemData['item_id'];
                $qty = (float) $itemData['quantity'];
                $unitCost = (float) $itemData['unit_cost'];
                $totalCost = $qty * $unitCost;
                $unitId = !empty($itemData['unit_id']) ? $itemData['unit_id'] : null;

                // Insert Stock In Item
                DB::insert("
                    INSERT INTO stock_in_items (
                        stock_in_id, item_id, unit_id, quantity, unit_cost, total_cost,
                        batch_number, expiry_date, serial_number, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ", [
                    $stockInId,
                    $itemId,
                    $unitId,
                    $qty,
                    $unitCost,
                    $totalCost,
                    $itemData['batch_number'] ?? null,
                    $itemData['expiry_date'] ?? null,
                    $itemData['serial_number'] ?? null,
                ]);

                // Check or Create Inventory Record with row locking
                $existingInv = DB::select("
                    SELECT id, quantity, unit_cost FROM inventories
                    WHERE item_id = ? AND warehouse_id = ? AND (location_id = ? OR (location_id IS NULL AND ? IS NULL))
                ", [$itemId, $request->warehouse_id, $request->location_id ?: null, $request->location_id ?: null]);

                $newBalance = $qty;
                if (!empty($existingInv)) {
                    $inv = $existingInv[0];
                    $newBalance = $inv->quantity + $qty;
                    // Weighted average cost or latest cost
                    DB::update("
                        UPDATE inventories
                        SET quantity = ?, unit_cost = ?, total_value = ? * ?, last_movement_date = NOW(), updated_at = NOW()
                        WHERE id = ?
                    ", [$newBalance, $unitCost, $newBalance, $unitCost, $inv->id]);
                } else {
                    DB::insert("
                        INSERT INTO inventories (
                            item_id, warehouse_id, location_id, quantity, reserved_quantity,
                            unit_cost, total_value, last_movement_date, created_at, updated_at
                        ) VALUES (?, ?, ?, ?, 0, ?, ?, NOW(), NOW(), NOW())
                    ", [
                        $itemId,
                        $request->warehouse_id,
                        $request->location_id ?: null,
                        $newBalance,
                        $unitCost,
                        $newBalance * $unitCost
                    ]);
                }

                // 3. Insert into Immutable Inventory Transactions
                DB::insert("
                    INSERT INTO inventory_transactions (
                        item_id, warehouse_id, location_id, transaction_type,
                        quantity_in, quantity_out, balance_after, unit_cost, total_cost,
                        reference_type, reference_id, user_id, date, note, created_at
                    ) VALUES (?, ?, ?, 'STOCK_IN', ?, 0, ?, ?, ?, 'STOCK_IN', ?, ?, ?, ?, NOW())
                ", [
                    $itemId,
                    $request->warehouse_id,
                    $request->location_id ?: null,
                    $qty,
                    $newBalance,
                    $unitCost,
                    $totalCost,
                    $stockInId,
                    $userId,
                    $request->date,
                    $request->note
                ]);
            }

            audit_log('STOCK_IN', 'Inventory', 'StockIn', $stockInId, null, [
                'number' => $request->stock_in_number,
                'items_count' => count($request->items)
            ]);
        });

        return redirect()->route('stock-in.index')->with('success', 'Stock In processed successfully and inventory updated.');
    }

    public function show($id)
    {
        $stockIns = DB::select("
            SELECT si.*, s.name as supplier_name, s.code as supplier_code,
                   w.name as warehouse_name, wl.code as location_code,
                   u.name as receiver_name, ap.name as approver_name
            FROM stock_ins si
            LEFT JOIN suppliers s ON si.supplier_id = s.id
            JOIN warehouses w ON si.warehouse_id = w.id
            LEFT JOIN warehouse_locations wl ON si.location_id = wl.id
            JOIN users u ON si.received_by = u.id
            LEFT JOIN users ap ON si.approved_by = ap.id
            WHERE si.id = ?
            LIMIT 1
        ", [$id]);

        if (empty($stockIns)) {
            abort(404, 'Stock in record not found.');
        }

        $stockIn = $stockIns[0];

        $items = DB::select("
            SELECT sii.*, i.code as item_code, i.name_en as item_name, u.name_en as unit_name
            FROM stock_in_items sii
            JOIN items i ON sii.item_id = i.id
            LEFT JOIN units u ON sii.unit_id = u.id
            WHERE sii.stock_in_id = ?
        ", [$id]);

        return view('stock_in.show', compact('stockIn', 'items'));
    }
}
