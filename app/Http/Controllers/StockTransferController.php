<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockTransferController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $sql = "
            SELECT st.*,
                   fw.name as from_warehouse_name, tw.name as to_warehouse_name,
                   fl.code as from_location_code, tl.code as to_location_code,
                   req.name as requester_name, rec.name as receiver_name,
                   (SELECT COUNT(*) FROM stock_transfer_items sti WHERE sti.stock_transfer_id = st.id) as total_items
            FROM stock_transfers st
            JOIN warehouses fw ON st.from_warehouse_id = fw.id
            JOIN warehouses tw ON st.to_warehouse_id = tw.id
            LEFT JOIN warehouse_locations fl ON st.from_location_id = fl.id
            LEFT JOIN warehouse_locations tl ON st.to_location_id = tl.id
            JOIN users req ON st.requested_by = req.id
            LEFT JOIN users rec ON st.received_by = rec.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND st.transfer_number LIKE ?";
            $params[] = "%{$search}%";
        }

        if (!empty($status)) {
            $sql .= " AND st.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY st.id DESC";

        $transfers = raw_paginate($sql, $params, 15);

        return view('stock_transfers.index', compact('transfers', 'search', 'status'));
    }

    public function create()
    {
        $nextNumber = generate_code('TRF', 'stock_transfers', 'transfer_number');
        $warehouses = DB::select("SELECT id, name FROM warehouses WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");
        $locations = DB::select("SELECT id, warehouse_id, code FROM warehouse_locations WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY code ASC");
        $items = DB::select("SELECT id, code, name_en, default_unit_cost, unit_id FROM items WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name_en ASC");

        return view('stock_transfers.create', compact('nextNumber', 'warehouses', 'locations', 'items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'transfer_number' => 'required|string|max:50|unique:stock_transfers,transfer_number',
            'from_warehouse_id' => 'required|integer|different:to_warehouse_id',
            'from_location_id' => 'nullable|integer',
            'to_warehouse_id' => 'required|integer',
            'to_location_id' => 'nullable|integer',
            'transfer_date' => 'required|date',
            'note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $userId = session('user_id');

        // Validate stock availability in source warehouse
        foreach ($request->items as $idx => $itemData) {
            $itemId = $itemData['item_id'];
            $qty = (float) $itemData['quantity'];

            $inv = DB::select("
                SELECT (quantity - reserved_quantity) as available_qty
                FROM inventories
                WHERE item_id = ? AND warehouse_id = ?
            ", [$itemId, $request->from_warehouse_id]);

            $available = !empty($inv) ? (float)$inv[0]->available_qty : 0;
            if ($available < $qty) {
                $item = DB::select("SELECT name_en, code FROM items WHERE id = ?", [$itemId])[0];
                return back()->withInput()->withErrors([
                    "items.{$idx}.quantity" => "Insufficient stock in source warehouse for [{$item->code}] {$item->name_en}. Available: {$available}, Requested: {$qty}"
                ]);
            }
        }

        DB::transaction(function () use ($request, $userId) {
            $uuid = (string) Str::uuid();

            DB::insert("
                INSERT INTO stock_transfers (
                    uuid, transfer_number, from_warehouse_id, from_location_id, to_warehouse_id,
                    to_location_id, transfer_date, requested_by, status, note, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'PENDING', ?, NOW(), NOW())
            ", [
                $uuid,
                strtoupper($request->transfer_number),
                $request->from_warehouse_id,
                $request->from_location_id ?: null,
                $request->to_warehouse_id,
                $request->to_location_id ?: null,
                $request->transfer_date,
                $userId,
                $request->note
            ]);

            $transferId = DB::getPdo()->lastInsertId();

            foreach ($request->items as $itemData) {
                $item = DB::select("SELECT default_unit_cost, unit_id FROM items WHERE id = ?", [$itemData['item_id']])[0];
                DB::insert("
                    INSERT INTO stock_transfer_items (stock_transfer_id, item_id, unit_id, quantity, unit_cost, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, NOW(), NOW())
                ", [
                    $transferId,
                    $itemData['item_id'],
                    $item->unit_id ?: null,
                    $itemData['quantity'],
                    $item->default_unit_cost
                ]);
            }

            audit_log('CREATE', 'StockTransfer', 'StockTransfer', $transferId, null, [
                'number' => $request->transfer_number,
                'status' => 'PENDING'
            ]);
        });

        return redirect()->route('stock-transfers.index')->with('success', 'Transfer request created successfully.');
    }

    public function show($id)
    {
        $transfers = DB::select("
            SELECT st.*,
                   fw.name as from_warehouse_name, tw.name as to_warehouse_name,
                   fl.code as from_location_code, tl.code as to_location_code,
                   req.name as requester_name, ap.name as approver_name, rec.name as receiver_name
            FROM stock_transfers st
            JOIN warehouses fw ON st.from_warehouse_id = fw.id
            JOIN warehouses tw ON st.to_warehouse_id = tw.id
            LEFT JOIN warehouse_locations fl ON st.from_location_id = fl.id
            LEFT JOIN warehouse_locations tl ON st.to_location_id = tl.id
            JOIN users req ON st.requested_by = req.id
            LEFT JOIN users ap ON st.approved_by = ap.id
            LEFT JOIN users rec ON st.received_by = rec.id
            WHERE st.id = ?
            LIMIT 1
        ", [$id]);

        if (empty($transfers)) {
            abort(404);
        }

        $transfer = $transfers[0];

        $items = DB::select("
            SELECT sti.*, i.code as item_code, i.name_en as item_name, u.name_en as unit_name
            FROM stock_transfer_items sti
            JOIN items i ON sti.item_id = i.id
            LEFT JOIN units u ON sti.unit_id = u.id
            WHERE sti.stock_transfer_id = ?
        ", [$id]);

        return view('stock_transfers.show', compact('transfer', 'items'));
    }

    /**
     * Dispatch transfer: status -> IN_TRANSIT, deduct source stock.
     */
    public function dispatch(Request $request, $id)
    {
        $userId = session('user_id');
        $transfers = DB::select("SELECT * FROM stock_transfers WHERE id = ? LIMIT 1", [$id]);
        if (empty($transfers) || !in_array($transfers[0]->status, ['PENDING', 'APPROVED'])) {
            return back()->with('error', 'Transfer cannot be dispatched in its current state.');
        }

        $transfer = $transfers[0];
        $items = DB::select("SELECT * FROM stock_transfer_items WHERE stock_transfer_id = ?", [$id]);

        DB::transaction(function () use ($transfer, $items, $userId, $id) {
            // Deduct source inventory
            foreach ($items as $item) {
                $inv = DB::select("
                    SELECT id, quantity, unit_cost FROM inventories
                    WHERE item_id = ? AND warehouse_id = ?
                    FOR UPDATE
                ", [$item->item_id, $transfer->from_warehouse_id])[0];

                $newBalance = $inv->quantity - $item->quantity;
                DB::update("
                    UPDATE inventories
                    SET quantity = ?, total_value = ? * unit_cost, last_movement_date = NOW(), updated_at = NOW()
                    WHERE id = ?
                ", [$newBalance, $newBalance, $inv->id]);

                // Create TRANSFER_OUT transaction
                DB::insert("
                    INSERT INTO inventory_transactions (
                        item_id, warehouse_id, location_id, transaction_type,
                        quantity_in, quantity_out, balance_after, unit_cost, total_cost,
                        reference_type, reference_id, user_id, date, note, created_at
                    ) VALUES (?, ?, ?, 'TRANSFER_OUT', 0, ?, ?, ?, ?, 'STOCK_TRANSFER', ?, ?, CURRENT_DATE, 'Dispatched for transfer', NOW())
                ", [
                    $item->item_id,
                    $transfer->from_warehouse_id,
                    $transfer->from_location_id,
                    $item->quantity,
                    $newBalance,
                    $inv->unit_cost,
                    $item->quantity * $inv->unit_cost,
                    $id,
                    $userId
                ]);
            }

            DB::update("
                UPDATE stock_transfers
                SET status = 'IN_TRANSIT', approved_by = ?, updated_at = NOW()
                WHERE id = ?
            ", [$userId, $id]);

            record_status_history('StockTransfer', $id, $transfer->status, 'IN_TRANSIT', 'Dispatched and in transit');
            audit_log('DISPATCH', 'StockTransfer', 'StockTransfer', $id);
        });

        return back()->with('success', 'Transfer dispatched. Stock deducted from source warehouse and now IN_TRANSIT.');
    }

    /**
     * Receive transfer: status -> COMPLETED, increment destination stock.
     */
    public function receive(Request $request, $id)
    {
        $userId = session('user_id');
        $transfers = DB::select("SELECT * FROM stock_transfers WHERE id = ? LIMIT 1", [$id]);
        if (empty($transfers) || $transfers[0]->status !== 'IN_TRANSIT') {
            return back()->with('error', 'Only in-transit transfers can be received.');
        }

        $transfer = $transfers[0];
        $items = DB::select("SELECT * FROM stock_transfer_items WHERE stock_transfer_id = ?", [$id]);

        DB::transaction(function () use ($transfer, $items, $userId, $id) {
            foreach ($items as $item) {
                // Add to destination inventory
                $destInv = DB::select("
                    SELECT id, quantity, unit_cost FROM inventories
                    WHERE item_id = ? AND warehouse_id = ? AND (location_id = ? OR (location_id IS NULL AND ? IS NULL))
                ", [$item->item_id, $transfer->to_warehouse_id, $transfer->to_location_id, $transfer->to_location_id]);

                $newBalance = $item->quantity;
                if (!empty($destInv)) {
                    $inv = $destInv[0];
                    $newBalance = $inv->quantity + $item->quantity;
                    DB::update("
                        UPDATE inventories
                        SET quantity = ?, total_value = ? * unit_cost, last_movement_date = NOW(), updated_at = NOW()
                        WHERE id = ?
                    ", [$newBalance, $newBalance, $inv->id]);
                } else {
                    DB::insert("
                        INSERT INTO inventories (
                            item_id, warehouse_id, location_id, quantity, reserved_quantity,
                            unit_cost, total_value, last_movement_date, created_at, updated_at
                        ) VALUES (?, ?, ?, ?, 0, ?, ?, NOW(), NOW(), NOW())
                    ", [
                        $item->item_id,
                        $transfer->to_warehouse_id,
                        $transfer->to_location_id,
                        $newBalance,
                        $item->unit_cost,
                        $newBalance * $item->unit_cost
                    ]);
                }

                // Create TRANSFER_IN transaction
                DB::insert("
                    INSERT INTO inventory_transactions (
                        item_id, warehouse_id, location_id, transaction_type,
                        quantity_in, quantity_out, balance_after, unit_cost, total_cost,
                        reference_type, reference_id, user_id, date, note, created_at
                    ) VALUES (?, ?, ?, 'TRANSFER_IN', ?, 0, ?, ?, ?, 'STOCK_TRANSFER', ?, ?, CURRENT_DATE, 'Received from transfer', NOW())
                ", [
                    $item->item_id,
                    $transfer->to_warehouse_id,
                    $transfer->to_location_id,
                    $item->quantity,
                    $newBalance,
                    $item->unit_cost,
                    $item->quantity * $item->unit_cost,
                    $id,
                    $userId
                ]);
            }

            DB::update("
                UPDATE stock_transfers
                SET status = 'COMPLETED', received_by = ?, updated_at = NOW()
                WHERE id = ?
            ", [$userId, $id]);

            record_status_history('StockTransfer', $id, 'IN_TRANSIT', 'COMPLETED', 'Received and completed');
            audit_log('RECEIVE', 'StockTransfer', 'StockTransfer', $id);
        });

        return back()->with('success', 'Transfer received successfully and destination inventory updated.');
    }
}
