<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sql = "
            SELECT u.*, (SELECT COUNT(*) FROM items i WHERE i.unit_id = u.id AND i.deleted_at IS NULL) as items_count 
            FROM units u 
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (u.code LIKE ? OR u.name_en LIKE ? OR u.name_km LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $sql .= " ORDER BY u.id ASC";
        $units = raw_paginate($sql, $params, 20);

        return view('master.units.index', compact('units', 'search'));
    }

    public function store(Request $request)
    {
        if (!$request->filled('name_en') && $request->filled('name')) {
            $request->merge(['name_en' => $request->name]);
        }
        if (!$request->filled('name_km') && $request->filled('name')) {
            $request->merge(['name_km' => $request->name]);
        }

        $request->validate([
            'code' => 'required|string|max:20',
            'name_en' => 'required|string|max:100',
            'name_km' => 'required|string|max:100',
        ]);

        $existing = DB::select("SELECT id FROM units WHERE code = ? LIMIT 1", [strtoupper($request->code)]);
        if (!empty($existing)) {
            return back()->withInput()->withErrors(['code' => 'A unit with this code already exists.']);
        }

        DB::insert("
            INSERT INTO units (code, name_en, name_km, created_at, updated_at)
            VALUES (?, ?, ?, NOW(), NOW())
        ", [
            strtoupper($request->code),
            $request->name_en,
            $request->name_km
        ]);

        $newId = DB::getPdo()->lastInsertId();
        audit_log('CREATE', 'Unit', 'Unit', $newId, null, $request->all());

        return redirect()->route('units.index')->with('success', 'Unit created successfully.');
    }

    public function update(Request $request, $id)
    {
        if (!$request->filled('name_en') && $request->filled('name')) {
            $request->merge(['name_en' => $request->name]);
        }
        if (!$request->filled('name_km') && $request->filled('name')) {
            $request->merge(['name_km' => $request->name]);
        }

        $request->validate([
            'code' => 'required|string|max:20',
            'name_en' => 'required|string|max:100',
            'name_km' => 'required|string|max:100',
        ]);

        $existing = DB::select("SELECT id FROM units WHERE code = ? AND id != ? LIMIT 1", [$request->code, $id]);
        if (!empty($existing)) {
            return back()->withInput()->withErrors(['code' => 'A unit with this code already exists.']);
        }

        $old = DB::select("SELECT * FROM units WHERE id = ? LIMIT 1", [$id])[0];

        DB::update("
            UPDATE units
            SET code = ?, name_en = ?, name_km = ?, updated_at = NOW()
            WHERE id = ?
        ", [strtoupper($request->code), $request->name_en, $request->name_km, $id]);

        audit_log('UPDATE', 'Unit', 'Unit', $id, $old, $request->all());

        return redirect()->route('units.index')->with('success', 'Unit updated successfully.');
    }

    public function destroy($id)
    {
        $itemsCount = DB::select("SELECT COUNT(*) as cnt FROM items WHERE unit_id = ? AND deleted_at IS NULL", [$id])[0]->cnt;
        if ($itemsCount > 0) {
            return back()->with('error', "Cannot delete unit: It is assigned to {$itemsCount} item(s).");
        }

        $old = DB::select("SELECT * FROM units WHERE id = ? LIMIT 1", [$id])[0];
        DB::delete("DELETE FROM units WHERE id = ?", [$id]);
        audit_log('DELETE', 'Unit', 'Unit', $id, $old, null);

        return redirect()->route('units.index')->with('success', 'Unit deleted successfully.');
    }
}
