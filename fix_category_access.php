<?php

/**
 * Quick fix script to ensure current user can access categories
 * Run: php fix_category_access.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

echo "Fixing Category Access...\n\n";

// Ensure category permissions exist
$categoryPermissions = [
    ['name' => 'category.view', 'description' => 'View categories', 'group' => 'Category Management'],
    ['name' => 'category.create', 'description' => 'Create categories', 'group' => 'Category Management'],
    ['name' => 'category.edit', 'description' => 'Edit categories', 'group' => 'Category Management'],
    ['name' => 'category.delete', 'description' => 'Delete categories', 'group' => 'Category Management'],
];

foreach ($categoryPermissions as $permissionData) {
    Permission::firstOrCreate(
        ['name' => $permissionData['name']],
        $permissionData
    );
}

// Get or create admin role
$adminRole = Role::firstOrCreate(
    ['name' => 'admin'],
    [
        'name' => 'admin',
        'description' => 'System Administrator with full access to all features',
        'is_active' => true
    ]
);

// Assign all permissions to admin role
$allPermissions = Permission::all()->pluck('id')->toArray();
$adminRole->assignPermissions($allPermissions);
echo "✓ Admin role has all permissions including categories\n";

// Get or create super admin role
$superAdminRole = Role::firstOrCreate(
    ['name' => 'super admin'],
    [
        'name' => 'super admin',
        'description' => 'Super Administrator with complete system access and control',
        'is_active' => true
    ]
);

$superAdminRole->assignPermissions($allPermissions);
echo "✓ Super admin role has all permissions including categories\n";

// List all users and their roles
echo "\nCurrent Users and Roles:\n";
$users = User::with('role')->get();
foreach ($users as $user) {
    $roleName = $user->role ? $user->role->name : 'No Role';
    echo "  - {$user->email}: {$roleName}\n";
}

echo "\n✅ Category access fixed!\n";
echo "\nTo assign admin role to a user, run:\n";
echo "php artisan tinker\n";
echo ">>> \$user = App\Models\User::where('email', 'your-email@example.com')->first();\n";
echo ">>> \$role = App\Models\Role::where('name', 'admin')->first();\n";
echo ">>> \$user->update(['role_id' => \$role->id]);\n";

