<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create all permissions for all modules
        $permissions = [
            // User Management
            ['name' => 'user.view', 'description' => 'View users', 'group' => 'User Management'],
            ['name' => 'user.create', 'description' => 'Create users', 'group' => 'User Management'],
            ['name' => 'user.edit', 'description' => 'Edit users', 'group' => 'User Management'],
            ['name' => 'user.delete', 'description' => 'Delete users', 'group' => 'User Management'],
            ['name' => 'user.export', 'description' => 'Export users', 'group' => 'User Management'],
            ['name' => 'user.import', 'description' => 'Import users', 'group' => 'User Management'],
            
            // Role Management
            ['name' => 'role.view', 'description' => 'View roles', 'group' => 'Role Management'],
            ['name' => 'role.create', 'description' => 'Create roles', 'group' => 'Role Management'],
            ['name' => 'role.edit', 'description' => 'Edit roles', 'group' => 'Role Management'],
            ['name' => 'role.delete', 'description' => 'Delete roles', 'group' => 'Role Management'],
            ['name' => 'role.assign', 'description' => 'Assign roles', 'group' => 'Role Management'],
            
            // Permission Management
            ['name' => 'permission.view', 'description' => 'View permissions', 'group' => 'Permission Management'],
            ['name' => 'permission.create', 'description' => 'Create permissions', 'group' => 'Permission Management'],
            ['name' => 'permission.edit', 'description' => 'Edit permissions', 'group' => 'Permission Management'],
            ['name' => 'permission.delete', 'description' => 'Delete permissions', 'group' => 'Permission Management'],
            ['name' => 'permission.assign', 'description' => 'Assign permissions', 'group' => 'Permission Management'],
            
            // Product Management
            ['name' => 'product.view', 'description' => 'View products', 'group' => 'Product Management'],
            ['name' => 'product.create', 'description' => 'Create products', 'group' => 'Product Management'],
            ['name' => 'product.edit', 'description' => 'Edit products', 'group' => 'Product Management'],
            ['name' => 'product.delete', 'description' => 'Delete products', 'group' => 'Product Management'],
            ['name' => 'product.sync', 'description' => 'Sync products', 'group' => 'Product Management'],
            ['name' => 'product.export', 'description' => 'Export products', 'group' => 'Product Management'],
            ['name' => 'product.import', 'description' => 'Import products', 'group' => 'Product Management'],
            ['name' => 'product.bulk_edit', 'description' => 'Bulk edit products', 'group' => 'Product Management'],
            
            // Competitor Management
            ['name' => 'competitor.view', 'description' => 'View competitors', 'group' => 'Competitor Management'],
            ['name' => 'competitor.create', 'description' => 'Create competitors', 'group' => 'Competitor Management'],
            ['name' => 'competitor.edit', 'description' => 'Edit competitors', 'group' => 'Competitor Management'],
            ['name' => 'competitor.delete', 'description' => 'Delete competitors', 'group' => 'Competitor Management'],
            ['name' => 'competitor.scrape', 'description' => 'Scrape competitor data', 'group' => 'Competitor Management'],
            ['name' => 'competitor.monitor', 'description' => 'Monitor competitors', 'group' => 'Competitor Management'],
            ['name' => 'competitor.export', 'description' => 'Export competitor data', 'group' => 'Competitor Management'],
            
            // Category Management
            ['name' => 'category.view', 'description' => 'View categories', 'group' => 'Category Management'],
            ['name' => 'category.create', 'description' => 'Create categories', 'group' => 'Category Management'],
            ['name' => 'category.edit', 'description' => 'Edit categories', 'group' => 'Category Management'],
            ['name' => 'category.delete', 'description' => 'Delete categories', 'group' => 'Category Management'],
            
            // Price History
            ['name' => 'price_history.view', 'description' => 'View price history', 'group' => 'Price History'],
            ['name' => 'price_history.export', 'description' => 'Export price history', 'group' => 'Price History'],
            ['name' => 'price_history.analyze', 'description' => 'Analyze price trends', 'group' => 'Price History'],
            ['name' => 'price_history.delete', 'description' => 'Delete price history', 'group' => 'Price History'],
            
            // Dashboard & Analytics
            ['name' => 'dashboard.view', 'description' => 'View dashboard', 'group' => 'Dashboard'],
            ['name' => 'dashboard.analytics', 'description' => 'View analytics', 'group' => 'Dashboard'],
            ['name' => 'dashboard.reports', 'description' => 'Generate reports', 'group' => 'Dashboard'],
            ['name' => 'dashboard.export', 'description' => 'Export dashboard data', 'group' => 'Dashboard'],
            
            // System Settings
            ['name' => 'settings.view', 'description' => 'View settings', 'group' => 'System Settings'],
            ['name' => 'settings.edit', 'description' => 'Edit settings', 'group' => 'System Settings'],
            ['name' => 'settings.system', 'description' => 'System configuration', 'group' => 'System Settings'],
            ['name' => 'settings.backup', 'description' => 'Backup system', 'group' => 'System Settings'],
            ['name' => 'settings.restore', 'description' => 'Restore system', 'group' => 'System Settings'],
            
            // Cron Jobs
            ['name' => 'cron.view', 'description' => 'View cron jobs', 'group' => 'Cron Jobs'],
            ['name' => 'cron.create', 'description' => 'Create cron jobs', 'group' => 'Cron Jobs'],
            ['name' => 'cron.edit', 'description' => 'Edit cron jobs', 'group' => 'Cron Jobs'],
            ['name' => 'cron.delete', 'description' => 'Delete cron jobs', 'group' => 'Cron Jobs'],
            ['name' => 'cron.execute', 'description' => 'Execute cron jobs', 'group' => 'Cron Jobs'],
            
            // Activity Feed
            ['name' => 'activity.view', 'description' => 'View activity feed', 'group' => 'Activity Feed'],
            ['name' => 'activity.export', 'description' => 'Export activity feed', 'group' => 'Activity Feed'],
            ['name' => 'activity.delete', 'description' => 'Delete activity records', 'group' => 'Activity Feed'],
            
            // API Management
            ['name' => 'api.view', 'description' => 'View API settings', 'group' => 'API Management'],
            ['name' => 'api.create', 'description' => 'Create API keys', 'group' => 'API Management'],
            ['name' => 'api.edit', 'description' => 'Edit API settings', 'group' => 'API Management'],
            ['name' => 'api.delete', 'description' => 'Delete API keys', 'group' => 'API Management'],
            
            // Logs
            ['name' => 'logs.view', 'description' => 'View system logs', 'group' => 'System Logs'],
            ['name' => 'logs.export', 'description' => 'Export logs', 'group' => 'System Logs'],
            ['name' => 'logs.delete', 'description' => 'Delete logs', 'group' => 'System Logs'],
            
            // Notifications
            ['name' => 'notification.view', 'description' => 'View notifications', 'group' => 'Notifications'],
            ['name' => 'notification.create', 'description' => 'Create notifications', 'group' => 'Notifications'],
            ['name' => 'notification.edit', 'description' => 'Edit notifications', 'group' => 'Notifications'],
            ['name' => 'notification.delete', 'description' => 'Delete notifications', 'group' => 'Notifications'],
            
            // Email Management
            ['name' => 'email.view', 'description' => 'View emails', 'group' => 'Email Management'],
            ['name' => 'email.send', 'description' => 'Send emails', 'group' => 'Email Management'],
            ['name' => 'email.template', 'description' => 'Manage email templates', 'group' => 'Email Management'],
            ['name' => 'email.settings', 'description' => 'Email settings', 'group' => 'Email Management'],
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        // Create Super Admin role with all permissions
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super admin'],
            [
                'name' => 'super admin',
                'description' => 'Super Administrator with complete system access and control over all modules',
                'is_active' => true
            ]
        );

        // Assign all permissions to super admin role
        $allPermissions = Permission::all();
        $superAdminRole->assignPermissions($allPermissions->pluck('id')->toArray());

        // Create Super Admin user
        $superAdminUser = User::firstOrCreate(
            ['email' => 'superadmin@pricecompetitor.com'],
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@pricecompetitor.com',
                'password' => Hash::make('superadmin123'),
                'role_id' => $superAdminRole->id,
                'phone_number' => '+1234567890',
                'country' => 'United States',
                'city' => 'New York',
                'company_name' => 'Price Competitor System',
                'website' => 'https://pricecompetitor.com',
                'bio' => 'Super Administrator with full system access',
                'skills' => ['System Administration', 'User Management', 'Data Analysis'],
                'email_verified_at' => now(),
            ]
        );

        // Create additional admin roles for different access levels
        $adminRoles = [
            [
                'name' => 'admin',
                'description' => 'System Administrator with full access to all features',
                'permissions' => $allPermissions->pluck('id')->toArray()
            ],
            [
                'name' => 'manager',
                'description' => 'Manager with access to product and competitor management',
                'permissions' => Permission::whereIn('group', [
                    'User Management', 'Product Management', 'Competitor Management', 
                    'Price History', 'Dashboard', 'Activity Feed'
                ])->pluck('id')->toArray()
            ],
            [
                'name' => 'analyst',
                'description' => 'Analyst with read-only access to products and price history',
                'permissions' => Permission::whereIn('name', [
                    'product.view', 'competitor.view', 'price_history.view', 
                    'dashboard.view', 'dashboard.analytics', 'activity.view'
                ])->pluck('id')->toArray()
            ],
            [
                'name' => 'user',
                'description' => 'Regular user with limited access',
                'permissions' => Permission::whereIn('name', [
                    'dashboard.view', 'product.view', 'price_history.view'
                ])->pluck('id')->toArray()
            ]
        ];

        foreach ($adminRoles as $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleData['name']],
                [
                    'name' => $roleData['name'],
                    'description' => $roleData['description'],
                    'is_active' => true
                ]
            );
            
            if (!empty($roleData['permissions'])) {
                $role->assignPermissions($roleData['permissions']);
            }
        }

        $this->command->info('Super Admin seeder completed successfully!');
        $this->command->info('Super Admin User: superadmin@pricecompetitor.com');
        $this->command->info('Super Admin Password: superadmin123');
        $this->command->info('Total Permissions Created: ' . $allPermissions->count());
        $this->command->info('Total Roles Created: ' . Role::count());
    }
} 