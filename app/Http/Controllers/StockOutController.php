<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockOutController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $warehouseId = $request->get('warehouse_id');
        $status = $request->get('status');

        $sql = "
            SELECT so.*, d.name_en as department_name, e.name_en as employee_name,
                   w.name as warehouse_name, u.name as issuer_name,
                   (SELECT COUNT(*) FROM stock_out_items soi WHERE soi.stock_out_id = so.id) as total_items,
                   (SELECT COALESCE(SUM(total_value), 0) FROM stock_out_items soi WHERE soi.stock_out_id = so.id) as total_value
            FROM stock_outs so
            LEFT JOIN departments d ON so.department_id = d.id
            LEFT JOIN employees e ON so.employee_id = e.id
            JOIN warehouses w ON so.warehouse_id = w.id
            JOIN users u ON so.issued_by = u.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (so.stock_out_number LIKE ? OR so.purpose LIKE ?)";
            $term = "%{$search}%";
            $params[] = $term;
            $params[] = $term;
        }

        if (!empty($warehouseId)) {
            $sql .= " AND so.warehouse_id = ?";
            $params[] = $warehouseId;
        }

        if (!empty($status)) {
            $sql .= " AND so.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY so.id DESC";

        $stockOuts = raw_paginate($sql, $params, 15);
        $warehouses = DB::select("SELECT id, name FROM warehouses WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");

        return view('stock_out.index', compact('stockOuts', 'warehouses', 'search', 'warehouseId', 'status'));
    }

    public function create()
    {
        $nextNumber = generate_code('OUT', 'stock_outs', 'stock_out_number');
        $departments = DB::select("SELECT id, name_en FROM departments WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name_en ASC");
        $employees = DB::select("SELECT id, name_en, code FROM employees WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name_en ASC");
        $warehouses = DB::select("SELECT id, name FROM warehouses WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");
        $items = DB::select("SELECT id, code, name_en, default_unit_cost, unit_id FROM items WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name_en ASC");
        $units = DB::select("SELECT id, name_en, code FROM units ORDER BY name_en ASC");

        return view('stock_out.create', compact('nextNumber', 'departments', 'employees', 'warehouses', 'items', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'stock_out_number' => 'required|string|max:50|unique:stock_outs,stock_out_number',
            'department_id' => 'nullable|integer',
            'employee_id' => 'nullable|integer',
            'warehouse_id' => 'required|integer',
            'date' => 'required|date',
            'purpose' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_cost' => 'nullable|numeric|min:0',
            'items.*.unit_id' => 'nullable|integer',
        ]);

        $userId = session('user_id');

        // Validate stock availability before proceeding
        foreach ($request->items as $idx => $itemData) {
            $itemId = $itemData['item_id'];
            $reqQty = (float) $itemData['quantity'];

            $inv = DB::select("
                SELECT id, quantity, reserved_quantity, (quantity - reserved_quantity) as available_qty
                FROM inventories
                WHERE item_id = ? AND warehouse_id = ?
            ", [$itemId, $request->warehouse_id]);

            $available = !empty($inv) ? (float)$inv[0]->available_qty : 0;
            if ($available < $reqQty) {
                $item = DB::select("SELECT name_en, code FROM items WHERE id = ? LIMIT 1", [$itemId])[0];
                return back()->withInput()->withErrors([
                    "items.{$idx}.quantity" => "Insufficient stock for item [{$item->code}] {$item->name_en}. Available: {$available}, Requested: {$reqQty}"
                ]);
            }
        }

        DB::transaction(function () use ($request, $userId) {
            $uuid = (string) Str::uuid();

            // 1. Insert Stock Out Record
            DB::insert("
                INSERT INTO stock_outs (
                    uuid, stock_out_number, department_id, employee_id, warehouse_id,
                    date, purpose, approved_by, issued_by, status, note, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'APPROVED', ?, NOW(), NOW())
            ", [
                $uuid,
                strtoupper($request->stock_out_number),
                $request->department_id ?: null,
                $request->employee_id ?: null,
                $request->warehouse_id,
                $request->date,
                $request->purpose,
                $userId,
                $userId,
                $request->note
            ]);

            $stockOutId = DB::getPdo()->lastInsertId();

            // 2. Process Items & Deduct Inventory
            foreach ($request->items as $itemData) {
                $itemId = $itemData['item_id'];
                $qty = (float) $itemData['quantity'];
                $unitId = !empty($itemData['unit_id']) ? $itemData['unit_id'] : null;

                // Fetch current inventory and cost
                $inv = DB::select("
                    SELECT id, quantity, unit_cost FROM inventories
                    WHERE item_id = ? AND warehouse_id = ?
                    FOR UPDATE
                ", [$itemId, $request->warehouse_id])[0];

                $unitCost = (float) ($itemData['unit_cost'] ?? $inv->unit_cost);
                $totalValue = $qty * $unitCost;

                DB::insert("
                    INSERT INTO stock_out_items (
                        stock_out_id, item_id, unit_id, quantity, unit_cost, total_value, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
                ", [
                    $stockOutId,
                    $itemId,
                    $unitId,
                    $qty,
                    $unitCost,
                    $totalValue
                ]);

                // Update inventory
                $newBalance = $inv->quantity - $qty;
                DB::update("
                    UPDATE inventories
                    SET quantity = ?, total_value = ? * unit_cost, last_movement_date = NOW(), updated_at = NOW()
                    WHERE id = ?
                ", [$newBalance, $newBalance, $inv->id]);

                // Insert into Immutable Inventory Transactions
                DB::insert("
                    INSERT INTO inventory_transactions (
                        item_id, warehouse_id, location_id, transaction_type,
                        quantity_in, quantity_out, balance_after, unit_cost, total_cost,
                        reference_type, reference_id, user_id, date, note, created_at
                    ) VALUES (?, ?, NULL, 'STOCK_OUT', 0, ?, ?, ?, ?, 'STOCK_OUT', ?, ?, ?, ?, NOW())
                ", [
                    $itemId,
                    $request->warehouse_id,
                    $qty,
                    $newBalance,
                    $unitCost,
                    $totalValue,
                    $stockOutId,
                    $userId,
                    $request->date,
                    $request->note
                ]);

                // Check for low stock notification trigger
                $itemInfo = DB::select("SELECT code, name_en, reorder_level FROM items WHERE id = ?", [$itemId])[0];
                if ($newBalance <= $itemInfo->reorder_level) {
                    create_notification(
                        null,
                        'LOW_STOCK',
                        'Low Stock Warning',
                        "Item [{$itemInfo->code}] {$itemInfo->name_en} is now at {$newBalance} units (Reorder level: {$itemInfo->reorder_level}).",
                        'Item',
                        $itemId
                    );
                }
            }

            audit_log('STOCK_OUT', 'Inventory', 'StockOut', $stockOutId, null, [
                'number' => $request->stock_out_number,
                'items_count' => count($request->items)
            ]);
        });

        return redirect()->route('stock-out.index')->with('success', 'Stock Out completed and inventory deducted.');
    }

    public function show($id)
    {
        $stockOuts = DB::select("
            SELECT so.*, d.name_en as department_name, e.name_en as employee_name,
                   w.name as warehouse_name, u.name as issuer_name, ap.name as approver_name
            FROM stock_outs so
            LEFT JOIN departments d ON so.department_id = d.id
            LEFT JOIN employees e ON so.employee_id = e.id
            JOIN warehouses w ON so.warehouse_id = w.id
            JOIN users u ON so.issued_by = u.id
            LEFT JOIN users ap ON so.approved_by = ap.id
            WHERE so.id = ?
            LIMIT 1
        ", [$id]);

        if (empty($stockOuts)) {
            abort(404, 'Stock out record not found.');
        }

        $stockOut = $stockOuts[0];

        $items = DB::select("
            SELECT soi.*, i.code as item_code, i.name_en as item_name, u.name_en as unit_name
            FROM stock_out_items soi
            JOIN items i ON soi.item_id = i.id
            LEFT JOIN units u ON soi.unit_id = u.id
            WHERE soi.stock_out_id = ?
        ", [$id]);

        return view('stock_out.show', compact('stockOut', 'items'));
    }
}
