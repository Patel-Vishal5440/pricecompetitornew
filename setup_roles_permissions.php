<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Artisan;

echo "=== Setting up Roles and Permissions ===\n\n";

// Run migrations if needed
echo "1. Running migrations...\n";
try {
    Artisan::call('migrate', ['--force' => true]);
    echo "✓ Migrations completed successfully\n";
} catch (Exception $e) {
    echo "⚠ Migration warning: " . $e->getMessage() . "\n";
}

// Seed roles and permissions
echo "\n2. Seeding roles and permissions...\n";
try {
    Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
    echo "✓ Roles and permissions seeded successfully\n";
} catch (Exception $e) {
    echo "⚠ Seeding warning: " . $e->getMessage() . "\n";
}

// Create or update admin user
echo "\n3. Setting up admin user...\n";
$adminUser = User::where('email', 'admin@gmail.com')->first();

if ($adminUser) {
    echo "✓ Admin user already exists\n";
} else {
    $adminUser = User::create([
        'name' => 'Super Admin',
        'email' => 'admin@gmail.com',
        'password' => bcrypt('123456'),
    ]);
    echo "✓ Admin user created\n";
}

// Assign super admin role
$superAdminRole = Role::where('name', 'super admin')->first();
if ($superAdminRole) {
    $adminUser->update(['role_id' => $superAdminRole->id]);
    echo "✓ Super Admin role assigned\n";
} else {
    echo "⚠ Super Admin role not found\n";
}

// Display summary
echo "\n=== Setup Summary ===\n";
echo "Admin Email: admin@gmail.com\n";
echo "Admin Password: 123456\n";
echo "Admin Role: Super Admin\n\n";

// List roles
echo "Available Roles:\n";
$roles = Role::all();
foreach ($roles as $role) {
    echo "- {$role->name}: {$role->description}\n";
}

echo "\n=== Setup Complete ===\n";
echo "You can now login with admin@gmail.com / 123456\n";
echo "Use 'php artisan roles:manage list-roles' to see all roles\n";
echo "Use 'php artisan roles:manage list-permissions' to see all permissions\n"; 