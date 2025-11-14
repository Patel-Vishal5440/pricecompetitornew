<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Console\Command;

class ManageRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:manage 
                            {action : Action to perform (list-roles, list-permissions, assign-role, create-role, create-permission)}
                            {--user= : User email for assign-role action}
                            {--role= : Role name for assign-role action}
                            {--name= : Name for create-role or create-permission action}
                            {--description= : Description for create-role or create-permission action}
                            {--group= : Group for create-permission action}
                            {--permissions=* : Permissions for create-role action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage roles and permissions in the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'list-roles':
                $this->listRoles();
                break;
            case 'list-permissions':
                $this->listPermissions();
                break;
            case 'assign-role':
                $this->assignRole();
                break;
            case 'create-role':
                $this->createRole();
                break;
            case 'create-permission':
                $this->createPermission();
                break;
            default:
                $this->error("Unknown action: {$action}");
                $this->info('Available actions: list-roles, list-permissions, assign-role, create-role, create-permission');
                return 1;
        }

        return 0;
    }

    /**
     * List all roles with their permissions
     */
    private function listRoles()
    {
        $this->info('=== ROLES ===');
        
        $roles = Role::with('permissions')->get();
        
        if ($roles->isEmpty()) {
            $this->warn('No roles found.');
            return;
        }

        foreach ($roles as $role) {
            $this->info("Role: {$role->name}");
            $this->line("Description: {$role->description}");
            $this->line("Active: " . ($role->is_active ? 'Yes' : 'No'));
            $this->line("Users: " . $role->users()->count());
            
            if ($role->permissions->isNotEmpty()) {
                $this->line("Permissions:");
                foreach ($role->permissions as $permission) {
                    $this->line("  - {$permission->name} ({$permission->group})");
                }
            } else {
                $this->line("Permissions: None");
            }
            $this->line('');
        }
    }

    /**
     * List all permissions grouped by their group
     */
    private function listPermissions()
    {
        $this->info('=== PERMISSIONS ===');
        
        $permissions = Permission::orderBy('group')->orderBy('name')->get();
        
        if ($permissions->isEmpty()) {
            $this->warn('No permissions found.');
            return;
        }

        $grouped = $permissions->groupBy('group');
        
        foreach ($grouped as $group => $groupPermissions) {
            $this->info("Group: {$group}");
            foreach ($groupPermissions as $permission) {
                $this->line("  - {$permission->name}: {$permission->description}");
            }
            $this->line('');
        }
    }

    /**
     * Assign a role to a user
     */
    private function assignRole()
    {
        $userEmail = $this->option('user');
        $roleName = $this->option('role');

        if (!$userEmail || !$roleName) {
            $this->error('Both --user and --role options are required for assign-role action.');
            return;
        }

        $user = User::where('email', $userEmail)->first();
        if (!$user) {
            $this->error("User with email '{$userEmail}' not found.");
            return;
        }

        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            $this->error("Role '{$roleName}' not found.");
            return;
        }

        $user->update(['role_id' => $role->id]);
        $this->info("Role '{$roleName}' assigned to user '{$userEmail}' successfully.");
    }

    /**
     * Create a new role
     */
    private function createRole()
    {
        $name = $this->option('name');
        $description = $this->option('description');
        $permissions = $this->option('permissions');

        if (!$name) {
            $this->error('--name option is required for create-role action.');
            return;
        }

        // Check if role already exists
        if (Role::where('name', $name)->exists()) {
            $this->error("Role '{$name}' already exists.");
            return;
        }

        $role = Role::create([
            'name' => $name,
            'description' => $description ?? "Role: {$name}",
            'is_active' => true
        ]);

        // Assign permissions if provided
        if (!empty($permissions)) {
            $permissionIds = Permission::whereIn('name', $permissions)->pluck('id');
            if ($permissionIds->isNotEmpty()) {
                $role->assignPermissions($permissionIds);
                $this->info("Permissions assigned: " . implode(', ', $permissions));
            } else {
                $this->warn("No valid permissions found from the provided list.");
            }
        }

        $this->info("Role '{$name}' created successfully.");
    }

    /**
     * Create a new permission
     */
    private function createPermission()
    {
        $name = $this->option('name');
        $description = $this->option('description');
        $group = $this->option('group');

        if (!$name) {
            $this->error('--name option is required for create-permission action.');
            return;
        }

        // Check if permission already exists
        if (Permission::where('name', $name)->exists()) {
            $this->error("Permission '{$name}' already exists.");
            return;
        }

        Permission::create([
            'name' => $name,
            'description' => $description ?? "Permission: {$name}",
            'group' => $group ?? 'General',
            'is_active' => true
        ]);

        $this->info("Permission '{$name}' created successfully.");
    }
}
