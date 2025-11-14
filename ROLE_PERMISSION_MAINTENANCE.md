# Role and Permission Maintenance Guide

## Overview
This guide explains how to maintain roles and permissions in your Laravel application. The system uses a role-based access control (RBAC) model where users are assigned roles, and roles have permissions.

## Database Structure

### Tables
1. **users** - Contains user information with `role_id` foreign key
2. **roles** - Contains role definitions
3. **permissions** - Contains permission definitions
4. **role_permissions** - Pivot table linking roles to permissions

### Key Fields
- **roles**: `name`, `description`, `is_active`
- **permissions**: `name`, `description`, `group`, `is_active`

## How to Maintain Roles and Permissions

### 1. Running Migrations and Seeders

```bash
# Run migrations to create tables
php artisan migrate

# Seed initial roles and permissions
php artisan db:seed --class=RolePermissionSeeder
```

### 2. Creating New Permissions

#### Method 1: Using Seeder (Recommended)
Edit `database/seeders/RolePermissionSeeder.php`:

```php
// Add new permissions to the $permissions array
['name' => 'new.permission', 'description' => 'New permission description', 'group' => 'New Group'],

// Then run the seeder
php artisan db:seed --class=RolePermissionSeeder
```

#### Method 2: Using Tinker
```bash
php artisan tinker
```

```php
use App\Models\Permission;

Permission::create([
    'name' => 'new.permission',
    'description' => 'New permission description',
    'group' => 'New Group',
    'is_active' => true
]);
```

### 3. Creating New Roles

#### Method 1: Using Seeder
Edit `database/seeders/RolePermissionSeeder.php`:

```php
// Add new role to the $roles array
[
    'name' => 'new_role',
    'description' => 'New role description',
    'permissions' => ['permission1', 'permission2', 'permission3']
],

// Then run the seeder
php artisan db:seed --class=RolePermissionSeeder
```

#### Method 2: Using Tinker
```bash
php artisan tinker
```

```php
use App\Models\Role;
use App\Models\Permission;

$role = Role::create([
    'name' => 'new_role',
    'description' => 'New role description',
    'is_active' => true
]);

// Assign permissions
$permissions = Permission::whereIn('name', ['permission1', 'permission2'])->pluck('id');
$role->assignPermissions($permissions);
```

### 4. Assigning Roles to Users

#### Method 1: Using Tinker
```bash
php artisan tinker
```

```php
use App\Models\User;
use App\Models\Role;

$user = User::where('email', 'user@example.com')->first();
$role = Role::where('name', 'admin')->first();

$user->update(['role_id' => $role->id]);
```

#### Method 2: Using the Admin Script
Edit `update_admin.php` and run:
```bash
php update_admin.php
```

### 5. Checking Permissions in Code

#### In Controllers
```php
// Check if user has specific permission
if (auth()->user()->hasPermission('user.create')) {
    // User can create users
}

// Check if user has any of the permissions
if (auth()->user()->hasAnyPermission(['user.create', 'user.edit'])) {
    // User can create or edit users
}

// Check if user has all permissions
if (auth()->user()->hasAllPermissions(['user.create', 'user.edit', 'user.delete'])) {
    // User has all user management permissions
}
```

#### In Blade Templates
```php
@if(auth()->user()->hasPermission('user.create'))
    <a href="{{ route('users.create') }}" class="btn btn-primary">Create User</a>
@endif

@if(auth()->user()->hasAnyPermission(['user.edit', 'user.delete']))
    <div class="action-buttons">
        @if(auth()->user()->hasPermission('user.edit'))
            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning">Edit</a>
        @endif
        @if(auth()->user()->hasPermission('user.delete'))
            <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
            </form>
        @endif
    </div>
@endif
```

### 6. Using Middleware for Route Protection

Create middleware to protect routes:

```php
// app/Http/Middleware/CheckPermission.php
public function handle($request, Closure $next, $permission)
{
    if (!auth()->user()->hasPermission($permission)) {
        abort(403, 'Unauthorized action.');
    }

    return $next($request);
}
```

Register in `app/Http/Kernel.php`:
```php
protected $routeMiddleware = [
    'permission' => \App\Http\Middleware\CheckPermission::class,
];
```

Use in routes:
```php
Route::middleware(['auth', 'permission:user.create'])->group(function () {
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
});
```

### 7. Admin Interface Management

The application provides web interfaces for managing roles and permissions:

- **Roles Management**: `/roles` - Create, edit, delete roles
- **User Management**: `/user-management` - Assign roles to users
- **Permission Management**: Through role editing interface

### 8. Best Practices

1. **Permission Naming Convention**: Use `resource.action` format (e.g., `user.create`, `product.edit`)

2. **Group Permissions**: Use the `group` field to organize permissions logically

3. **Role Hierarchy**: Create roles with increasing levels of access:
   - `user` - Basic access
   - `analyst` - Read-only access to data
   - `manager` - Can manage data
   - `admin` - Full access

4. **Regular Audits**: Periodically review and update roles and permissions

5. **Documentation**: Keep track of what each permission and role does

### 9. Troubleshooting

#### Common Issues:

1. **Permission not working**: Check if the permission exists and is assigned to the user's role
2. **Role not assigned**: Verify the user has a `role_id` in the database
3. **Cache issues**: Clear application cache after permission changes

#### Debug Commands:
```bash
# Check user permissions
php artisan tinker
>>> auth()->user()->permissions()->pluck('name');

# Check role permissions
>>> App\Models\Role::where('name', 'admin')->first()->permissions()->pluck('name');
```

### 10. Maintenance Commands

Create custom Artisan commands for common tasks:

```bash
# Create a command to list all permissions
php artisan make:command ListPermissions

# Create a command to assign role to user
php artisan make:command AssignRole
```

This system provides a flexible and maintainable way to manage access control in your Laravel application. 