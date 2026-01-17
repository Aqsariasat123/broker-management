<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'View Dashboard', 'slug' => 'dashboard.view', 'module' => 'dashboard'],
            
            // Policies
            ['name' => 'View Policies', 'slug' => 'policies.view', 'module' => 'policies'],
            ['name' => 'Create Policies', 'slug' => 'policies.create', 'module' => 'policies'],
            ['name' => 'Edit Policies', 'slug' => 'policies.edit', 'module' => 'policies'],
            ['name' => 'Delete Policies', 'slug' => 'policies.delete', 'module' => 'policies'],
            
            // Clients
            ['name' => 'View Clients', 'slug' => 'clients.view', 'module' => 'clients'],
            ['name' => 'Create Clients', 'slug' => 'clients.create', 'module' => 'clients'],
            ['name' => 'Edit Clients', 'slug' => 'clients.edit', 'module' => 'clients'],
            ['name' => 'Delete Clients', 'slug' => 'clients.delete', 'module' => 'clients'],
            
            // Contacts
            ['name' => 'View Contacts', 'slug' => 'contacts.view', 'module' => 'contacts'],
            ['name' => 'Create Contacts', 'slug' => 'contacts.create', 'module' => 'contacts'],
            ['name' => 'Edit Contacts', 'slug' => 'contacts.edit', 'module' => 'contacts'],
            ['name' => 'Delete Contacts', 'slug' => 'contacts.delete', 'module' => 'contacts'],
            
            // Life Proposals
            ['name' => 'View Life Proposals', 'slug' => 'life-proposals.view', 'module' => 'life-proposals'],
            ['name' => 'Create Life Proposals', 'slug' => 'life-proposals.create', 'module' => 'life-proposals'],
            ['name' => 'Edit Life Proposals', 'slug' => 'life-proposals.edit', 'module' => 'life-proposals'],
            ['name' => 'Delete Life Proposals', 'slug' => 'life-proposals.delete', 'module' => 'life-proposals'],
            
            // Claims
            ['name' => 'View Claims', 'slug' => 'claims.view', 'module' => 'claims'],
            ['name' => 'Create Claims', 'slug' => 'claims.create', 'module' => 'claims'],
            ['name' => 'Edit Claims', 'slug' => 'claims.edit', 'module' => 'claims'],
            ['name' => 'Delete Claims', 'slug' => 'claims.delete', 'module' => 'claims'],
            
            // Expenses
            ['name' => 'View Expenses', 'slug' => 'expenses.view', 'module' => 'expenses'],
            ['name' => 'Create Expenses', 'slug' => 'expenses.create', 'module' => 'expenses'],
            ['name' => 'Edit Expenses', 'slug' => 'expenses.edit', 'module' => 'expenses'],
            ['name' => 'Delete Expenses', 'slug' => 'expenses.delete', 'module' => 'expenses'],
            
            // Incomes
            ['name' => 'View Incomes', 'slug' => 'incomes.view', 'module' => 'incomes'],
            ['name' => 'Create Incomes', 'slug' => 'incomes.create', 'module' => 'incomes'],
            ['name' => 'Edit Incomes', 'slug' => 'incomes.edit', 'module' => 'incomes'],
            ['name' => 'Delete Incomes', 'slug' => 'incomes.delete', 'module' => 'incomes'],
            
            // Commissions
            ['name' => 'View Commissions', 'slug' => 'commissions.view', 'module' => 'commissions'],
            ['name' => 'Create Commissions', 'slug' => 'commissions.create', 'module' => 'commissions'],
            ['name' => 'Edit Commissions', 'slug' => 'commissions.edit', 'module' => 'commissions'],
            ['name' => 'Delete Commissions', 'slug' => 'commissions.delete', 'module' => 'commissions'],
            
            // Statements
            ['name' => 'View Statements', 'slug' => 'statements.view', 'module' => 'statements'],
            ['name' => 'Create Statements', 'slug' => 'statements.create', 'module' => 'statements'],
            ['name' => 'Edit Statements', 'slug' => 'statements.edit', 'module' => 'statements'],
            ['name' => 'Delete Statements', 'slug' => 'statements.delete', 'module' => 'statements'],
            
            // Payments
            ['name' => 'View Payments', 'slug' => 'payments.view', 'module' => 'payments'],
            ['name' => 'Create Payments', 'slug' => 'payments.create', 'module' => 'payments'],
            ['name' => 'Edit Payments', 'slug' => 'payments.edit', 'module' => 'payments'],
            ['name' => 'Delete Payments', 'slug' => 'payments.delete', 'module' => 'payments'],
            
            // Payment Plans
            ['name' => 'View Payment Plans', 'slug' => 'payment-plans.view', 'module' => 'payment-plans'],
            ['name' => 'Create Payment Plans', 'slug' => 'payment-plans.create', 'module' => 'payment-plans'],
            ['name' => 'Edit Payment Plans', 'slug' => 'payment-plans.edit', 'module' => 'payment-plans'],
            ['name' => 'Delete Payment Plans', 'slug' => 'payment-plans.delete', 'module' => 'payment-plans'],
            
            // Debit Notes
            ['name' => 'View Debit Notes', 'slug' => 'debit-notes.view', 'module' => 'debit-notes'],
            ['name' => 'Create Debit Notes', 'slug' => 'debit-notes.create', 'module' => 'debit-notes'],
            ['name' => 'Edit Debit Notes', 'slug' => 'debit-notes.edit', 'module' => 'debit-notes'],
            ['name' => 'Delete Debit Notes', 'slug' => 'debit-notes.delete', 'module' => 'debit-notes'],
            
            // Vehicles
            ['name' => 'View Vehicles', 'slug' => 'vehicles.view', 'module' => 'vehicles'],
            ['name' => 'Create Vehicles', 'slug' => 'vehicles.create', 'module' => 'vehicles'],
            ['name' => 'Edit Vehicles', 'slug' => 'vehicles.edit', 'module' => 'vehicles'],
            ['name' => 'Delete Vehicles', 'slug' => 'vehicles.delete', 'module' => 'vehicles'],
            
            // Documents
            ['name' => 'View Documents', 'slug' => 'documents.view', 'module' => 'documents'],
            ['name' => 'Create Documents', 'slug' => 'documents.create', 'module' => 'documents'],
            ['name' => 'Edit Documents', 'slug' => 'documents.edit', 'module' => 'documents'],
            ['name' => 'Delete Documents', 'slug' => 'documents.delete', 'module' => 'documents'],
            
            // Tasks
            ['name' => 'View Tasks', 'slug' => 'tasks.view', 'module' => 'tasks'],
            ['name' => 'Create Tasks', 'slug' => 'tasks.create', 'module' => 'tasks'],
            ['name' => 'Edit Tasks', 'slug' => 'tasks.edit', 'module' => 'tasks'],
            ['name' => 'Delete Tasks', 'slug' => 'tasks.delete', 'module' => 'tasks'],
            
            // Schedules
            ['name' => 'View Schedules', 'slug' => 'schedules.view', 'module' => 'schedules'],
            ['name' => 'Create Schedules', 'slug' => 'schedules.create', 'module' => 'schedules'],
            ['name' => 'Edit Schedules', 'slug' => 'schedules.edit', 'module' => 'schedules'],
            ['name' => 'Delete Schedules', 'slug' => 'schedules.delete', 'module' => 'schedules'],
            
            // Calendar
            ['name' => 'View Calendar', 'slug' => 'calendar.view', 'module' => 'calendar'],
            
            // Nominees
            ['name' => 'View Nominees', 'slug' => 'nominees.view', 'module' => 'nominees'],
            ['name' => 'Create Nominees', 'slug' => 'nominees.create', 'module' => 'nominees'],
            ['name' => 'Edit Nominees', 'slug' => 'nominees.edit', 'module' => 'nominees'],
            ['name' => 'Delete Nominees', 'slug' => 'nominees.delete', 'module' => 'nominees'],
            
            // Users
            ['name' => 'View Users', 'slug' => 'users.view', 'module' => 'users'],
            ['name' => 'Create Users', 'slug' => 'users.create', 'module' => 'users'],
            ['name' => 'Edit Users', 'slug' => 'users.edit', 'module' => 'users'],
            ['name' => 'Delete Users', 'slug' => 'users.delete', 'module' => 'users'],
            
            // Roles
            ['name' => 'View Roles', 'slug' => 'roles.view', 'module' => 'roles'],
            ['name' => 'Create Roles', 'slug' => 'roles.create', 'module' => 'roles'],
            ['name' => 'Edit Roles', 'slug' => 'roles.edit', 'module' => 'roles'],
            ['name' => 'Delete Roles', 'slug' => 'roles.delete', 'module' => 'roles'],
            
            // Permissions
            ['name' => 'View Permissions', 'slug' => 'permissions.view', 'module' => 'permissions'],
            ['name' => 'Create Permissions', 'slug' => 'permissions.create', 'module' => 'permissions'],
            ['name' => 'Edit Permissions', 'slug' => 'permissions.edit', 'module' => 'permissions'],
            ['name' => 'Delete Permissions', 'slug' => 'permissions.delete', 'module' => 'permissions'],
            
            // Audit Logs
            ['name' => 'View Audit Logs', 'slug' => 'audit-logs.view', 'module' => 'audit-logs'],
            
            // Lookups
            ['name' => 'View Lookups', 'slug' => 'lookups.view', 'module' => 'lookups'],
            ['name' => 'Manage Lookups', 'slug' => 'lookups.manage', 'module' => 'lookups'],
            
            // Reports
            ['name' => 'View Reports', 'slug' => 'reports.view', 'module' => 'reports'],
            ['name' => 'Export Reports', 'slug' => 'reports.export', 'module' => 'reports'],
            
            // Settings
            ['name' => 'Manage Settings', 'slug' => 'settings.manage', 'module' => 'settings'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        // Assign permissions to roles
        $adminRole = \App\Models\Role::where('slug', 'admin')->first();
        $supportRole = \App\Models\Role::where('slug', 'support')->first();
        
        if ($adminRole) {
            // Admin gets all permissions
            $adminPermissions = Permission::all();
            foreach ($adminPermissions as $permission) {
                DB::table('role_permissions')->updateOrInsert(
                    [
                        'role_id' => $adminRole->id,
                        'permission_id' => $permission->id,
                    ],
                    [
                        'role_id' => $adminRole->id,
                        'role' => 'admin', // Keep for backward compatibility
                        'permission_id' => $permission->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        if ($supportRole) {
            // Support gets view permissions only
            $supportPermissions = Permission::where('slug', 'like', '%.view')->get();
            foreach ($supportPermissions as $permission) {
                DB::table('role_permissions')->updateOrInsert(
                    [
                        'role_id' => $supportRole->id,
                        'permission_id' => $permission->id,
                    ],
                    [
                        'role_id' => $supportRole->id,
                        'role' => 'support', // Keep for backward compatibility
                        'permission_id' => $permission->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}

