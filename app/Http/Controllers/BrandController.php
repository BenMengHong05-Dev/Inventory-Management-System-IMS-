<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $sql = "
            SELECT b.*,
                   (SELECT COUNT(*) FROM items i WHERE i.brand_id = b.id AND i.deleted_at IS NULL) as item_count,
                   (SELECT COUNT(*) FROM items i WHERE i.brand_id = b.id AND i.deleted_at IS NULL) as items_count
            FROM brands b
            WHERE b.deleted_at IS NULL
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (b.code LIKE ? OR b.name LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($status)) {
            $sql .= " AND b.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY b.id DESC";

        $brands = raw_paginate($sql, $params, 15);

        return view('master.brands.index', compact('brands', 'search', 'status'));
    }

    public function store(Request $request)
    {
        if ($request->filled('status')) {
            $request->merge(['status' => strtoupper($request->status)]);
        } else {
            $request->merge(['status' => 'ACTIVE']);
        }

        $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:ACTIVE,INACTIVE',
        ]);

        $existing = DB::select("SELECT id FROM brands WHERE (code = ? OR name = ?) AND deleted_at IS NULL LIMIT 1", [
            $request->code, $request->name
        ]);
        if (!empty($existing)) {
            return back()->withInput()->withErrors(['code' => 'A brand with this code or name already exists.']);
        }

        $uuid = (string) Str::uuid();
        DB::insert("
            INSERT INTO brands (uuid, code, name, description, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ", [
            $uuid,
            strtoupper($request->code),
            $request->name,
            $request->description,
            $request->status
        ]);

        $newId = DB::getPdo()->lastInsertId();
        audit_log('CREATE', 'Brand', 'Brand', $newId, null, $request->all());

        return redirect()->route('brands.index')->with('success', 'Brand created successfully.');
    }

    public function update(Request $request, $id)
    {
        if ($request->filled('status')) {
            $request->merge(['status' => strtoupper($request->status)]);
        } else {
            $request->merge(['status' => 'ACTIVE']);
        }

        $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:ACTIVE,INACTIVE',
        ]);

        $existing = DB::select("
            SELECT id FROM brands
            WHERE (code = ? OR name = ?) AND id != ? AND deleted_at IS NULL
            LIMIT 1
        ", [$request->code, $request->name, $id]);

        if (!empty($existing)) {
            return back()->withInput()->withErrors(['code' => 'A brand with this code or name already exists.']);
        }

        $old = DB::select("SELECT * FROM brands WHERE id = ? LIMIT 1", [$id])[0];

        DB::update("
            UPDATE brands
            SET code = ?, name = ?, description = ?, status = ?, updated_at = NOW()
            WHERE id = ?
        ", [
            strtoupper($request->code),
            $request->name,
            $request->description,
            $request->status,
            $id
        ]);

        audit_log('UPDATE', 'Brand', 'Brand', $id, $old, $request->all());

        return redirect()->route('brands.index')->with('success', 'Brand updated successfully.');
    }

    public function destroy($id)
    {
        $itemsCount = DB::select("SELECT COUNT(*) as cnt FROM items WHERE brand_id = ? AND deleted_at IS NULL", [$id])[0]->cnt;
        if ($itemsCount > 0) {
            return back()->with('error', "Cannot delete brand: It is linked to {$itemsCount} item(s).");
        }

        $old = DB::select("SELECT * FROM brands WHERE id = ? LIMIT 1", [$id])[0];
        DB::update("UPDATE brands SET deleted_at = NOW() WHERE id = ?", [$id]);
        audit_log('DELETE', 'Brand', 'Brand', $id, $old, null);

        return redirect()->route('brands.index')->with('success', 'Brand deleted successfully.');
    }
}
