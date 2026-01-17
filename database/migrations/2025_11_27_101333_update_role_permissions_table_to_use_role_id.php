<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('role_permissions')) {
            return;
        }

        // Add role_id column if it doesn't exist
        if (!Schema::hasColumn('role_permissions', 'role_id')) {
            Schema::table('role_permissions', function (Blueprint $table) {
                $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->cascadeOnDelete();
            });
        }

        // Migrate data from role (string) to role_id
        $rolePermissions = DB::table('role_permissions')
            ->whereNull('role_id')
            ->whereNotNull('role')
            ->get();

        foreach ($rolePermissions as $rp) {
            $role = DB::table('roles')->where('slug', $rp->role)->first();
            if ($role) {
                DB::table('role_permissions')
                    ->where('id', $rp->id)
                    ->update(['role_id' => $role->id]);
            }
        }

        // Make role column nullable (we'll keep it for backward compatibility temporarily)
        if (Schema::hasColumn('role_permissions', 'role')) {
            Schema::table('role_permissions', function (Blueprint $table) {
                $table->string('role')->nullable()->change();
            });
        }

        // Drop old unique constraint if it exists
        try {
            Schema::table('role_permissions', function (Blueprint $table) {
                $table->dropUnique(['role', 'permission_id']);
            });
        } catch (\Exception $e) {
            // Constraint might not exist
        }

        // Add new unique constraint on role_id and permission_id
        try {
            Schema::table('role_permissions', function (Blueprint $table) {
                $table->unique(['role_id', 'permission_id']);
            });
        } catch (\Exception $e) {
            // Constraint might already exist
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('role_permissions')) {
            return;
        }

        // Drop new unique constraint
        try {
            Schema::table('role_permissions', function (Blueprint $table) {
                $table->dropUnique(['role_id', 'permission_id']);
            });
        } catch (\Exception $e) {
            // Constraint might not exist
        }

        // Restore old unique constraint
        Schema::table('role_permissions', function (Blueprint $table) {
            $table->unique(['role', 'permission_id']);
        });

        // Make role column required again
        if (Schema::hasColumn('role_permissions', 'role')) {
            Schema::table('role_permissions', function (Blueprint $table) {
                $table->string('role')->nullable(false)->change();
            });
        }

        // Migrate data back from role_id to role
        $rolePermissions = DB::table('role_permissions')
            ->whereNotNull('role_id')
            ->whereNull('role')
            ->get();

        foreach ($rolePermissions as $rp) {
            $role = DB::table('roles')->where('id', $rp->role_id)->first();
            if ($role) {
                DB::table('role_permissions')
                    ->where('id', $rp->id)
                    ->update(['role' => $role->slug]);
            }
        }

        // Drop role_id column
        if (Schema::hasColumn('role_permissions', 'role_id')) {
            Schema::table('role_permissions', function (Blueprint $table) {
                $table->dropForeign(['role_id']);
                $table->dropColumn('role_id');
            });
        }
    }
};
