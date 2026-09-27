<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ItemReturnController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $sql = "
            SELECT ir.*, i.code as item_code, i.name_en as item_name,
                   w.name as warehouse_name, wl.code as location_code,
                   e.name_en as returned_by_name, u.name as receiver_name
            FROM item_returns ir
            JOIN items i ON ir.item_id = i.id
            JOIN warehouses w ON ir.warehouse_id = w.id
            LEFT JOIN warehouse_locations wl ON ir.location_id = wl.id
            LEFT JOIN employees e ON ir.returned_by = e.id
            JOIN users u ON ir.received_by = u.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (ir.return_number LIKE ? OR i.code LIKE ? OR i.name_en LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term, $term]);
        }

        $sql .= " ORDER BY ir.id DESC";

        $returns = raw_paginate($sql, $params, 15);

        return view('inventory.returns.index', compact('returns', 'search'));
    }

    public function create()
    {
        $nextNumber = generate_code('RET', 'item_returns', 'return_number');
        $items = DB::select("SELECT id, code, name_en, default_unit_cost FROM items WHERE item_type IN ('CONSUMABLE', 'NON_CONSUMABLE') AND status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name_en ASC");
        $warehouses = DB::select("SELECT id, name FROM warehouses WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");
        $locations = DB::select("SELECT id, warehouse_id, code FROM warehouse_locations WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY code ASC");
        $employees = DB::select("SELECT id, name_en, code FROM employees WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name_en ASC");
        $stockOuts = DB::select("SELECT id, stock_out_number FROM stock_outs ORDER BY id DESC LIMIT 50");

        return view('inventory.returns.create', compact('nextNumber', 'items', 'warehouses', 'locations', 'employees', 'stockOuts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'return_number' => 'required|string|max:50|unique:item_returns,return_number',
            'stock_out_id' => 'nullable|integer',
            'item_id' => 'required|integer',
            'returned_quantity' => 'required|numeric|min:0.01',
            'condition' => 'required|string|max:50',
            'warehouse_id' => 'required|integer',
            'location_id' => 'nullable|integer',
            'returned_by' => 'nullable|integer',
            'return_date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        $userId = session('user_id');

        DB::transaction(function () use ($request, $userId) {
            $uuid = (string) Str::uuid();

            // 1. Insert Item Return
            DB::insert("
                INSERT INTO item_returns (
                    uuid, return_number, stock_out_id, item_id, returned_quantity,
                    condition, warehouse_id, location_id, returned_by, received_by,
                    return_date, note, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ", [
                $uuid,
                strtoupper($request->return_number),
                $request->stock_out_id ?: null,
                $request->item_id,
                $request->returned_quantity,
                $request->condition,
                $request->warehouse_id,
                $request->location_id ?: null,
                $request->returned_by ?: null,
                $userId,
                $request->return_date,
                $request->note
            ]);

            $returnId = DB::getPdo()->lastInsertId();

            // 2. Increase Inventory
            $inv = DB::select("
                SELECT id, quantity, unit_cost FROM inventories
                WHERE item_id = ? AND warehouse_id = ? AND (location_id = ? OR (location_id IS NULL AND ? IS NULL))
            ", [$request->item_id, $request->warehouse_id, $request->location_id ?: null, $request->location_id ?: null]);

            $unitCost = 0;
            $newBalance = $request->returned_quantity;

            if (!empty($inv)) {
                $newBalance = $inv[0]->quantity + $request->returned_quantity;
                $unitCost = (float)$inv[0]->unit_cost;
                DB::update("
                    UPDATE inventories
                    SET quantity = ?, total_value = ? * unit_cost, last_movement_date = NOW(), updated_at = NOW()
                    WHERE id = ?
                ", [$newBalance, $newBalance, $inv[0]->id]);
            } else {
                $item = DB::select("SELECT default_unit_cost FROM items WHERE id = ?", [$request->item_id])[0];
                $unitCost = (float)$item->default_unit_cost;
                DB::insert("
                    INSERT INTO inventories (
                        item_id, warehouse_id, location_id, quantity, reserved_quantity,
                        unit_cost, total_value, last_movement_date, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, 0, ?, ?, NOW(), NOW(), NOW())
                ", [
                    $request->item_id,
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
                ) VALUES (?, ?, ?, 'RETURN_IN', ?, 0, ?, ?, ?, 'ITEM_RETURN', ?, ?, ?, ?, NOW())
            ", [
                $request->item_id,
                $request->warehouse_id,
                $request->location_id ?: null,
                $request->returned_quantity,
                $newBalance,
                $unitCost,
                $request->returned_quantity * $unitCost,
                $returnId,
                $userId,
                $request->return_date,
                $request->note ?: 'Item returned to stock'
            ]);

            audit_log('RETURN_ITEM', 'Inventory', 'ItemReturn', $returnId, null, $request->all());
        });

        return redirect()->route('item-returns.index')->with('success', 'Returned item received and inventory restocked.');
    }
}
