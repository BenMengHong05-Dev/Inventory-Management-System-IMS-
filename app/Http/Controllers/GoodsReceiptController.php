<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GoodsReceiptController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $supplierId = $request->get('supplier_id');

        $sql = "
            SELECT gr.*, po.po_number, s.name as supplier_name, w.name as warehouse_name, u.name as receiver_name,
                   (SELECT COUNT(*) FROM goods_receipt_items gri WHERE gri.goods_receipt_id = gr.id) as total_items,
                   (SELECT COALESCE(SUM(total_cost), 0) FROM goods_receipt_items gri WHERE gri.goods_receipt_id = gr.id) as total_received_value
            FROM goods_receipts gr
            JOIN purchase_orders po ON gr.purchase_order_id = po.id
            JOIN suppliers s ON gr.supplier_id = s.id
            JOIN warehouses w ON gr.warehouse_id = w.id
            JOIN users u ON gr.received_by = u.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (gr.receipt_number LIKE ? OR po.po_number LIKE ?)";
            $term = "%{$search}%";
            $params[] = $term;
            $params[] = $term;
        }

        if (!empty($supplierId)) {
            $sql .= " AND gr.supplier_id = ?";
            $params[] = $supplierId;
        }

        $sql .= " ORDER BY gr.id DESC";

        $receipts = raw_paginate($sql, $params, 15);
        $suppliers = DB::select("SELECT id, name FROM suppliers WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");

        return view('purchasing.receipts.index', compact('receipts', 'suppliers', 'search', 'supplierId'));
    }

    public function create(Request $request)
    {
        $poId = $request->get('po_id');
        $orders = DB::select("
            SELECT id, po_number, supplier_id, warehouse_id
            FROM purchase_orders
            WHERE status IN ('APPROVED', 'ORDERED', 'PARTIALLY_RECEIVED')
            ORDER BY id DESC
        ");

        $selectedPo = null;
        $poItems = [];
        $locations = [];

        if ($poId) {
            $poData = DB::select("
                SELECT po.*, s.name as supplier_name, w.name as warehouse_name
                FROM purchase_orders po
                JOIN suppliers s ON po.supplier_id = s.id
                JOIN warehouses w ON po.warehouse_id = w.id
                WHERE po.id = ? AND po.status IN ('APPROVED', 'ORDERED', 'PARTIALLY_RECEIVED')
                LIMIT 1
            ", [$poId]);

            if (!empty($poData)) {
                $selectedPo = $poData[0];
                $poItems = DB::select("
                    SELECT poi.*, i.code as item_code, i.name_en as item_name, u.name_en as unit_name,
                           (poi.quantity - poi.received_quantity) as outstanding_quantity
                    FROM purchase_order_items poi
                    JOIN items i ON poi.item_id = i.id
                    LEFT JOIN units u ON poi.unit_id = u.id
                    WHERE poi.purchase_order_id = ? AND (poi.quantity - poi.received_quantity) > 0
                ", [$poId]);

                $locations = DB::select("SELECT id, code FROM warehouse_locations WHERE warehouse_id = ? AND status = 'ACTIVE' AND deleted_at IS NULL ORDER BY code ASC", [$selectedPo->warehouse_id]);
            }
        }

        $nextNumber = generate_code('GRN', 'goods_receipts', 'receipt_number');

        return view('purchasing.receipts.create', compact('nextNumber', 'orders', 'selectedPo', 'poItems', 'locations', 'poId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receipt_number' => 'required|string|max:50|unique:goods_receipts,receipt_number',
            'purchase_order_id' => 'required|integer',
            'receipt_date' => 'required|date',
            'location_id' => 'nullable|integer',
            'note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.po_item_id' => 'required|integer',
            'items.*.quantity_received' => 'required|numeric|min:0',
            'items.*.batch_number' => 'nullable|string|max:100',
            'items.*.expiry_date' => 'nullable|date',
            'items.*.serial_number' => 'nullable|string|max:100',
        ]);

        $userId = session('user_id');

        $po = DB::select("SELECT * FROM purchase_orders WHERE id = ? LIMIT 1", [$request->purchase_order_id])[0];

        // Validate partial receiving rules: Do not allow receiving more than outstanding PO quantity
        $hasAtLeastOneItem = false;
        foreach ($request->items as $idx => $itemInput) {
            $receivedQty = (float)$itemInput['quantity_received'];
            if ($receivedQty <= 0) {
                continue;
            }
            $hasAtLeastOneItem = true;

            $poItem = DB::select("
                SELECT poi.*, i.name_en, i.code as item_code
                FROM purchase_order_items poi
                JOIN items i ON poi.item_id = i.id
                WHERE poi.id = ? AND poi.purchase_order_id = ?
            ", [$itemInput['po_item_id'], $request->purchase_order_id])[0];

            $outstanding = (float)($poItem->quantity - $poItem->received_quantity);
            if ($receivedQty > $outstanding) {
                return back()->withInput()->withErrors([
                    "items.{$idx}.quantity_received" => "Cannot receive {$receivedQty} units for [{$poItem->item_code}] {$poItem->name_en}. Maximum outstanding is {$outstanding}."
                ]);
            }
        }

        if (!$hasAtLeastOneItem) {
            return back()->withInput()->withErrors(['items' => 'Please enter a received quantity greater than 0 for at least one item.']);
        }

        DB::transaction(function () use ($request, $po, $userId) {
            $uuid = (string) Str::uuid();

            // 1. Create Goods Receipt
            DB::insert("
                INSERT INTO goods_receipts (
                    uuid, receipt_number, purchase_order_id, supplier_id, warehouse_id,
                    location_id, receipt_date, received_by, status, note, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'RECEIVED', ?, NOW(), NOW())
            ", [
                $uuid,
                strtoupper($request->receipt_number),
                $po->id,
                $po->supplier_id,
                $po->warehouse_id,
                $request->location_id ?: null,
                $request->receipt_date,
                $userId,
                $request->note
            ]);

            $receiptId = DB::getPdo()->lastInsertId();

            // 2. Process Items, Increment Inventory, Create Transactions
            foreach ($request->items as $itemInput) {
                $receivedQty = (float)$itemInput['quantity_received'];
                if ($receivedQty <= 0) {
                    continue;
                }

                $poItem = DB::select("SELECT * FROM purchase_order_items WHERE id = ?", [$itemInput['po_item_id']])[0];
                $itemId = $poItem->item_id;
                $unitCost = (float)$poItem->unit_price;
                $totalCost = $receivedQty * $unitCost;

                DB::insert("
                    INSERT INTO goods_receipt_items (
                        goods_receipt_id, purchase_order_item_id, item_id, quantity_received,
                        unit_cost, total_cost, batch_number, expiry_date, serial_number, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ", [
                    $receiptId,
                    $poItem->id,
                    $itemId,
                    $receivedQty,
                    $unitCost,
                    $totalCost,
                    $itemInput['batch_number'] ?? null,
                    $itemInput['expiry_date'] ?? null,
                    $itemInput['serial_number'] ?? null
                ]);

                // Update PO Item received quantity
                DB::update("
                    UPDATE purchase_order_items
                    SET received_quantity = received_quantity + ?, updated_at = NOW()
                    WHERE id = ?
                ", [$receivedQty, $poItem->id]);

                // Update or Insert Inventory
                $existingInv = DB::select("
                    SELECT id, quantity, unit_cost FROM inventories
                    WHERE item_id = ? AND warehouse_id = ? AND (location_id = ? OR (location_id IS NULL AND ? IS NULL))
                ", [$itemId, $po->warehouse_id, $request->location_id ?: null, $request->location_id ?: null]);

                $newBalance = $receivedQty;
                if (!empty($existingInv)) {
                    $inv = $existingInv[0];
                    $newBalance = $inv->quantity + $receivedQty;
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
                        $po->warehouse_id,
                        $request->location_id ?: null,
                        $newBalance,
                        $unitCost,
                        $newBalance * $unitCost
                    ]);
                }

                // Inventory Transaction
                DB::insert("
                    INSERT INTO inventory_transactions (
                        item_id, warehouse_id, location_id, transaction_type,
                        quantity_in, quantity_out, balance_after, unit_cost, total_cost,
                        reference_type, reference_id, user_id, date, note, created_at
                    ) VALUES (?, ?, ?, 'STOCK_IN', ?, 0, ?, ?, ?, 'GOODS_RECEIPT', ?, ?, ?, ?, NOW())
                ", [
                    $itemId,
                    $po->warehouse_id,
                    $request->location_id ?: null,
                    $receivedQty,
                    $newBalance,
                    $unitCost,
                    $totalCost,
                    $receiptId,
                    $userId,
                    $request->receipt_date,
                    "Goods received against PO: {$po->po_number}"
                ]);
            }

            // 3. Determine New PO Status (PARTIALLY_RECEIVED or RECEIVED)
            $remainingPoItems = DB::select("
                SELECT COUNT(*) as cnt FROM purchase_order_items
                WHERE purchase_order_id = ? AND (quantity - received_quantity) > 0
            ", [$po->id])[0]->cnt;

            $newPoStatus = ($remainingPoItems > 0) ? 'PARTIALLY_RECEIVED' : 'RECEIVED';
            DB::update("UPDATE purchase_orders SET status = ?, updated_at = NOW() WHERE id = ?", [$newPoStatus, $po->id]);
            record_status_history('PurchaseOrder', $po->id, $po->status, $newPoStatus, "Goods received via {$request->receipt_number}");

            audit_log('RECEIVE', 'Purchasing', 'GoodsReceipt', $receiptId, null, [
                'receipt_number' => $request->receipt_number,
                'po_status' => $newPoStatus
            ]);
        });

        return redirect()->route('goods-receipts.index')->with('success', 'Goods received, inventory updated, and PO status adjusted.');
    }

    public function show($id)
    {
        $receipts = DB::select("
            SELECT gr.*, po.po_number, s.name as supplier_name,
                   w.name as warehouse_name, wl.code as location_code, u.name as receiver_name
            FROM goods_receipts gr
            JOIN purchase_orders po ON gr.purchase_order_id = po.id
            JOIN suppliers s ON gr.supplier_id = s.id
            JOIN warehouses w ON gr.warehouse_id = w.id
            LEFT JOIN warehouse_locations wl ON gr.location_id = wl.id
            JOIN users u ON gr.received_by = u.id
            WHERE gr.id = ?
            LIMIT 1
        ", [$id]);

        if (empty($receipts)) {
            abort(404);
        }

        $receipt = $receipts[0];

        $items = DB::select("
            SELECT gri.*, i.code as item_code, i.name_en as item_name
            FROM goods_receipt_items gri
            JOIN items i ON gri.item_id = i.id
            WHERE gri.goods_receipt_id = ?
        ", [$id]);

        return view('purchasing.receipts.show', compact('receipt', 'items'));
    }
}
