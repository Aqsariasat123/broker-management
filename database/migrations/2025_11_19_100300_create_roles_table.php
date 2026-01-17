<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->boolean('is_system')->default(false); // System roles like 'admin' cannot be deleted
                $table->timestamps();
            });
        }

        // Insert default roles if they don't exist
        if (!DB::table('roles')->where('slug', 'admin')->exists()) {
            DB::table('roles')->insert([
                ['name' => 'Admin', 'slug' => 'admin', 'description' => 'Full system access', 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
        
        if (!DB::table('roles')->where('slug', 'support')->exists()) {
            DB::table('roles')->insert([
                ['name' => 'Support', 'slug' => 'support', 'description' => 'Support staff access', 'is_system' => false, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // Add is_system column if it doesn't exist
        if (Schema::hasTable('roles') && !Schema::hasColumn('roles', 'is_system')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->boolean('is_system')->default(false)->after('description');
            });
            
            // Mark admin as system role
            DB::table('roles')->where('slug', 'admin')->update(['is_system' => true]);
        }

        // Add role_id to users table if it doesn't exist
        if (!Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('role_id')->nullable()->after('email')->constrained('roles')->nullOnDelete();
            });
        }

        // Update role_permissions table to use role_id if column doesn't exist
        if (Schema::hasTable('role_permissions') && !Schema::hasColumn('role_permissions', 'role_id')) {
            Schema::table('role_permissions', function (Blueprint $table) {
                $table->foreignId('role_id')->after('id')->constrained('roles')->cascadeOnDelete();
            });
        }

        // Migrate existing data
        $adminRole = DB::table('roles')->where('slug', 'admin')->first();
        $supportRole = DB::table('roles')->where('slug', 'support')->first();
        
        if ($adminRole && $supportRole) {
            // Update users - check if role column exists and migrate to role_id
            if (Schema::hasColumn('users', 'role') && Schema::hasColumn('users', 'role_id')) {
                DB::table('users')->where('role', 'admin')->whereNull('role_id')->update(['role_id' => $adminRole->id]);
                DB::table('users')->where('role', 'support')->whereNull('role_id')->update(['role_id' => $supportRole->id]);
            }
            
            // Update role_permissions - migrate from string role to role_id
            if (Schema::hasTable('role_permissions')) {
                if (Schema::hasColumn('role_permissions', 'role') && Schema::hasColumn('role_permissions', 'role_id')) {
                    DB::table('role_permissions')->where('role', 'admin')->whereNull('role_id')->update(['role_id' => $adminRole->id]);
                    DB::table('role_permissions')->where('role', 'support')->whereNull('role_id')->update(['role_id' => $supportRole->id]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::table('role_permissions', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
            $table->string('role')->after('id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
            $table->string('role')->default('support')->after('email');
        });

        Schema::dropIfExists('roles');
    }
};

