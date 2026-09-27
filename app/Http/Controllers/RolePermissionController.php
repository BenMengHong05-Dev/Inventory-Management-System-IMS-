<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RolePermissionController extends Controller
{
    /**
     * List all roles with assigned user counts and permission counts.
     */
    public function index()
    {
        $roles = DB::select("
            SELECT r.*,
                   (SELECT COUNT(*) FROM user_roles ur WHERE ur.role_id = r.id) as user_count,
                   (SELECT COUNT(*) FROM user_roles ur WHERE ur.role_id = r.id) as users_count,
                   (SELECT COUNT(*) FROM role_permissions rp WHERE rp.role_id = r.id) as permission_count,
                   (SELECT COUNT(*) FROM role_permissions rp WHERE rp.role_id = r.id) as permissions_count
            FROM roles r
            ORDER BY r.id ASC
        ");

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * View and manage role permissions matrix.
     */
    public function edit($id)
    {
        $roles = DB::select("SELECT * FROM roles WHERE id = ? LIMIT 1", [$id]);
        if (empty($roles)) {
            abort(404);
        }
        $role = $roles[0];

        // Fetch all permissions ordered by module and name
        $permissions = DB::select("SELECT * FROM permissions ORDER BY module ASC, name ASC");

        // Group permissions by module for convenience
        $modules = [];
        foreach ($permissions as $perm) {
            $modules[$perm->module][] = $perm;
        }

        // Fetch assigned permission IDs for this role
        $assignedPerms = DB::select("SELECT permission_id FROM role_permissions WHERE role_id = ?", [$id]);
        $rolePermissions = array_column($assignedPerms, 'permission_id');
        $assignedIds = $rolePermissions;

        return view('admin.roles.edit', compact('role', 'permissions', 'modules', 'rolePermissions', 'assignedIds'));
    }

    /**
     * Update role permissions via raw SQL.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer',
        ]);

        $roles = DB::select("SELECT * FROM roles WHERE id = ? LIMIT 1", [$id]);
        if (empty($roles)) {
            abort(404);
        }
        $role = $roles[0];

        DB::transaction(function () use ($request, $role, $id) {
            $name = $request->filled('name') ? $request->name : $role->name;
            $desc = $request->has('description') ? $request->description : $role->description;

            DB::update("
                UPDATE roles
                SET name = ?, description = ?, updated_at = NOW()
                WHERE id = ?
            ", [$name, $desc, $id]);

            // Sync permissions using raw SQL delete & insert
            DB::delete("DELETE FROM role_permissions WHERE role_id = ?", [$id]);

            if ($request->has('permissions') && is_array($request->permissions)) {
                foreach ($request->permissions as $permId) {
                    DB::insert("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)", [$id, (int)$permId]);
                }
            }

            audit_log('UPDATE_PERMISSIONS', 'RoleManagement', 'Role', $id, null, [
                'role' => $name,
                'permissions_count' => count($request->permissions ?? [])
            ]);
        });

        return redirect()->route('admin.roles.index')->with('success', 'Role permissions updated successfully.');
    }

    /**
     * Alias for update method to match possible route names.
     */
    public function updatePermissions(Request $request, $id)
    {
        return $this->update($request, $id);
    }

    /**
     * Store new role.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $slug = $request->filled('slug') ? Str::slug($request->slug) : Str::slug($request->name);
        
        // Ensure slug is unique
        $exists = DB::select("SELECT 1 FROM roles WHERE slug = ? LIMIT 1", [$slug]);
        if (!empty($exists)) {
            $slug = $slug . '-' . time();
        }

        DB::insert("
            INSERT INTO roles (name, slug, description, created_at, updated_at)
            VALUES (?, ?, ?, NOW(), NOW())
        ", [
            $request->name,
            $slug,
            $request->description
        ]);

        $newId = DB::getPdo()->lastInsertId();
        audit_log('CREATE', 'RoleManagement', 'Role', $newId, null, $request->all());

        return redirect()->route('admin.roles.index')->with('success', 'New role created successfully.');
    }

    /**
     * Alias for store method to match route name.
     */
    public function storeRole(Request $request)
    {
        return $this->store($request);
    }

    /**
     * Delete role and its associations.
     */
    public function destroy($id)
    {
        $roles = DB::select("SELECT * FROM roles WHERE id = ? LIMIT 1", [$id]);
        if (empty($roles)) {
            abort(404);
        }
        $role = $roles[0];

        if ($role->name === 'Super Admin' || $role->slug === 'super-admin') {
            return back()->with('error', 'The Super Admin role cannot be deleted.');
        }

        DB::transaction(function () use ($id, $role) {
            DB::delete("DELETE FROM role_permissions WHERE role_id = ?", [$id]);
            DB::delete("DELETE FROM user_roles WHERE role_id = ?", [$id]);
            DB::delete("DELETE FROM roles WHERE id = ?", [$id]);
            audit_log('DELETE', 'RoleManagement', 'Role', $id, $role, null);
        });

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}
