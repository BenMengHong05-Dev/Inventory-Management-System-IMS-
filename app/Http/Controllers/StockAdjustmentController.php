<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $warehouseId = $request->get('warehouse_id');
        $type = $request->get('type');

        $sql = "
            SELECT sa.*, w.name as warehouse_name, wl.code as location_code,
                   i.code as item_code, i.name_en as item_name, i.name_km as item_name_km, 
                   u.name as creator_name, ap.name as approver_name
            FROM stock_adjustments sa
            JOIN warehouses w ON sa.warehouse_id = w.id
            LEFT JOIN warehouse_locations wl ON sa.location_id = wl.id
            JOIN items i ON sa.item_id = i.id
            JOIN users u ON sa.created_by = u.id
            LEFT JOIN users ap ON sa.approved_by = ap.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (sa.adjustment_number LIKE ? OR i.code LIKE ? OR i.name_en LIKE ? OR i.name_km LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term, $term, $term]);
        }

        if (!empty($warehouseId)) {
            $sql .= " AND sa.warehouse_id = ?";
            $params[] = $warehouseId;
        }

        if (!empty($type)) {
            $sql .= " AND sa.adjustment_type = ?";
            $params[] = $type;
        }

        $sql .= " ORDER BY sa.id DESC";

        $adjustments = raw_paginate($sql, $params, 15);
        $warehouses = DB::select("SELECT id, name FROM warehouses WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");

        return view('stock_adjustments.index', compact('adjustments', 'warehouses', 'search', 'warehouseId', 'type'));
    }

    public function create()
    {
        $nextNumber = generate_code('ADJ', 'stock_adjustments', 'adjustment_number');
        $warehouses = DB::select("SELECT id, name FROM warehouses WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");
        $locations = DB::select("SELECT id, warehouse_id, code FROM warehouse_locations WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY code ASC");
        $items = DB::select("SELECT id, code, name_en, name_km, default_unit_cost FROM items WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name_en ASC");

        return view('stock_adjustments.create', compact('nextNumber', 'warehouses', 'locations', 'items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'adjustment_number' => 'required|string|max:50|unique:stock_adjustments,adjustment_number',
            'warehouse_id' => 'required|integer',
            'location_id' => 'nullable|integer',
            'item_id' => 'required|integer',
            'actual_quantity' => 'required|numeric|min:0',
            'reason' => 'required|string|max:255',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        $userId = session('user_id');

        // Fetch current system quantity
        $inv = DB::select("
            SELECT id, quantity, unit_cost FROM inventories
            WHERE item_id = ? AND warehouse_id = ? AND (location_id = ? OR (location_id IS NULL AND ? IS NULL))
        ", [$request->item_id, $request->warehouse_id, $request->location_id ?: null, $request->location_id ?: null]);

        $systemQty = !empty($inv) ? (float)$inv[0]->quantity : 0.00;
        $actualQty = (float)$request->actual_quantity;
        $diff = $actualQty - $systemQty;

        if ($diff == 0) {
            return back()->withInput()->withErrors(['actual_quantity' => 'Actual quantity is identical to system quantity. No adjustment needed.']);
        }

        $adjustmentType = $diff > 0 ? 'INCREASE' : 'DECREASE';

        DB::transaction(function () use ($request, $systemQty, $actualQty, $diff, $adjustmentType, $inv, $userId) {
            $uuid = (string) Str::uuid();

            // 1. Insert Adjustment Record
            DB::insert("
                INSERT INTO stock_adjustments (
                    uuid, adjustment_number, warehouse_id, location_id, item_id,
                    adjustment_type, system_quantity, actual_quantity, difference,
                    reason, approved_by, created_by, date, status, note, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'APPROVED', ?, NOW(), NOW())
            ", [
                $uuid,
                strtoupper($request->adjustment_number),
                $request->warehouse_id,
                $request->location_id ?: null,
                $request->item_id,
                $adjustmentType,
                $systemQty,
                $actualQty,
                abs($diff),
                $request->reason,
                $userId,
                $userId,
                $request->date,
                $request->note
            ]);

            $adjId = DB::getPdo()->lastInsertId();

            // 2. Update Inventory
            $unitCost = !empty($inv) ? (float)$inv[0]->unit_cost : 0.00;
            if (empty($inv)) {
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
                    $actualQty,
                    $unitCost,
                    $actualQty * $unitCost
                ]);
            } else {
                DB::update("
                    UPDATE inventories
                    SET quantity = ?, total_value = ? * unit_cost, last_movement_date = NOW(), updated_at = NOW()
                    WHERE id = ?
                ", [$actualQty, $actualQty, $inv[0]->id]);
            }

            // 3. Create Transaction Record
            $txType = $diff > 0 ? 'ADJUSTMENT_IN' : 'ADJUSTMENT_OUT';
            $qtyIn = $diff > 0 ? $diff : 0;
            $qtyOut = $diff < 0 ? abs($diff) : 0;

            DB::insert("
                INSERT INTO inventory_transactions (
                    item_id, warehouse_id, location_id, transaction_type,
                    quantity_in, quantity_out, balance_after, unit_cost, total_cost,
                    reference_type, reference_id, user_id, date, note, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'STOCK_ADJUSTMENT', ?, ?, ?, ?, NOW())
            ", [
                $request->item_id,
                $request->warehouse_id,
                $request->location_id ?: null,
                $txType,
                $qtyIn,
                $qtyOut,
                $actualQty,
                $unitCost,
                abs($diff) * $unitCost,
                $adjId,
                $userId,
                $request->date,
                $request->reason
            ]);

            audit_log('ADJUST', 'Inventory', 'StockAdjustment', $adjId, [
                'system_qty' => $systemQty
            ], [
                'actual_qty' => $actualQty,
                'difference' => $diff,
                'reason' => $request->reason
            ]);
        });

        return redirect()->route('stock-adjustments.index')->with('success', 'Stock adjustment applied and inventory updated.');
    }
}
