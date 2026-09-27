<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $categoryId = $request->get('category_id');
        $status = $request->get('status');
        $condition = $request->get('condition');
        $departmentId = $request->get('department_id');

        $sql = "
            SELECT a.*, c.name_en as category_name, c.name_km as category_name_km, b.name as brand_name,
                   d.name_en as department_name, d.name_km as department_name_km,
                   e.name_en as employee_name, e.name_km as employee_name_km,
                   w.name as warehouse_name, wl.code as location_code
            FROM assets a
            LEFT JOIN categories c ON a.category_id = c.id
            LEFT JOIN brands b ON a.brand_id = b.id
            LEFT JOIN departments d ON a.current_department_id = d.id
            LEFT JOIN employees e ON a.current_employee_id = e.id
            LEFT JOIN warehouses w ON a.warehouse_id = w.id
            LEFT JOIN warehouse_locations wl ON a.location_id = wl.id
            WHERE a.deleted_at IS NULL
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (a.asset_code LIKE ? OR a.asset_name LIKE ? OR a.serial_number LIKE ? OR a.barcode LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term, $term, $term]);
        }

        if (!empty($categoryId)) {
            $sql .= " AND a.category_id = ?";
            $params[] = $categoryId;
        }

        if (!empty($status)) {
            $sql .= " AND a.status = ?";
            $params[] = $status;
        }

        if (!empty($condition)) {
            $sql .= " AND a.condition = ?";
            $params[] = $condition;
        }

        if (!empty($departmentId)) {
            $sql .= " AND a.current_department_id = ?";
            $params[] = $departmentId;
        }

        $sql .= " ORDER BY a.id DESC";

        $assets = raw_paginate($sql, $params, 15);

        $categories = DB::select("SELECT id, name_en, name_km FROM categories WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name_en ASC");
        $departments = DB::select("SELECT id, name_en, name_km FROM departments WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name_en ASC");

        return view('assets.index', compact('assets', 'categories', 'departments', 'search', 'categoryId', 'status', 'condition', 'departmentId'));
    }

    public function create()
    {
        $nextCode = generate_code('AST', 'assets', 'asset_code');
        $items = DB::select("SELECT id, code, name_en, category_id, brand_id, model FROM items WHERE item_type = 'ASSET' AND status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name_en ASC");
        $categories = DB::select("SELECT id, name_en FROM categories WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name_en ASC");
        $brands = DB::select("SELECT id, name FROM brands WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");
        $warehouses = DB::select("SELECT id, name FROM warehouses WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");
        $locations = DB::select("SELECT id, warehouse_id, code FROM warehouse_locations WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY code ASC");
        $suppliers = DB::select("SELECT id, name FROM suppliers WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");

        return view('assets.create', compact('nextCode', 'items', 'categories', 'brands', 'warehouses', 'locations', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_code' => 'required|string|max:50|unique:assets,asset_code',
            'asset_name' => 'required|string|max:255',
            'item_id' => 'nullable|integer',
            'category_id' => 'nullable|integer',
            'brand_id' => 'nullable|integer',
            'model' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100|unique:assets,serial_number',
            'barcode' => 'nullable|string|max:100|unique:assets,barcode',
            'supplier_id' => 'nullable|integer',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'required|numeric|min:0',
            'warranty_start' => 'nullable|date',
            'warranty_expiry' => 'nullable|date|after_or_equal:warranty_start',
            'warehouse_id' => 'nullable|integer',
            'location_id' => 'nullable|integer',
            'condition' => 'required|in:NEW,GOOD,FAIR,DAMAGED,CRITICAL',
            'image' => 'nullable|image|max:2048',
            'notes' => 'nullable|string',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('assets', 'public');
        }

        $uuid = (string) Str::uuid();
        $barcode = $request->barcode ?: strtoupper($request->asset_code);
        $qrCode = $request->asset_code;

        DB::insert("
            INSERT INTO assets (
                uuid, asset_code, asset_name, item_id, category_id, brand_id, model,
                serial_number, barcode, qr_code, supplier_id, purchase_date, purchase_cost,
                warranty_start, warranty_expiry, warehouse_id, location_id, condition,
                status, image, notes, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'IN_STOCK', ?, ?, NOW(), NOW())
        ", [
            $uuid,
            strtoupper($request->asset_code),
            $request->asset_name,
            $request->item_id ?: null,
            $request->category_id ?: null,
            $request->brand_id ?: null,
            $request->model,
            $request->serial_number ?: null,
            $barcode,
            $qrCode,
            $request->supplier_id ?: null,
            $request->purchase_date ?: null,
            $request->purchase_cost,
            $request->warranty_start ?: null,
            $request->warranty_expiry ?: null,
            $request->warehouse_id ?: null,
            $request->location_id ?: null,
            $request->condition,
            $imagePath,
            $request->notes
        ]);

        $assetId = DB::getPdo()->lastInsertId();
        audit_log('CREATE', 'Asset', 'Asset', $assetId, null, $request->all());

        return redirect()->route('assets.index')->with('success', 'Asset created successfully.');
    }

    public function show($id)
    {
        $assets = DB::select("
            SELECT a.*, c.name_en as category_name, b.name as brand_name,
                   s.name as supplier_name, w.name as warehouse_name, wl.code as location_code,
                   d.name_en as department_name, e.name_en as employee_name, e.code as employee_code
            FROM assets a
            LEFT JOIN categories c ON a.category_id = c.id
            LEFT JOIN brands b ON a.brand_id = b.id
            LEFT JOIN suppliers s ON a.supplier_id = s.id
            LEFT JOIN warehouses w ON a.warehouse_id = w.id
            LEFT JOIN warehouse_locations wl ON a.location_id = wl.id
            LEFT JOIN departments d ON a.current_department_id = d.id
            LEFT JOIN employees e ON a.current_employee_id = e.id
            WHERE a.id = ? AND a.deleted_at IS NULL
            LIMIT 1
        ", [$id]);

        if (empty($assets)) {
            abort(404);
        }

        $asset = $assets[0];

        // Assignment History (Never overwritten)
        $assignmentHistories = DB::select("
            SELECT aah.*, u.name as user_name,
                   prev_e.name_en as prev_holder_name,
                   new_e.name_en as new_holder_name,
                   d.name_en as department_name
            FROM asset_assignment_histories aah
            LEFT JOIN users u ON aah.user_id = u.id
            LEFT JOIN employees prev_e ON aah.previous_holder_id = prev_e.id
            LEFT JOIN employees new_e ON aah.new_holder_id = new_e.id
            LEFT JOIN departments d ON aah.department_id = d.id
            WHERE aah.asset_id = ?
            ORDER BY aah.id DESC
        ", [$id]);

        // Maintenance History
        $maintenances = DB::select("
            SELECT m.*, u.name as requester_name
            FROM maintenances m
            JOIN users u ON m.requester_id = u.id
            WHERE m.asset_id = ?
            ORDER BY m.id DESC
        ", [$id]);

        return view('assets.show', compact('asset', 'assignmentHistories', 'maintenances'));
    }

    public function edit($id)
    {
        $assets = DB::select("SELECT * FROM assets WHERE id = ? AND deleted_at IS NULL LIMIT 1", [$id]);
        if (empty($assets)) {
            abort(404);
        }
        $asset = $assets[0];

        $categories = DB::select("SELECT id, name_en FROM categories WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name_en ASC");
        $brands = DB::select("SELECT id, name FROM brands WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");
        $warehouses = DB::select("SELECT id, name FROM warehouses WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");
        $locations = DB::select("SELECT id, warehouse_id, code FROM warehouse_locations WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY code ASC");
        $suppliers = DB::select("SELECT id, name FROM suppliers WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");

        return view('assets.edit', compact('asset', 'categories', 'brands', 'warehouses', 'locations', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'asset_code' => 'required|string|max:50',
            'asset_name' => 'required|string|max:255',
            'category_id' => 'nullable|integer',
            'brand_id' => 'nullable|integer',
            'model' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:100',
            'supplier_id' => 'nullable|integer',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'required|numeric|min:0',
            'warranty_start' => 'nullable|date',
            'warranty_expiry' => 'nullable|date',
            'warehouse_id' => 'nullable|integer',
            'location_id' => 'nullable|integer',
            'condition' => 'required|in:NEW,GOOD,FAIR,DAMAGED,CRITICAL',
            'status' => 'required|in:IN_STOCK,ASSIGNED,IN_USE,MAINTENANCE,LOST,DAMAGED,DISPOSED,RETURNED',
            'image' => 'nullable|image|max:2048',
            'notes' => 'nullable|string',
        ]);

        $existing = DB::select("SELECT id FROM assets WHERE (asset_code = ? OR (serial_number = ? AND serial_number IS NOT NULL)) AND id != ? AND deleted_at IS NULL LIMIT 1", [
            $request->asset_code, $request->serial_number, $id
        ]);
        if (!empty($existing)) {
            return back()->withInput()->withErrors(['asset_code' => 'An asset with this code or serial number already exists.']);
        }

        $old = DB::select("SELECT * FROM assets WHERE id = ? LIMIT 1", [$id])[0];
        $imagePath = $old->image;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('assets', 'public');
        }

        DB::update("
            UPDATE assets
            SET asset_code = ?, asset_name = ?, category_id = ?, brand_id = ?, model = ?,
                serial_number = ?, barcode = ?, supplier_id = ?, purchase_date = ?, purchase_cost = ?,
                warranty_start = ?, warranty_expiry = ?, warehouse_id = ?, location_id = ?,
                condition = ?, status = ?, image = ?, notes = ?, updated_at = NOW()
            WHERE id = ?
        ", [
            strtoupper($request->asset_code),
            $request->asset_name,
            $request->category_id ?: null,
            $request->brand_id ?: null,
            $request->model,
            $request->serial_number ?: null,
            $request->barcode ?: strtoupper($request->asset_code),
            $request->supplier_id ?: null,
            $request->purchase_date ?: null,
            $request->purchase_cost,
            $request->warranty_start ?: null,
            $request->warranty_expiry ?: null,
            $request->warehouse_id ?: null,
            $request->location_id ?: null,
            $request->condition,
            $request->status,
            $imagePath,
            $request->notes,
            $id
        ]);

        audit_log('UPDATE', 'Asset', 'Asset', $id, $old, $request->all());

        return redirect()->route('assets.index')->with('success', 'Asset updated successfully.');
    }

    public function destroy($id)
    {
        $old = DB::select("SELECT * FROM assets WHERE id = ? LIMIT 1", [$id])[0];
        if (in_array($old->status, ['ASSIGNED', 'IN_USE'])) {
            return back()->with('error', 'Cannot delete asset while it is assigned or in use.');
        }

        DB::update("UPDATE assets SET deleted_at = NOW() WHERE id = ?", [$id]);
        audit_log('DELETE', 'Asset', 'Asset', $id, $old, null);

        return redirect()->route('assets.index')->with('success', 'Asset deleted successfully.');
    }

    /**
     * Warranty tracking view (Module 29).
     */
    public function warranty(Request $request)
    {
        $status = $request->get('status', 'all');
        $driver = DB::connection()->getDriverName();
        $diffSql = $driver === 'sqlite'
            ? "ROUND((JULIANDAY(a.warranty_expiry) - JULIANDAY(CURRENT_DATE)))"
            : "DATEDIFF(a.warranty_expiry, CURRENT_DATE)";

        $sql = "
            SELECT a.*, a.asset_code as asset_tag, a.asset_name as name,
                   c.name_en as category_name, c.name_km as category_name_km,
                   b.name as brand_name, s.name as supplier_name,
                   {$diffSql} as remaining_days
            FROM assets a
            LEFT JOIN categories c ON a.category_id = c.id
            LEFT JOIN brands b ON a.brand_id = b.id
            LEFT JOIN suppliers s ON a.supplier_id = s.id
            WHERE a.warranty_expiry IS NOT NULL AND a.deleted_at IS NULL
        ";

        if ($status === 'active') {
            $sql .= " AND {$diffSql} > 0";
        } elseif ($status === 'expired') {
            $sql .= " AND {$diffSql} < 0";
        } elseif ($status === 'expiring_soon') {
            $sql .= " AND {$diffSql} >= 0 AND {$diffSql} <= 30";
        }

        $sql .= " ORDER BY a.warranty_expiry ASC";

        $warranties = raw_paginate($sql, [], 20);
        $assets = $warranties;

        return view('assets.warranty', compact('warranties', 'assets', 'status'));
    }
}
