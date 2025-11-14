<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Role;

echo "Updating admin user...\n";

// Find the admin user
$user = User::where('email', 'admin@gmail.com')->first();

if ($user) {
    echo "User found: {$user->name}\n";
    
    // Update password
    $user->update(['password' => bcrypt('123456')]);
    
    // Assign Super Admin role if not already assigned
    $superAdminRole = Role::where('name', 'Super Admin')->first();
    if ($superAdminRole) {
        $user->update(['role_id' => $superAdminRole->id]);
        echo "Super Admin role assigned!\n";
    }
    
    echo "Admin user updated successfully!\n";
    echo "Email: admin@gmail.com\n";
    echo "Password: 123456\n";
    echo "Role: Super Admin\n";
} else {
    echo "Admin user not found. Creating new one...\n";
    
    $superAdminRole = Role::where('name', 'Super Admin')->first();
    
    $user = User::create([
        'name' => 'Super Admin',
        'email' => 'admin@gmail.com',
        'password' => bcrypt('123456'),
        'role_id' => $superAdminRole ? $superAdminRole->id : null,
    ]);
    
    echo "New admin user created successfully!\n";
    echo "Email: admin@gmail.com\n";
    echo "Password: 123456\n";
}

echo "Done!\n"; 