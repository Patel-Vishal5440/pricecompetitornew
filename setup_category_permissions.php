<?php

/**
 * Script to add category permissions to existing roles
 * Run: php setup_category_permissions.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;
use App\Models\Role;

echo "Setting up Category Permissions...\n\n";

// Create category permissions if they don't exist
$categoryPermissions = [
    ['name' => 'category.view', 'description' => 'View categories', 'group' => 'Category Management'],
    ['name' => 'category.create', 'description' => 'Create categories', 'group' => 'Category Management'],
    ['name' => 'category.edit', 'description' => 'Edit categories', 'group' => 'Category Management'],
    ['name' => 'category.delete', 'description' => 'Delete categories', 'group' => 'Category Management'],
];

foreach ($categoryPermissions as $permissionData) {
    $permission = Permission::firstOrCreate(
        ['name' => $permissionData['name']],
        $permissionData
    );
    echo "✓ Permission '{$permissionData['name']}' created/verified\n";
}

// Assign category permissions to roles
$roles = [
    'super admin' => ['category.view', 'category.create', 'category.edit', 'category.delete'],
    'admin' => ['category.view', 'category.create', 'category.edit', 'category.delete'],
    'manager' => ['category.view', 'category.create', 'category.edit'],
    'analyst' => ['category.view'],
];

foreach ($roles as $roleName => $permissionNames) {
    $role = Role::where('name', $roleName)->first();
    if ($role) {
        $permissionIds = Permission::whereIn('name', $permissionNames)->pluck('id')->toArray();
        
        // Get existing permissions
        $existingPermissionIds = $role->permissions()->pluck('permissions.id')->toArray();
        
        // Merge with new permissions
        $allPermissionIds = array_unique(array_merge($existingPermissionIds, $permissionIds));
        
        $role->assignPermissions($allPermissionIds);
        echo "✓ Assigned category permissions to role '{$roleName}'\n";
    } else {
        echo "⚠ Role '{$roleName}' not found\n";
    }
}

echo "\n✅ Category permissions setup completed!\n";
echo "\nNote: If you're still getting 403 errors, make sure your user has one of these roles:\n";
echo "   - super admin\n";
echo "   - admin\n";
echo "   - manager (with category permissions)\n";
echo "   - analyst (view only)\n";

