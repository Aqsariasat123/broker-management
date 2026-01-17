<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'role_id',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Get the role relationship
     */
    public function roleModel()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $role): bool
    {
        // Support both old string role and new role_id
        if ($this->role_id && $this->role_id > 0) {
            $roleModel = $this->roleModel;
            if ($roleModel && $roleModel->slug === $role) {
                return true;
            }
        }
        // Fallback to old string role field
        if ($this->role && $this->role === $role) {
            return true;
        }
        return false;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        // Check role_id first
        if ($this->role_id && $this->role_id > 0) {
            $roleModel = $this->roleModel;
            if ($roleModel && $roleModel->slug === 'admin') {
                return true;
            }
        }
        // Fallback to old string role field
        return $this->role === 'admin';
    }

    /**
     * Check if user is support
     */
    public function isSupport(): bool
    {
        return $this->hasRole('support');
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permissionSlug): bool
    {
        if ($this->isAdmin()) {
            return true; // Admins have all permissions
        }

        if ($this->role_id) {
            $role = $this->roleModel;
            if ($role) {
                return $role->permissions()->where('slug', $permissionSlug)->exists();
            }
        }

        // Fallback to old method
        return Permission::hasPermission($this->role ?? 'support', $permissionSlug);
    }

    /**
     * Get user's audit logs
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Get user's permissions
     */
    public function permissions()
    {
        if ($this->role_id) {
            $role = $this->roleModel;
            if ($role) {
                return $role->permissions;
            }
        }
        
        // Fallback to old method
        return Permission::whereExists(function($query) {
            $query->select(\DB::raw(1))
                ->from('role_permissions')
                ->whereColumn('role_permissions.permission_id', 'permissions.id')
                ->where('role_permissions.role', $this->role);
        })->get();
    }
}
