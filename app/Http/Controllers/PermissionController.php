<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\LookupCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{

    public function index(Request $request)
    {
        $query = Permission::query();

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('module', 'like', "%{$search}%");
            });
        }

        // Filter by module
        if ($request->has('module') && $request->module) {
            $query->where('module', $request->module);
        }

        $permissions = $query->orderBy('module')->orderBy('name')->paginate(15);
        $modules = Permission::distinct()->pluck('module')->filter()->sort()->values();

        return view('permissions.index', compact('permissions', 'modules'));
    }

    public function create()
    {
        $modules = Permission::distinct()->pluck('module')->filter()->sort()->values();
        return view('permissions.create', compact('modules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:permissions',
            'description' => 'nullable|string|max:500',
            'module' => 'nullable|string|max:100',
        ]);

        $permission = Permission::create($validated);

        \App\Models\AuditLog::log('create', $permission, null, $permission->getAttributes(), 'Permission created: ' . $permission->name);

        return redirect()->route('permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    public function edit(Permission $permission)
    {
        $modules = Permission::distinct()->pluck('module')->filter()->sort()->values();
        return view('permissions.edit', compact('permission', 'modules'));
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:permissions,slug,' . $permission->id,
            'description' => 'nullable|string|max:500',
            'module' => 'nullable|string|max:100',
        ]);

        $oldValues = $permission->getAttributes();
        $permission->update($validated);

        \App\Models\AuditLog::log('update', $permission, $oldValues, $permission->getChanges(), 'Permission updated: ' . $permission->name);

        return redirect()->route('permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $permissionName = $permission->name;
        $permission->delete();

        \App\Models\AuditLog::log('delete', $permission, $permission->getAttributes(), null, 'Permission deleted: ' . $permissionName);

        return redirect()->route('permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}

