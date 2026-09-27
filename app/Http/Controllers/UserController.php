<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $roleId = $request->get('role_id');

        $sql = "
            SELECT u.*,
                   r.name as role_name, r.id as role_id
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            WHERE u.deleted_at IS NULL
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term, $term]);
        }

        if (!empty($status)) {
            $sql .= " AND u.status = ?";
            $params[] = $status;
        }

        if (!empty($roleId)) {
            $sql .= " AND r.id = ?";
            $params[] = $roleId;
        }

        $sql .= " ORDER BY u.id DESC";

        $users = raw_paginate($sql, $params, 15);
        $roles = DB::select("SELECT id, name FROM roles ORDER BY name ASC");

        return view('admin.users.index', compact('users', 'roles', 'search', 'status', 'roleId'));
    }

    public function create()
    {
        $roles = DB::select("SELECT id, name FROM roles ORDER BY name ASC");
        $warehouses = DB::select("SELECT id, name FROM warehouses WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");
        return view('admin.users.create', compact('roles', 'warehouses'));
    }

    public function store(Request $request)
    {
        if ($request->has('is_active') && !$request->filled('status')) {
            $request->merge(['status' => $request->is_active ? 'ACTIVE' : 'INACTIVE']);
        } elseif ($request->filled('status')) {
            $request->merge(['status' => strtoupper($request->status)]);
        } else {
            $request->merge(['status' => 'ACTIVE']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:50',
            'role_id' => 'required|integer',
            'status' => 'required|in:ACTIVE,INACTIVE',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $uuid = (string) Str::uuid();
        $hashedPassword = Hash::make($request->password);

        DB::transaction(function () use ($request, $uuid, $hashedPassword, $avatarPath) {
            DB::insert("
                INSERT INTO users (uuid, name, email, password, phone, status, avatar, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ", [
                $uuid,
                $request->name,
                $request->email,
                $hashedPassword,
                $request->phone,
                $request->status,
                $avatarPath
            ]);

            $userId = DB::getPdo()->lastInsertId();

            DB::insert("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)", [$userId, $request->role_id]);

            audit_log('CREATE', 'UserManagement', 'User', $userId, null, [
                'name' => $request->name,
                'email' => $request->email,
                'role_id' => $request->role_id
            ]);
        });

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $users = DB::select("
            SELECT u.*, ur.role_id
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            WHERE u.id = ? AND u.deleted_at IS NULL
            LIMIT 1
        ", [$id]);

        if (empty($users)) {
            abort(404);
        }

        $user = $users[0];
        $roles = DB::select("SELECT id, name FROM roles ORDER BY name ASC");
        $warehouses = DB::select("SELECT id, name FROM warehouses WHERE status = 'ACTIVE' AND deleted_at IS NULL ORDER BY name ASC");

        return view('admin.users.edit', compact('user', 'roles', 'warehouses'));
    }

    public function update(Request $request, $id)
    {
        if ($request->has('is_active') && !$request->filled('status')) {
            $request->merge(['status' => $request->is_active ? 'ACTIVE' : 'INACTIVE']);
        } elseif ($request->filled('status')) {
            $request->merge(['status' => strtoupper($request->status)]);
        } else {
            $request->merge(['status' => 'ACTIVE']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|string|max:50',
            'role_id' => 'required|integer',
            'status' => 'required|in:ACTIVE,INACTIVE',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $existing = DB::select("SELECT id FROM users WHERE email = ? AND id != ? AND deleted_at IS NULL LIMIT 1", [$request->email, $id]);
        if (!empty($existing)) {
            return back()->withInput()->withErrors(['email' => 'Another user with this email already exists.']);
        }

        $old = DB::select("SELECT * FROM users WHERE id = ? LIMIT 1", [$id])[0];
        $avatarPath = $old->avatar;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        DB::transaction(function () use ($request, $old, $avatarPath, $id) {
            $params = [
                $request->name,
                $request->email,
                $request->phone,
                $request->status,
                $avatarPath,
            ];

            $passwordSql = "";
            if ($request->filled('password')) {
                $passwordSql = ", password = ?";
                $params[] = Hash::make($request->password);
            }

            $params[] = $id;

            DB::update("
                UPDATE users
                SET name = ?, email = ?, phone = ?, status = ?, avatar = ? {$passwordSql}, updated_at = NOW()
                WHERE id = ?
            ", $params);

            // Update user role
            DB::delete("DELETE FROM user_roles WHERE user_id = ?", [$id]);
            DB::insert("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)", [$id, $request->role_id]);

            audit_log('UPDATE', 'UserManagement', 'User', $id, $old, $request->except(['password', 'password_confirmation']));
        });

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        if ($id == session('user_id')) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $old = DB::select("SELECT * FROM users WHERE id = ? LIMIT 1", [$id])[0];
        DB::update("UPDATE users SET deleted_at = NOW() WHERE id = ?", [$id]);
        audit_log('DELETE', 'UserManagement', 'User', $id, $old, null);

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
