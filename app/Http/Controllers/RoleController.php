<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Repositories\RoleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    protected $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageTitle = 'Roles Management';
        $pageDescription = 'Manage system roles and permissions';
        
        // If AJAX request, return DataTables response
        if ($request->ajax()) {
            return $this->roleRepository->dataSource($request);
        }
        
        // For non-AJAX requests, return the view with initial data
        $search = $request->input('search');
        $perPage = $request->input('per_page', 15);
        
        $query = Role::with(['permissions', 'users']);
        
        // Search functionality
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $roles = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        // Get total count for display (without search filter)
        $totalRoles = Role::count();
        $filteredCount = $roles->total();
        
        return view('roles.index', compact(
            'pageTitle', 
            'pageDescription', 
            'roles', 
            'search', 
            'perPage',
            'totalRoles',
            'filteredCount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Create Role';
        $pageDescription = 'Create a new system role';
        
        $permissions = Permission::where('is_active', true)->get();
        $role = null; // Pass null for create operation
        
        return view('roles.create', compact('pageTitle', 'pageDescription', 'permissions', 'role'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:255|unique:roles,name',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true
        ]);

        if ($request->has('permissions')) {
            $role->assignPermissions($request->permissions);
        }

        return redirect()->route('roles.index')
            ->with('role_created_success', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $pageTitle = 'Role Details';
        $pageDescription = 'View role information and permissions';
        
        $role->load('permissions', 'users');
        
        return view('roles.show', compact('pageTitle', 'pageDescription', 'role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $pageTitle = 'Edit Role';
        $pageDescription = 'Edit role information and permissions';
        
        $permissions = Permission::where('is_active', true)->get();
        $role->load('permissions');
        
        return view('roles.create', compact('pageTitle', 'pageDescription', 'role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if ($request->has('permissions')) {
            $role->assignPermissions($request->permissions);
        } else {
            $role->permissions()->detach();
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Check if role has users
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete role that has assigned users.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    /**
     * Toggle role active status
     */
    public function toggleStatus(Role $role)
    {
        $role->update([
            'is_active' => !$role->is_active
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Role status updated successfully.');
    }
}
