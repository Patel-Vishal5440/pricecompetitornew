<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Repositories\UserManagementRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserManagementController extends Controller
{
    protected $userManagementRepository;

    public function __construct(UserManagementRepository $userManagementRepository)
    {
        $this->userManagementRepository = $userManagementRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageTitle = 'User Management';
        $pageDescription = 'Manage system users and their roles';
        
        // If AJAX request, return DataTables response
        if ($request->ajax()) {
            return $this->userManagementRepository->dataSource($request);
        }
        
        // For non-AJAX requests, return the view with initial data
        $search = $request->input('search');
        $perPage = $request->input('per_page', 15);
        
        $query = User::with('role');
        
        // Handle search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
            });
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        // Get total count for display (without search filter)
        $totalUsers = User::count();
        $filteredCount = $users->total();
        
        return view('user-management.index', compact(
            'pageTitle', 
            'pageDescription', 
            'users', 
            'search', 
            'perPage',
            'totalUsers',
            'filteredCount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Create User';
        $pageDescription = 'Create a new system user';
        
        $roles = Role::where('is_active', true)->get();
        
        return view('user-management.create', compact('pageTitle', 'pageDescription', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'nullable|exists:roles,id',
            'phone_number' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'bio' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'phone_number' => $request->phone_number,
            'country' => $request->country,
            'city' => $request->city,
            'company_name' => $request->company_name,
            'website' => $request->website,
            'bio' => $request->bio,
        ]);

        return redirect()->route('user-management.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $pageTitle = 'User Details';
        $pageDescription = 'View user information and permissions';
        
        $user->load('role.permissions');
        
        return view('user-management.show', compact('pageTitle', 'pageDescription', 'user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $pageTitle = 'Edit User';
        $pageDescription = 'Edit user information and role';
        
        $roles = Role::where('is_active', true)->get();
        
        return view('user-management.create', compact('pageTitle', 'pageDescription', 'user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'nullable|exists:roles,id',
            'phone_number' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'bio' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'phone_number' => $request->phone_number,
            'country' => $request->country,
            'city' => $request->city,
            'company_name' => $request->company_name,
            'website' => $request->website,
            'bio' => $request->bio,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('user-management.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting the current user
        if ($user->id === auth()->id()) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete your own account.'
                ], 403);
            }
            return redirect()->route('user-management.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.'
            ]);
        }   

        return redirect()->route('user-management.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user)
    {
        // Prevent deactivating the current user
        if ($user->id === auth()->id()) {
            return redirect()->route('user-management.index')
                ->with('error', 'You cannot deactivate your own account.');
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        return redirect()->route('user-management.index')
            ->with('success', 'User status updated successfully.');
    }

    /**
     * Show user permissions
     */
    public function permissions(User $user)
    {
        $pageTitle = 'User Permission';
        $pageDescription = 'View user permission through their role';
        
        $user->load('role.permissions');
        
        return view('user-management.permissions', compact('pageTitle', 'pageDescription', 'user'));
    }
}
