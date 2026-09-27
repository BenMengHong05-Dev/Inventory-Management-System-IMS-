<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $supplierId = $request->get('supplier_id');
        $status = $request->get('status');

        $sql = "
            SELECT po.*, s.name as supplier_name, w.name as warehouse_name,
                   pr.request_number, ap.name as approver_name,
                   (SELECT COUNT(*) FROM purchase_order_items poi WHERE poi.purchase_order_id = po.id) as total_items
            FROM purchase_orders po
            JOIN suppliers s ON po.supplier_id = s.id
            JOIN warehouses w ON po.warehouse_id = w.id
            LEFT JOIN purchase_requests pr ON po.purchase_request_id = pr.id
            LEFT JOIN users ap ON po.approved_by = ap.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (po.po_number LIKE ? OR s.name LIKE ?)";
            $term = "%{$search}%";
            $params[] = $term;
            $params[] = $term;
        }

        if (!empty($supplierId)) {
            $sql .= " AND po.supplier_id = ?";
            $params[] = $supplierId;
        }

        if (!empty($status)) {
            $sql .= " AND po.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY po.id DESC";

        $purchaseOrders = raw_paginate($sql, $params, 15);
        $suppliers = DB::select("SELECT id, name FROM suppliers WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");

        return view('purchasing.orders.index', compact('purchaseOrders', 'suppliers', 'search', 'supplierId', 'status'));
    }

    public function create(Request $request)
    {
        $nextNumber = generate_code('PO', 'purchase_orders', 'po_number');
        $suppliers = DB::select("SELECT id, name FROM suppliers WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");
        $warehouses = DB::select("SELECT id, name FROM warehouses WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");
        $items = DB::select("SELECT id, code, name_en, default_unit_cost, unit_id FROM items WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name_en ASC");
        $units = DB::select("SELECT id, name_en, code FROM units ORDER BY name_en ASC");
        
        $prId = $request->get('pr_id');
        $prItems = [];
        $selectedPr = null;
        if ($prId) {
            $prData = DB::select("SELECT * FROM purchase_requests WHERE id = ? AND status = 'APPROVED' LIMIT 1", [$prId]);
            if (!empty($prData)) {
                $selectedPr = $prData[0];
                $prItems = DB::select("
                    SELECT pri.*, i.name_en, i.code as item_code
                    FROM purchase_request_items pri
                    JOIN items i ON pri.item_id = i.id
                    WHERE pri.purchase_request_id = ?
                ", [$prId]);
            }
        }

        return view('purchasing.orders.create', compact('nextNumber', 'suppliers', 'warehouses', 'items', 'units', 'selectedPr', 'prItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'po_number' => 'required|string|max:50|unique:purchase_orders,po_number',
            'supplier_id' => 'required|integer',
            'warehouse_id' => 'required|integer',
            'purchase_request_id' => 'nullable|integer',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'payment_terms' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.tax' => 'nullable|numeric|min:0',
        ]);

        $userId = session('user_id');

        DB::transaction(function () use ($request, $userId) {
            $uuid = (string) Str::uuid();

            $subtotal = 0;
            $taxAmount = 0;
            $discountAmount = 0;

            foreach ($request->items as $item) {
                $qty = (float)$item['quantity'];
                $price = (float)$item['unit_price'];
                $disc = (float)($item['discount'] ?? 0);
                $tax = (float)($item['tax'] ?? 0);

                $itemSubtotal = $qty * $price;
                $subtotal += $itemSubtotal;
                $discountAmount += $disc;
                $taxAmount += $tax;
            }

            $total = ($subtotal - $discountAmount) + $taxAmount;

            DB::insert("
                INSERT INTO purchase_orders (
                    uuid, po_number, supplier_id, purchase_request_id, warehouse_id,
                    order_date, expected_delivery_date, payment_terms, tax_amount,
                    discount_amount, subtotal, total, status, approved_by, notes, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'ORDERED', ?, ?, NOW(), NOW())
            ", [
                $uuid,
                strtoupper($request->po_number),
                $request->supplier_id,
                $request->purchase_request_id ?: null,
                $request->warehouse_id,
                $request->order_date,
                $request->expected_delivery_date ?: null,
                $request->payment_terms,
                $taxAmount,
                $discountAmount,
                $subtotal,
                $total,
                $userId,
                $request->notes
            ]);

            $poId = DB::getPdo()->lastInsertId();

            foreach ($request->items as $item) {
                $qty = (float)$item['quantity'];
                $price = (float)$item['unit_price'];
                $disc = (float)($item['discount'] ?? 0);
                $tax = (float)($item['tax'] ?? 0);
                $itemTotal = ($qty * $price - $disc) + $tax;

                DB::insert("
                    INSERT INTO purchase_order_items (
                        purchase_order_id, item_id, quantity, received_quantity,
                        unit_id, unit_price, discount, tax, total, created_at, updated_at
                    ) VALUES (?, ?, ?, 0, ?, ?, ?, ?, ?, NOW(), NOW())
                ", [
                    $poId,
                    $item['item_id'],
                    $qty,
                    $item['unit_id'] ?? null,
                    $price,
                    $disc,
                    $tax,
                    $itemTotal
                ]);
            }

            // If linked to a PR, mark PR as COMPLETED
            if ($request->purchase_request_id) {
                DB::update("UPDATE purchase_requests SET status = 'COMPLETED', updated_at = NOW() WHERE id = ?", [$request->purchase_request_id]);
            }

            record_status_history('PurchaseOrder', $poId, null, 'ORDERED', 'PO issued to supplier');
            audit_log('CREATE', 'Purchasing', 'PurchaseOrder', $poId, null, ['po_number' => $request->po_number]);
        });

        return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order created and issued.');
    }

    public function show($id)
    {
        $orders = DB::select("
            SELECT po.*, s.name as supplier_name, s.phone as supplier_phone, s.email as supplier_email,
                   s.address as supplier_address, s.tax_number as supplier_tax,
                   w.name as warehouse_name, pr.request_number, ap.name as approver_name
            FROM purchase_orders po
            JOIN suppliers s ON po.supplier_id = s.id
            JOIN warehouses w ON po.warehouse_id = w.id
            LEFT JOIN purchase_requests pr ON po.purchase_request_id = pr.id
            LEFT JOIN users ap ON po.approved_by = ap.id
            WHERE po.id = ?
            LIMIT 1
        ", [$id]);

        if (empty($orders)) {
            abort(404);
        }

        $order = $orders[0];

        $items = DB::select("
            SELECT poi.*, i.code as item_code, i.name_en as item_name, u.name_en as unit_name,
                   (poi.quantity - poi.received_quantity) as remaining_quantity
            FROM purchase_order_items poi
            JOIN items i ON poi.item_id = i.id
            LEFT JOIN units u ON poi.unit_id = u.id
            WHERE poi.purchase_order_id = ?
        ", [$id]);

        $receipts = DB::select("
            SELECT gr.*, u.name as receiver_name
            FROM goods_receipts gr
            JOIN users u ON gr.received_by = u.id
            WHERE gr.purchase_order_id = ?
            ORDER BY gr.id DESC
        ", [$id]);

        return view('purchasing.orders.show', compact('order', 'items', 'receipts'));
    }
}
