<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $categoryId = $request->get('category_id');
        $brandId = $request->get('brand_id');
        $itemType = $request->get('item_type');
        $status = $request->get('status');

        $sql = "
            SELECT i.*, c.name_en as category_name, c.name_km as category_name_km, 
                   b.name as brand_name, u.name_en as unit_name, u.name_km as unit_name_km,
                   COALESCE((SELECT SUM(quantity) FROM inventories inv WHERE inv.item_id = i.id), 0) as total_stock,
                   COALESCE((SELECT SUM(reserved_quantity) FROM inventories inv WHERE inv.item_id = i.id), 0) as reserved_stock,
                   COALESCE((SELECT SUM(total_value) FROM inventories inv WHERE inv.item_id = i.id), 0) as total_value
            FROM items i
            LEFT JOIN categories c ON i.category_id = c.id
            LEFT JOIN brands b ON i.brand_id = b.id
            LEFT JOIN units u ON i.unit_id = u.id
            WHERE i.deleted_at IS NULL
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (i.code LIKE ? OR i.barcode LIKE ? OR i.name_en LIKE ? OR i.name_km LIKE ? OR i.model LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term, $term, $term, $term]);
        }

        if (!empty($categoryId)) {
            $sql .= " AND (i.category_id = ? OR i.sub_category_id = ?)";
            $params[] = $categoryId;
            $params[] = $categoryId;
        }

        if (!empty($brandId)) {
            $sql .= " AND i.brand_id = ?";
            $params[] = $brandId;
        }

        if (!empty($itemType)) {
            $sql .= " AND i.item_type = ?";
            $params[] = $itemType;
        }

        if (!empty($status)) {
            $sql .= " AND i.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY i.id DESC";

        $items = raw_paginate($sql, $params, 15);

        // Filter master options
        $categories = DB::select("SELECT id, name_en, name_km FROM categories WHERE parent_id IS NULL AND status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name_en ASC");
        $brands = DB::select("SELECT id, name FROM brands WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");

        return view('items.index', compact('items', 'categories', 'brands', 'search', 'categoryId', 'brandId', 'itemType', 'status'));
    }

    public function create()
    {
        $nextCode = generate_code('ITM', 'items');
        $categories = DB::select("SELECT id, name_en, name_km FROM categories WHERE parent_id IS NULL AND status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name_en ASC");
        $subCategories = DB::select("SELECT id, name_en, name_km, parent_id FROM categories WHERE parent_id IS NOT NULL AND status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name_en ASC");
        $brands = DB::select("SELECT id, name FROM brands WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");
        $units = DB::select("SELECT id, name_en, code FROM units ORDER BY name_en ASC");

        return view('items.create', compact('nextCode', 'categories', 'subCategories', 'brands', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
            'barcode' => 'nullable|string|max:100',
            'qr_code' => 'nullable|string|max:100',
            'name_en' => 'required|string|max:255',
            'name_km' => 'required|string|max:255',
            'category_id' => 'nullable|integer',
            'sub_category_id' => 'nullable|integer',
            'brand_id' => 'nullable|integer',
            'model' => 'nullable|string|max:100',
            'unit_id' => 'nullable|integer',
            'item_type' => 'required|in:CONSUMABLE,NON_CONSUMABLE,ASSET',
            'description' => 'nullable|string',
            'min_stock' => 'required|numeric|min:0',
            'max_stock' => 'required|numeric|min:0',
            'reorder_level' => 'required|numeric|min:0',
            'default_unit_cost' => 'required|numeric|min:0',
            'status' => 'required|in:ACTIVE,INACTIVE',
            'image' => 'nullable|image|max:2048',
        ]);

        // Validate code uniqueness via raw SQL
        $existing = DB::select("SELECT id FROM items WHERE code = ? AND deleted_at IS NULL LIMIT 1", [$request->code]);
        if (!empty($existing)) {
            return back()->withInput()->withErrors(['code' => 'An item with this code already exists.']);
        }

        if ($request->barcode) {
            $existingBarcode = DB::select("SELECT id FROM items WHERE barcode = ? AND deleted_at IS NULL LIMIT 1", [$request->barcode]);
            if (!empty($existingBarcode)) {
                return back()->withInput()->withErrors(['barcode' => 'An item with this barcode already exists.']);
            }
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('items', 'public');
        }

        $uuid = (string) Str::uuid();
        $barcode = $request->barcode ?: strtoupper($request->code);
        $qrCode = $request->qr_code ?: $uuid;

        DB::insert("
            INSERT INTO items (
                uuid, code, barcode, qr_code, name_en, name_km, category_id, sub_category_id,
                brand_id, model, unit_id, item_type, description, image, min_stock, max_stock,
                reorder_level, default_unit_cost, status, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ", [
            $uuid,
            strtoupper($request->code),
            $barcode,
            $qrCode,
            $request->name_en,
            $request->name_km,
            $request->category_id ?: null,
            $request->sub_category_id ?: null,
            $request->brand_id ?: null,
            $request->model,
            $request->unit_id ?: null,
            $request->item_type,
            $request->description,
            $imagePath,
            $request->min_stock,
            $request->max_stock,
            $request->reorder_level,
            $request->default_unit_cost,
            $request->status
        ]);

        $itemId = DB::getPdo()->lastInsertId();
        audit_log('CREATE', 'Item', 'Item', $itemId, null, $request->all());

        return redirect()->route('items.index')->with('success', 'Item created successfully.');
    }

    public function show($id)
    {
        $items = DB::select("
            SELECT i.*, c.name_en as category_name, sc.name_en as sub_category_name,
                   b.name as brand_name, u.name_en as unit_name, u.code as unit_code
            FROM items i
            LEFT JOIN categories c ON i.category_id = c.id
            LEFT JOIN categories sc ON i.sub_category_id = sc.id
            LEFT JOIN brands b ON i.brand_id = b.id
            LEFT JOIN units u ON i.unit_id = u.id
            WHERE i.id = ? AND i.deleted_at IS NULL
            LIMIT 1
        ", [$id]);

        if (empty($items)) {
            abort(404, 'Item not found.');
        }

        $item = $items[0];

        // 1. Current Warehouse / Location Quantities
        $inventories = DB::select("
            SELECT inv.*, w.name as warehouse_name, wl.code as location_code,
                   (inv.quantity - inv.reserved_quantity) as available_quantity
            FROM inventories inv
            JOIN warehouses w ON inv.warehouse_id = w.id
            LEFT JOIN warehouse_locations wl ON inv.location_id = wl.id
            WHERE inv.item_id = ?
            ORDER BY w.name ASC
        ", [$id]);

        // 2. Stock Movement History (Centralized transactions)
        $transactions = DB::select("
            SELECT it.*, w.name as warehouse_name, wl.code as location_code, u.name as user_name
            FROM inventory_transactions it
            JOIN warehouses w ON it.warehouse_id = w.id
            LEFT JOIN warehouse_locations wl ON it.location_id = wl.id
            LEFT JOIN users u ON it.user_id = u.id
            WHERE it.item_id = ?
            ORDER BY it.id DESC
            LIMIT 25
        ", [$id]);

        // 3. Purchase History
        $purchases = DB::select("
            SELECT poi.*, po.po_number, po.order_date, po.status as po_status, s.name as supplier_name
            FROM purchase_order_items poi
            JOIN purchase_orders po ON poi.purchase_order_id = po.id
            JOIN suppliers s ON po.supplier_id = s.id
            WHERE poi.item_id = ?
            ORDER BY po.order_date DESC
            LIMIT 15
        ", [$id]);

        // 4. Supplier History
        $suppliers = DB::select("
            SELECT DISTINCT s.id, s.code, s.name, s.phone, s.email
            FROM purchase_order_items poi
            JOIN purchase_orders po ON poi.purchase_order_id = po.id
            JOIN suppliers s ON po.supplier_id = s.id
            WHERE poi.item_id = ?
        ", [$id]);

        return view('items.show', compact('item', 'inventories', 'transactions', 'purchases', 'suppliers'));
    }

    public function edit($id)
    {
        $items = DB::select("SELECT * FROM items WHERE id = ? AND deleted_at IS NULL LIMIT 1", [$id]);
        if (empty($items)) {
            abort(404);
        }
        $item = $items[0];

        $categories = DB::select("SELECT id, name_en FROM categories WHERE parent_id IS NULL AND status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name_en ASC");
        $subCategories = DB::select("SELECT id, name_en, parent_id FROM categories WHERE parent_id IS NOT NULL AND status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name_en ASC");
        $brands = DB::select("SELECT id, name FROM brands WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");
        $units = DB::select("SELECT id, name_en, code FROM units ORDER BY name_en ASC");

        return view('items.edit', compact('item', 'categories', 'subCategories', 'brands', 'units'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|string|max:50',
            'barcode' => 'nullable|string|max:100',
            'qr_code' => 'nullable|string|max:100',
            'name_en' => 'required|string|max:255',
            'name_km' => 'required|string|max:255',
            'category_id' => 'nullable|integer',
            'sub_category_id' => 'nullable|integer',
            'brand_id' => 'nullable|integer',
            'model' => 'nullable|string|max:100',
            'unit_id' => 'nullable|integer',
            'item_type' => 'required|in:CONSUMABLE,NON_CONSUMABLE,ASSET',
            'description' => 'nullable|string',
            'min_stock' => 'required|numeric|min:0',
            'max_stock' => 'required|numeric|min:0',
            'reorder_level' => 'required|numeric|min:0',
            'default_unit_cost' => 'required|numeric|min:0',
            'status' => 'required|in:ACTIVE,INACTIVE',
            'image' => 'nullable|image|max:2048',
        ]);

        $existing = DB::select("SELECT id FROM items WHERE code = ? AND id != ? AND deleted_at IS NULL LIMIT 1", [$request->code, $id]);
        if (!empty($existing)) {
            return back()->withInput()->withErrors(['code' => 'An item with this code already exists.']);
        }

        $old = DB::select("SELECT * FROM items WHERE id = ? LIMIT 1", [$id])[0];
        $imagePath = $old->image;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('items', 'public');
        }

        DB::update("
            UPDATE items
            SET code = ?, barcode = ?, qr_code = ?, name_en = ?, name_km = ?, category_id = ?, sub_category_id = ?,
                brand_id = ?, model = ?, unit_id = ?, item_type = ?, description = ?, image = ?, min_stock = ?,
                max_stock = ?, reorder_level = ?, default_unit_cost = ?, status = ?, updated_at = NOW()
            WHERE id = ?
        ", [
            strtoupper($request->code),
            $request->barcode,
            $request->qr_code,
            $request->name_en,
            $request->name_km,
            $request->category_id ?: null,
            $request->sub_category_id ?: null,
            $request->brand_id ?: null,
            $request->model,
            $request->unit_id ?: null,
            $request->item_type,
            $request->description,
            $imagePath,
            $request->min_stock,
            $request->max_stock,
            $request->reorder_level,
            $request->default_unit_cost,
            $request->status,
            $id
        ]);

        audit_log('UPDATE', 'Item', 'Item', $id, $old, $request->all());

        return redirect()->route('items.index')->with('success', 'Item updated successfully.');
    }

    public function destroy($id)
    {
        // Business rule: Do not delete records that have transaction history
        $txCount = DB::select("SELECT COUNT(*) as cnt FROM inventory_transactions WHERE item_id = ?", [$id])[0]->cnt;
        if ($txCount > 0) {
            return back()->with('error', "Cannot delete item: It has {$txCount} inventory transaction(s) recorded.");
        }

        $old = DB::select("SELECT * FROM items WHERE id = ? LIMIT 1", [$id])[0];
        DB::update("UPDATE items SET deleted_at = NOW() WHERE id = ?", [$id]);
        audit_log('DELETE', 'Item', 'Item', $id, $old, null);

        return redirect()->route('items.index')->with('success', 'Item deleted successfully.');
    }
}
