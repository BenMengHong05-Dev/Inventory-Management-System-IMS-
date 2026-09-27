<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $sql = "
            SELECT d.*, u.name as manager_name,
                   (SELECT COUNT(*) FROM employees e WHERE e.department_id = d.id AND e.deleted_at IS NULL) as employee_count
            FROM departments d
            LEFT JOIN users u ON d.manager_id = u.id
            WHERE d.deleted_at IS NULL
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (d.code LIKE ? OR d.name_en LIKE ? OR d.name_km LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term, $term]);
        }

        if (!empty($status)) {
            $sql .= " AND d.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY d.id DESC";
        $departments = raw_paginate($sql, $params, 15);
        $managers = DB::select("SELECT id, name FROM users WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");

        return view('master.departments.index', compact('departments', 'managers', 'search', 'status'));
    }

    public function store(Request $request)
    {
        if (!$request->filled('name_en') && $request->filled('name')) {
            $request->merge(['name_en' => $request->name]);
        }
        if (!$request->filled('name_km') && $request->filled('name')) {
            $request->merge(['name_km' => $request->name]);
        }
        if ($request->filled('status')) {
            $request->merge(['status' => strtoupper($request->status)]);
        }

        $request->validate([
            'code' => 'required|string|max:50',
            'name_en' => 'required|string|max:255',
            'name_km' => 'required|string|max:255',
            'manager_id' => 'nullable|integer',
            'description' => 'nullable|string',
            'status' => 'required|in:ACTIVE,INACTIVE',
        ]);

        $existing = DB::select("SELECT id FROM departments WHERE code = ? AND deleted_at IS NULL LIMIT 1", [$request->code]);
        if (!empty($existing)) {
            return back()->withInput()->withErrors(['code' => 'A department with this code already exists.']);
        }

        $uuid = (string) Str::uuid();

        DB::insert("
            INSERT INTO departments (uuid, code, name_en, name_km, manager_id, description, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ", [
            $uuid,
            strtoupper($request->code),
            $request->name_en,
            $request->name_km,
            $request->manager_id ?: null,
            $request->description,
            $request->status
        ]);

        $deptId = DB::getPdo()->lastInsertId();
        audit_log('CREATE', 'Department', 'Department', $deptId, null, $request->all());

        return redirect()->route('departments.index')->with('success', 'Department created successfully.');
    }

    public function update(Request $request, $id)
    {
        if (!$request->filled('name_en') && $request->filled('name')) {
            $request->merge(['name_en' => $request->name]);
        }
        if (!$request->filled('name_km') && $request->filled('name')) {
            $request->merge(['name_km' => $request->name]);
        }
        if ($request->filled('status')) {
            $request->merge(['status' => strtoupper($request->status)]);
        }

        $request->validate([
            'code' => 'required|string|max:50',
            'name_en' => 'required|string|max:255',
            'name_km' => 'required|string|max:255',
            'manager_id' => 'nullable|integer',
            'description' => 'nullable|string',
            'status' => 'required|in:ACTIVE,INACTIVE',
        ]);

        $existing = DB::select("SELECT id FROM departments WHERE code = ? AND id != ? AND deleted_at IS NULL LIMIT 1", [$request->code, $id]);
        if (!empty($existing)) {
            return back()->withInput()->withErrors(['code' => 'A department with this code already exists.']);
        }

        $old = DB::select("SELECT * FROM departments WHERE id = ? LIMIT 1", [$id])[0];

        DB::update("
            UPDATE departments
            SET code = ?, name_en = ?, name_km = ?, manager_id = ?, description = ?, status = ?, updated_at = NOW()
            WHERE id = ?
        ", [
            strtoupper($request->code),
            $request->name_en,
            $request->name_km,
            $request->manager_id ?: null,
            $request->description,
            $request->status,
            $id
        ]);

        audit_log('UPDATE', 'Department', 'Department', $id, $old, $request->all());

        return redirect()->route('departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy($id)
    {
        $empCount = DB::select("SELECT COUNT(*) as cnt FROM employees WHERE department_id = ? AND deleted_at IS NULL", [$id])[0]->cnt;
        if ($empCount > 0) {
            return back()->with('error', "Cannot delete department: There are {$empCount} employee(s) in this department.");
        }

        $old = DB::select("SELECT * FROM departments WHERE id = ? LIMIT 1", [$id])[0];
        DB::update("UPDATE departments SET deleted_at = NOW() WHERE id = ?", [$id]);
        audit_log('DELETE', 'Department', 'Department', $id, $old, null);

        return redirect()->route('departments.index')->with('success', 'Department deleted successfully.');
    }
}
