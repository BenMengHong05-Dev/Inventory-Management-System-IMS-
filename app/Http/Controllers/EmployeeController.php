<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $deptId = $request->get('department_id');
        $status = $request->get('status');

        $sql = "
            SELECT e.*, d.name_en as department_name, d.name_km as department_name_km,
                   (SELECT COUNT(*) FROM assets a WHERE a.current_employee_id = e.id AND a.deleted_at IS NULL) as active_assets_count
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.id
            WHERE e.deleted_at IS NULL
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (e.code LIKE ? OR e.name_en LIKE ? OR e.name_km LIKE ? OR e.phone LIKE ? OR e.email LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term, $term, $term, $term]);
        }

        if (!empty($deptId)) {
            $sql .= " AND e.department_id = ?";
            $params[] = $deptId;
        }

        if (!empty($status)) {
            $sql .= " AND e.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY e.id DESC";
        $employees = raw_paginate($sql, $params, 15);
        $departments = DB::select("SELECT id, name_en, name_km FROM departments WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name_en ASC");

        return view('master.employees.index', compact('employees', 'departments', 'search', 'deptId', 'status'));
    }

    public function create()
    {
        $nextCode = generate_code('EMP', 'employees');
        $departments = DB::select("SELECT id, name_en FROM departments WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name_en ASC");
        return view('master.employees.create', compact('nextCode', 'departments'));
    }

    public function store(Request $request)
    {
        if (!$request->filled('name_en') && $request->filled('name')) {
            $request->merge(['name_en' => $request->name]);
        }
        if (!$request->filled('name_km') && $request->filled('name')) {
            $request->merge(['name_km' => $request->name]);
        }
        if (!$request->filled('position') && $request->filled('designation')) {
            $request->merge(['position' => $request->designation]);
        }
        if (!$request->filled('gender')) {
            $request->merge(['gender' => 'OTHER']);
        }
        if ($request->filled('status')) {
            $request->merge(['status' => strtoupper($request->status)]);
        } else {
            $request->merge(['status' => 'ACTIVE']);
        }

        $request->validate([
            'code' => 'required|string|max:50',
            'name_en' => 'required|string|max:255',
            'name_km' => 'required|string|max:255',
            'gender' => 'required|in:MALE,FEMALE,OTHER',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'department_id' => 'nullable|integer',
            'position' => 'nullable|string|max:100',
            'status' => 'required|in:ACTIVE,INACTIVE',
            'photo' => 'nullable|image|max:2048',
        ]);

        $existing = DB::select("SELECT id FROM employees WHERE code = ? AND deleted_at IS NULL LIMIT 1", [$request->code]);
        if (!empty($existing)) {
            return back()->withInput()->withErrors(['code' => 'An employee with this code already exists.']);
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('employees', 'public');
        }

        $uuid = (string) Str::uuid();

        DB::insert("
            INSERT INTO employees (uuid, code, name_en, name_km, gender, phone, email, department_id, position, status, photo, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ", [
            $uuid,
            strtoupper($request->code),
            $request->name_en,
            $request->name_km,
            $request->gender,
            $request->phone,
            $request->email,
            $request->department_id ?: null,
            $request->position,
            $request->status,
            $photoPath
        ]);

        $empId = DB::getPdo()->lastInsertId();
        audit_log('CREATE', 'Employee', 'Employee', $empId, null, $request->all());

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function show($id)
    {
        $employees = DB::select("
            SELECT e.*, d.name_en as department_name
            FROM employees e
            LEFT JOIN departments d ON e.department_id = d.id
            WHERE e.id = ? AND e.deleted_at IS NULL
            LIMIT 1
        ", [$id]);

        if (empty($employees)) {
            abort(404, 'Employee not found.');
        }

        $employee = $employees[0];

        // 1. Currently Assigned Assets
        $assignedAssets = DB::select("
            SELECT a.*, c.name_en as category_name, b.name as brand_name, aa.assignment_date, aa.expected_return_date
            FROM assets a
            JOIN asset_assignments aa ON a.id = aa.asset_id AND aa.actual_return_date IS NULL
            LEFT JOIN categories c ON a.category_id = c.id
            LEFT JOIN brands b ON a.brand_id = b.id
            WHERE aa.employee_id = ? AND a.deleted_at IS NULL
        ", [$id]);

        // 2. Complete Asset Assignment History
        $assignmentHistory = DB::select("
            SELECT aah.*, a.asset_code, a.asset_name, u.name as user_name
            FROM asset_assignment_histories aah
            JOIN assets a ON aah.asset_id = a.id
            LEFT JOIN users u ON aah.user_id = u.id
            WHERE aah.new_holder_id = ? OR aah.previous_holder_id = ?
            ORDER BY aah.id DESC
        ", [$id, $id]);

        // 3. Stock Issued History (Stock Out to this employee)
        $stockIssuedHistory = DB::select("
            SELECT so.*, soi.item_id, i.name_en as item_name, soi.quantity, u.name_en as unit_name
            FROM stock_outs so
            JOIN stock_out_items soi ON so.id = soi.stock_out_id
            JOIN items i ON soi.item_id = i.id
            LEFT JOIN units u ON soi.unit_id = u.id
            WHERE so.employee_id = ?
            ORDER BY so.date DESC
        ", [$id]);

        return view('master.employees.show', compact('employee', 'assignedAssets', 'assignmentHistory', 'stockIssuedHistory'));
    }

    public function edit($id)
    {
        $employees = DB::select("SELECT * FROM employees WHERE id = ? AND deleted_at IS NULL LIMIT 1", [$id]);
        if (empty($employees)) {
            abort(404);
        }
        $employee = $employees[0];
        $departments = DB::select("SELECT id, name_en FROM departments WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name_en ASC");

        return view('master.employees.edit', compact('employee', 'departments'));
    }

    public function update(Request $request, $id)
    {
        if (!$request->filled('name_en') && $request->filled('name')) {
            $request->merge(['name_en' => $request->name]);
        }
        if (!$request->filled('name_km') && $request->filled('name')) {
            $request->merge(['name_km' => $request->name]);
        }
        if (!$request->filled('position') && $request->filled('designation')) {
            $request->merge(['position' => $request->designation]);
        }
        if (!$request->filled('gender')) {
            $request->merge(['gender' => 'OTHER']);
        }
        if ($request->filled('status')) {
            $request->merge(['status' => strtoupper($request->status)]);
        } else {
            $request->merge(['status' => 'ACTIVE']);
        }

        $request->validate([
            'code' => 'required|string|max:50',
            'name_en' => 'required|string|max:255',
            'name_km' => 'required|string|max:255',
            'gender' => 'required|in:MALE,FEMALE,OTHER',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'department_id' => 'nullable|integer',
            'position' => 'nullable|string|max:100',
            'status' => 'required|in:ACTIVE,INACTIVE',
            'photo' => 'nullable|image|max:2048',
        ]);

        $existing = DB::select("SELECT id FROM employees WHERE code = ? AND id != ? AND deleted_at IS NULL LIMIT 1", [$request->code, $id]);
        if (!empty($existing)) {
            return back()->withInput()->withErrors(['code' => 'An employee with this code already exists.']);
        }

        $old = DB::select("SELECT * FROM employees WHERE id = ? LIMIT 1", [$id])[0];
        $photoPath = $old->photo;

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('employees', 'public');
        }

        DB::update("
            UPDATE employees
            SET code = ?, name_en = ?, name_km = ?, gender = ?, phone = ?, email = ?, department_id = ?, position = ?, status = ?, photo = ?, updated_at = NOW()
            WHERE id = ?
        ", [
            strtoupper($request->code),
            $request->name_en,
            $request->name_km,
            $request->gender,
            $request->phone,
            $request->email,
            $request->department_id ?: null,
            $request->position,
            $request->status,
            $photoPath,
            $id
        ]);

        audit_log('UPDATE', 'Employee', 'Employee', $id, $old, $request->all());

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy($id)
    {
        // Check if employee has active assigned assets
        $assignedCount = DB::select("
            SELECT COUNT(*) as cnt FROM asset_assignments
            WHERE employee_id = ? AND actual_return_date IS NULL
        ", [$id])[0]->cnt;

        if ($assignedCount > 0) {
            return back()->with('error', "Cannot delete employee: The employee currently holds {$assignedCount} active assigned asset(s).");
        }

        $old = DB::select("SELECT * FROM employees WHERE id = ? LIMIT 1", [$id])[0];
        DB::update("UPDATE employees SET deleted_at = NOW() WHERE id = ?", [$id]);
        audit_log('DELETE', 'Employee', 'Employee', $id, $old, null);

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }
}
