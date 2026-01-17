<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('name')->get();
        $permissions = Permission::orderBy('module')->orderBy('name')->get()->groupBy('module');
        
        // Get current role-permission assignments
        $rolePermissions = [];
        foreach ($roles as $role) {
            $rolePermissions[$role->id] = $role->permissions->pluck('id')->toArray();
        }

        return view('roles.index', compact('roles', 'permissions', 'rolePermissions'));
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'slug' => 'required|string|max:255|unique:roles|regex:/^[a-z0-9_-]+$/',
            'description' => 'nullable|string|max:500',
        ]);

        $role = Role::create($validated);

        \App\Models\AuditLog::log('create', $role, null, $role->getAttributes(), 'Role created: ' . $role->name);

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'slug' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id), 'regex:/^[a-z0-9_-]+$/'],
            'description' => 'nullable|string|max:500',
        ]);

        $oldValues = $role->getAttributes();
        $role->update($validated);

        \App\Models\AuditLog::log('update', $role, $oldValues, $role->getChanges(), 'Role updated: ' . $role->name);

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return redirect()->route('roles.index')
                ->with('error', 'System roles cannot be deleted.');
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete role. There are users assigned to this role.');
        }

        $roleName = $role->name;
        $role->delete();

        \App\Models\AuditLog::log('delete', $role, $role->getAttributes(), null, 'Role deleted: ' . $roleName);

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $selectedPermissions = $request->permissions ?? [];

        // Sync permissions
        $role->permissions()->sync($selectedPermissions);

        \App\Models\AuditLog::log('update', $role, null, ['permissions' => $selectedPermissions], 'Role permissions updated: ' . $role->name);

        return redirect()->route('roles.index')
            ->with('success', 'Role permissions updated successfully.');
    }
}
