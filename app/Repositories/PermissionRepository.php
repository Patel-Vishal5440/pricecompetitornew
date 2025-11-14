<?php
namespace App\Repositories;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PermissionRepository
{
    public function dataSource(Request $request){
        $searchData = $request->get('searchData', null);

        Log::info('Permission DataTables request received', [
            'searchData' => $searchData,
            'user' => auth()->user() ? auth()->user()->id : 'not authenticated'
        ]);

        $permissions = Permission::query()
            ->with('roles')
            ->when($searchData, function (Builder $query, $searchData) {
                return $query->where(function ($query) use ($searchData) {
                    $query->where('name', 'like', "%{$searchData}%")
                          ->orWhere('description', 'like', "%{$searchData}%")
                          ->orWhere('group', 'like', "%{$searchData}%");
                });
            })
            ->latest('created_at');

        try {
            $result = $this->permissionDataTable($permissions);
            Log::info('Permission DataTables response generated successfully');
            return $result;
        } catch (\Exception $e) {
            Log::error('Permission DataTables Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'An error occurred while loading data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function permissionDataTable($permissions)
    {
        $dataTable = DataTables::of($permissions)
            ->addColumn('name', function ($permission) {
                return '<div class="userDatatable-content">' . $permission->name . '</div>';
            })
            ->addColumn('description', function ($permission) {
                return '<div class="userDatatable-content">' . ($permission->description ?? '-') . '</div>';
            })
            ->addColumn('group', function ($permission) {
                return '<div class="userDatatable-content">' . ($permission->group ?? '-') . '</div>';
            })
            ->addColumn('assigned_roles', function ($permission) {
                if ($permission->roles->count()) {
                    return '<div class="userDatatable-content">' . $permission->roles->pluck('name')->join(', ') . '</div>';
                }
                return '<div class="userDatatable-content">-</div>';
            })
            ->addColumn('status', function ($permission) {
                $color = $permission->is_active ? '#198754' : '#dc3545';
                $bgColor = $permission->is_active ? '#30ff302b' : '#ffcccc85';
                return '<div class="userDatatable-content">
                    <span class="badge-lg rounded px-3 py-1" 
                          style="font-size: 12px; font-weight: 500; color: ' . $color . '; background-color: ' . $bgColor . ';">
                        ' . ($permission->is_active ? 'Active' : 'Inactive') . '
                    </span>
                </div>';
            })
            ->addColumn('actions', function ($permission) {
                $actions = '<div class="d-inline-flex gap-2 align-items-center">';
                $actions .= '<a href="' . route('permissions.show', $permission) . '" title="View" class="mx-2"><i class="fas fa-eye"></i></a>';
                $actions .= '<span class="text-light">|</span>';
                $actions .= '<a href="' . route('permissions.edit', $permission) . '" title="Edit" class="mx-2"><i class="fas fa-edit"></i></a>';
                $actions .= '<span class="text-light">|</span>';
                
                // Only show delete if no roles are assigned
                if ($permission->roles->count() == 0) {
                    $actions .= '<form action="' . route('permissions.destroy', $permission) . '" method="POST" style="display:inline" class="delete-form">';
                    $actions .= csrf_field();
                    $actions .= method_field('DELETE');
                    $actions .= '<button type="submit" class="btn btn-link text-danger p-0 m-0 align-baseline mx-2" style="font-size:inherit;" title="Delete">';
                    $actions .= '<i class="fas fa-trash m-0"></i>';
                    $actions .= '</button></form>';
                    $actions .= '<span class="text-light">|</span>';
                }
                
                $actions .= '<form action="' . route('permissions.toggle-status', $permission) . '" method="POST" style="display:inline">';
                $actions .= csrf_field();
                $actions .= method_field('PATCH');
                $actions .= '<button type="submit" class="btn btn-link p-0 m-0 align-baseline mx-2" style="font-size:inherit;" title="' . ($permission->is_active ? 'Deactivate' : 'Activate') . '">';
                $actions .= '<i class="fas fa-' . ($permission->is_active ? 'ban' : 'check') . '"></i>';
                $actions .= '</button></form>';
                $actions .= '</div>';
                return $actions;
            });

        return $dataTable->rawColumns(['name', 'description', 'group', 'assigned_roles', 'status', 'actions'])->make(true);
    }
} 