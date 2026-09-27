<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $sql = "
            SELECT s.*,
                   (SELECT COUNT(*) FROM purchase_orders po WHERE po.supplier_id = s.id) as po_count,
                   (SELECT COALESCE(SUM(total), 0) FROM purchase_orders po WHERE po.supplier_id = s.id) as total_purchased
            FROM suppliers s
            WHERE s.deleted_at IS NULL
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (s.code LIKE ? OR s.name LIKE ? OR s.contact_person LIKE ? OR s.phone LIKE ? OR s.email LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term, $term, $term, $term]);
        }

        if (!empty($status)) {
            $sql .= " AND s.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY s.id DESC";
        $suppliers = raw_paginate($sql, $params, 15);

        return view('master.suppliers.index', compact('suppliers', 'search', 'status'));
    }

    public function create()
    {
        $nextCode = generate_code('SUP', 'suppliers');
        return view('master.suppliers.create', compact('nextCode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'tax_number' => 'nullable|string|max:100',
            'bank_info' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|in:ACTIVE,INACTIVE',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $existing = DB::select("SELECT id FROM suppliers WHERE code = ? AND deleted_at IS NULL LIMIT 1", [$request->code]);
        if (!empty($existing)) {
            return back()->withInput()->withErrors(['code' => 'A supplier with this code already exists.']);
        }

        $uuid = (string) Str::uuid();

        DB::insert("
            INSERT INTO suppliers (uuid, code, name, contact_person, phone, email, address, tax_number, bank_info, description, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ", [
            $uuid,
            strtoupper($request->code),
            $request->name,
            $request->contact_person,
            $request->phone,
            $request->email,
            $request->address,
            $request->tax_number,
            $request->bank_info,
            $request->description,
            $request->status
        ]);

        $supplierId = DB::getPdo()->lastInsertId();

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('supplier_attachments', 'public');
            DB::insert("
                INSERT INTO attachments (record_type, record_id, file_name, file_path, file_size, file_type, uploaded_by, created_at, updated_at)
                VALUES ('SUPPLIER', ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ", [
                $supplierId,
                $file->getClientOriginalName(),
                $path,
                $file->getSize(),
                $file->getClientMimeType(),
                session('user_id')
            ]);
        }

        audit_log('CREATE', 'Supplier', 'Supplier', $supplierId, null, $request->all());

        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function show($id)
    {
        $suppliers = DB::select("SELECT * FROM suppliers WHERE id = ? AND deleted_at IS NULL LIMIT 1", [$id]);
        if (empty($suppliers)) {
            abort(404, 'Supplier not found.');
        }
        $supplier = $suppliers[0];

        // Purchase history
        $purchaseOrders = DB::select("
            SELECT po.*, w.name as warehouse_name
            FROM purchase_orders po
            LEFT JOIN warehouses w ON po.warehouse_id = w.id
            WHERE po.supplier_id = ?
            ORDER BY po.id DESC
        ", [$id]);

        // Total purchase amount & PO count
        $stats = DB::select("
            SELECT COUNT(*) as po_count, COALESCE(SUM(total), 0) as total_amount
            FROM purchase_orders
            WHERE supplier_id = ?
        ", [$id])[0];

        // Items supplied
        $itemsSupplied = DB::select("
            SELECT DISTINCT i.id, i.code, i.name_en, i.name_km, c.name_en as category_name
            FROM purchase_order_items poi
            JOIN purchase_orders po ON poi.purchase_order_id = po.id
            JOIN items i ON poi.item_id = i.id
            LEFT JOIN categories c ON i.category_id = c.id
            WHERE po.supplier_id = ?
        ", [$id]);

        $attachments = DB::select("SELECT * FROM attachments WHERE record_type = 'SUPPLIER' AND record_id = ?", [$id]);

        return view('master.suppliers.show', compact('supplier', 'purchaseOrders', 'stats', 'itemsSupplied', 'attachments'));
    }

    public function edit($id)
    {
        $suppliers = DB::select("SELECT * FROM suppliers WHERE id = ? AND deleted_at IS NULL LIMIT 1", [$id]);
        if (empty($suppliers)) {
            abort(404);
        }
        $supplier = $suppliers[0];
        $attachments = DB::select("SELECT * FROM attachments WHERE record_type = 'SUPPLIER' AND record_id = ?", [$id]);

        return view('master.suppliers.edit', compact('supplier', 'attachments'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'tax_number' => 'nullable|string|max:100',
            'bank_info' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|in:ACTIVE,INACTIVE',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $existing = DB::select("SELECT id FROM suppliers WHERE code = ? AND id != ? AND deleted_at IS NULL LIMIT 1", [$request->code, $id]);
        if (!empty($existing)) {
            return back()->withInput()->withErrors(['code' => 'A supplier with this code already exists.']);
        }

        $old = DB::select("SELECT * FROM suppliers WHERE id = ? LIMIT 1", [$id])[0];

        DB::update("
            UPDATE suppliers
            SET code = ?, name = ?, contact_person = ?, phone = ?, email = ?, address = ?, tax_number = ?, bank_info = ?, description = ?, status = ?, updated_at = NOW()
            WHERE id = ?
        ", [
            strtoupper($request->code),
            $request->name,
            $request->contact_person,
            $request->phone,
            $request->email,
            $request->address,
            $request->tax_number,
            $request->bank_info,
            $request->description,
            $request->status,
            $id
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('supplier_attachments', 'public');
            DB::insert("
                INSERT INTO attachments (record_type, record_id, file_name, file_path, file_size, file_type, uploaded_by, created_at, updated_at)
                VALUES ('SUPPLIER', ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ", [
                $id,
                $file->getClientOriginalName(),
                $path,
                $file->getSize(),
                $file->getClientMimeType(),
                session('user_id')
            ]);
        }

        audit_log('UPDATE', 'Supplier', 'Supplier', $id, $old, $request->all());

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy($id)
    {
        $poCount = DB::select("SELECT COUNT(*) as cnt FROM purchase_orders WHERE supplier_id = ?", [$id])[0]->cnt;
        if ($poCount > 0) {
            return back()->with('error', "Cannot delete supplier: There are {$poCount} purchase order(s) associated with this supplier.");
        }

        $old = DB::select("SELECT * FROM suppliers WHERE id = ? LIMIT 1", [$id])[0];
        DB::update("UPDATE suppliers SET deleted_at = NOW() WHERE id = ?", [$id]);
        audit_log('DELETE', 'Supplier', 'Supplier', $id, $old, null);

        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }
}
