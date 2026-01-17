<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'module',
    ];

    public static function hasPermission(string $role, string $permissionSlug): bool
    {
        // Try new method first (role_id)
        $roleModel = \App\Models\Role::where('slug', $role)->first();
        if ($roleModel) {
            return static::where('slug', $permissionSlug)
                ->whereHas('roles', function($q) use ($roleModel) {
                    $q->where('roles.id', $roleModel->id);
                })
                ->exists();
        }

        // Fallback to old method (string role)
        return static::where('slug', $permissionSlug)
            ->whereExists(function($query) use ($role) {
                $query->select(\DB::raw(1))
                    ->from('role_permissions')
                    ->whereColumn('role_permissions.permission_id', 'permissions.id')
                    ->where('role_permissions.role', $role);
            })
            ->exists();
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id')
            ->withTimestamps();
    }
}

