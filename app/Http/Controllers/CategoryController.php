<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * List categories with parent relationship, search, and filter.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $sql = "
            SELECT c.*, p.name_en as parent_name, p.name_km as parent_name_km,
                   (SELECT COUNT(*) FROM items i WHERE (i.category_id = c.id OR i.sub_category_id = c.id) AND i.deleted_at IS NULL) as item_count
            FROM categories c
            LEFT JOIN categories p ON c.parent_id = p.id
            WHERE c.deleted_at IS NULL
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (c.code LIKE ? OR c.name_en LIKE ? OR c.name_km LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($status)) {
            $sql .= " AND c.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY c.id DESC";

        $categories = raw_paginate($sql, $params, 15);
        $parentCategories = DB::select("SELECT id, name_en, name_km FROM categories WHERE parent_id IS NULL AND deleted_at IS NULL ORDER BY name_en ASC");

        return view('master.categories.index', compact('categories', 'parentCategories', 'search', 'status'));
    }

    /**
     * Store category via raw SQL.
     */
    public function store(Request $request)
    {
        // Support either 'name' or 'name_en'
        if (!$request->has('name_en') && $request->has('name')) {
            $request->merge(['name_en' => $request->name]);
        }
        if (!$request->has('name_km') && $request->has('name_en')) {
            $request->merge(['name_km' => $request->name_en]);
        }
        if (!$request->has('code') || empty($request->code)) {
            $request->merge(['code' => generate_code('CAT', 'categories')]);
        }

        $request->validate([
            'code' => 'required|string|max:50',
            'name_en' => 'required|string|max:255',
            'name_km' => 'nullable|string|max:255',
            'parent_id' => 'nullable|integer',
            'description' => 'nullable|string',
            'status' => 'required|in:ACTIVE,INACTIVE,active,inactive',
        ]);

        // Duplicate code / name check via raw SQL
        $existing = DB::select("SELECT id FROM categories WHERE (code = ? OR name_en = ?) AND deleted_at IS NULL LIMIT 1", [
            $request->code, $request->name_en
        ]);
        if (!empty($existing)) {
            return back()->withInput()->withErrors(['code' => 'A category with this code or English name already exists.']);
        }

        $uuid = (string) Str::uuid();
        DB::insert("
            INSERT INTO categories (uuid, code, name_en, name_km, parent_id, description, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ", [
            $uuid,
            strtoupper($request->code),
            $request->name_en,
            $request->name_km,
            $request->parent_id ?: null,
            $request->description,
            $request->status
        ]);

        $newId = DB::getPdo()->lastInsertId();
        audit_log('CREATE', 'Category', 'Category', $newId, null, $request->all());

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    /**
     * Update category via raw SQL.
     */
    public function update(Request $request, $id)
    {
        if (!$request->has('name_en') && $request->has('name')) {
            $request->merge(['name_en' => $request->name]);
        }
        if (!$request->has('name_km') && $request->has('name_en')) {
            $request->merge(['name_km' => $request->name_en]);
        }

        $request->validate([
            'code' => 'required|string|max:50',
            'name_en' => 'required|string|max:255',
            'name_km' => 'nullable|string|max:255',
            'parent_id' => 'nullable|integer',
            'description' => 'nullable|string',
            'status' => 'required|in:ACTIVE,INACTIVE,active,inactive',
        ]);

        // Check duplicate excluding this category
        $existing = DB::select("
            SELECT id FROM categories
            WHERE (code = ? OR name_en = ?) AND id != ? AND deleted_at IS NULL
            LIMIT 1
        ", [$request->code, $request->name_en, $id]);

        if (!empty($existing)) {
            return back()->withInput()->withErrors(['code' => 'A category with this code or English name already exists.']);
        }

        $old = DB::select("SELECT * FROM categories WHERE id = ? LIMIT 1", [$id])[0];

        DB::update("
            UPDATE categories
            SET code = ?, name_en = ?, name_km = ?, parent_id = ?, description = ?, status = ?, updated_at = NOW()
            WHERE id = ?
        ", [
            strtoupper($request->code),
            $request->name_en,
            $request->name_km,
            $request->parent_id ?: null,
            $request->description,
            $request->status,
            $id
        ]);

        audit_log('UPDATE', 'Category', 'Category', $id, $old, $request->all());

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Soft delete category via raw SQL with dependency validation.
     */
    public function destroy($id)
    {
        // Business Rule: Prevent deletion when records are using the category
        $itemsCount = DB::select("
            SELECT COUNT(*) as cnt FROM items
            WHERE (category_id = ? OR sub_category_id = ?) AND deleted_at IS NULL
        ", [$id, $id])[0]->cnt;

        if ($itemsCount > 0) {
            return back()->with('error', "Cannot delete category: It is currently linked to {$itemsCount} item(s).");
        }

        $childrenCount = DB::select("
            SELECT COUNT(*) as cnt FROM categories
            WHERE parent_id = ? AND deleted_at IS NULL
        ", [$id])[0]->cnt;

        if ($childrenCount > 0) {
            return back()->with('error', "Cannot delete category: It has {$childrenCount} sub-category(ies) assigned.");
        }

        $old = DB::select("SELECT * FROM categories WHERE id = ? LIMIT 1", [$id])[0];

        DB::update("UPDATE categories SET deleted_at = NOW() WHERE id = ?", [$id]);
        audit_log('DELETE', 'Category', 'Category', $id, $old, null);

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
