<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // User Management
            ['name' => 'user.view', 'description' => 'View users', 'group' => 'User Management'],
            ['name' => 'user.create', 'description' => 'Create users', 'group' => 'User Management'],
            ['name' => 'user.edit', 'description' => 'Edit users', 'group' => 'User Management'],
            ['name' => 'user.delete', 'description' => 'Delete users', 'group' => 'User Management'],
            
            // Role Management
            ['name' => 'role.view', 'description' => 'View roles', 'group' => 'Role Management'],
            ['name' => 'role.create', 'description' => 'Create roles', 'group' => 'Role Management'],
            ['name' => 'role.edit', 'description' => 'Edit roles', 'group' => 'Role Management'],
            ['name' => 'role.delete', 'description' => 'Delete roles', 'group' => 'Role Management'],
            
            // Permission Management
            ['name' => 'permission.view', 'description' => 'View permissions', 'group' => 'Permission Management'],
            ['name' => 'permission.create', 'description' => 'Create permissions', 'group' => 'Permission Management'],
            ['name' => 'permission.edit', 'description' => 'Edit permissions', 'group' => 'Permission Management'],
            ['name' => 'permission.delete', 'description' => 'Delete permissions', 'group' => 'Permission Management'],
            
            // Product Management
            ['name' => 'product.view', 'description' => 'View products', 'group' => 'Product Management'],
            ['name' => 'product.create', 'description' => 'Create products', 'group' => 'Product Management'],
            ['name' => 'product.edit', 'description' => 'Edit products', 'group' => 'Product Management'],
            ['name' => 'product.delete', 'description' => 'Delete products', 'group' => 'Product Management'],
            ['name' => 'product.sync', 'description' => 'Sync products', 'group' => 'Product Management'],
            
            // Competitor Management
            ['name' => 'competitor.view', 'description' => 'View competitors', 'group' => 'Competitor Management'],
            ['name' => 'competitor.create', 'description' => 'Create competitors', 'group' => 'Competitor Management'],
            ['name' => 'competitor.edit', 'description' => 'Edit competitors', 'group' => 'Competitor Management'],
            ['name' => 'competitor.delete', 'description' => 'Delete competitors', 'group' => 'Competitor Management'],
            
            // Price History
            ['name' => 'price_history.view', 'description' => 'View price history', 'group' => 'Price History'],
            ['name' => 'price_history.export', 'description' => 'Export price history', 'group' => 'Price History'],
            
            // Dashboard
            ['name' => 'dashboard.view', 'description' => 'View dashboard', 'group' => 'Dashboard'],
            ['name' => 'dashboard.analytics', 'description' => 'View analytics', 'group' => 'Dashboard'],
            
            // System Settings
            ['name' => 'settings.view', 'description' => 'View settings', 'group' => 'System Settings'],
            ['name' => 'settings.edit', 'description' => 'Edit settings', 'group' => 'System Settings'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Create roles
        $roles = [
            [
                'name' => 'super admin',
                'description' => 'Super Administrator with complete system access and control',
                'permissions' => Permission::all()->pluck('id')->toArray()
            ],
            [
                'name' => 'admin',
                'description' => 'System Administrator with full access to all features',
                'permissions' => Permission::all()->pluck('id')->toArray()
            ],
            [
                'name' => 'manager',
                'description' => 'Manager with access to product and competitor management',
                'permissions' => [
                    'user.view', 'product.view', 'product.create', 'product.edit', 'product.sync',
                    'competitor.view', 'competitor.create', 'competitor.edit',
                    'price_history.view', 'price_history.export', 'dashboard.view', 'dashboard.analytics'
                ]
            ],
            [
                'name' => 'analyst',
                'description' => 'Analyst with read-only access to products and price history',
                'permissions' => [
                    'product.view', 'competitor.view', 'price_history.view', 'dashboard.view'
                ]
            ],
            [
                'name' => 'user',
                'description' => 'Regular user with limited access',
                'permissions' => [
                    'dashboard.view'
                ]
            ]
        ];

        foreach ($roles as $roleData) {
            $permissionIds = [];
            if (isset($roleData['permissions'])) {
                foreach ($roleData['permissions'] as $permissionName) {
                    $permission = Permission::where('name', $permissionName)->first();
                    if ($permission) {
                        $permissionIds[] = $permission->id;
                    }
                }
            }
            
            $role = Role::create([
                'name' => $roleData['name'],
                'description' => $roleData['description'],
                'is_active' => true
            ]);
            
            if (!empty($permissionIds)) {
                $role->assignPermissions($permissionIds);
            }
        }

        // Assign super admin role to existing admin user
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        if ($adminUser) {
            $superAdminRole = Role::where('name', 'super admin')->first();
            if ($superAdminRole) {
                $adminUser->update(['role_id' => $superAdminRole->id]);
            }
        }
    }
}
