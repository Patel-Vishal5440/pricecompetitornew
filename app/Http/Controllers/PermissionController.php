<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Repositories\PermissionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    protected $permissionRepository;

    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(PermissionRepository $permissionRepository, Request $request)
    {
        $pageTitle = 'Permissions Management';
        $pageDescription = 'Manage system permissions';

        if (request()->ajax()) {
            $this->permissionRepository = $permissionRepository;
            return $this->permissionRepository->dataSource($request);
        }

        return view('permissions.index', [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Create Permission';
        $pageDescription = 'Create a new system permission';
        
        $permission = null; // Pass null for create operation
        
        return view('permissions.create', compact('pageTitle', 'pageDescription', 'permission'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions,name',
            'description' => 'nullable|string',
            'group' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Permission::create([
            'name' => $request->name,
            'description' => $request->description,
            'group' => $request->group,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('permissions.index')
            ->with('permission_created_success', 'Permission created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        $pageTitle = 'Permission Details';
        $pageDescription = 'View permission information and assigned roles';
        
        $permission->load('roles');
        
        return view('permissions.show', compact('pageTitle', 'pageDescription', 'permission'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        $pageTitle = 'Edit Permission';
        $pageDescription = 'Edit permission information';
        
        return view('permissions.create', compact('pageTitle', 'pageDescription', 'permission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'description' => 'nullable|string',
            'group' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $permission->update([
            'name' => $request->name,
            'description' => $request->description,
            'group' => $request->group,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        // Check if permission is assigned to any roles
        if ($permission->roles()->count() > 0) {
            return redirect()->route('permissions.index')
                ->with('error', 'Cannot delete permission that is assigned to roles.');
        }

        $permission->delete();

        return redirect()->route('permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }

    /**
     * Toggle permission active status
     */
    public function toggleStatus(Permission $permission)
    {
        $permission->update([
            'is_active' => !$permission->is_active
        ]);

        return redirect()->route('permissions.index')
            ->with('success', 'Permission status updated successfully.');
    }
}
