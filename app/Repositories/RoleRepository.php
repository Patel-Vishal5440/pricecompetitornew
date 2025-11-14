<?php
namespace App\Repositories;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;

class RoleRepository
{
    public function dataSource(Request $request){
        $searchData = $request->get('searchData', null);

        Log::info('Role DataTables request received', [
            'searchData' => $searchData,
            'user' => auth()->user() ? auth()->user()->id : 'not authenticated'
        ]);

        $roles = Role::query()
            ->with(['permissions', 'users'])
            ->when($searchData, function (Builder $query, $searchData) {
                return $query->where(function ($query) use ($searchData) {
                    $query->where('name', 'like', "%{$searchData}%")
                          ->orWhere('description', 'like', "%{$searchData}%");
                });
            })
            ->latest('created_at');

        try {
            $result = $this->roleDataTable($roles);
            Log::info('Role DataTables response generated successfully');
            return $result;
        } catch (\Exception $e) {
            Log::error('Role DataTables Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'An error occurred while loading data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function roleDataTable($roles)
    {
        $dataTable = DataTables::of($roles)
            ->addColumn('name', function ($row) {
                return ucfirst($row->name);
            })
            ->addColumn('description', function ($row) {
                return $row->description ?? '-';
            })
            ->addColumn('permissions', function ($row) {
                return $row->permissions->count();
            })
            ->addColumn('users', function ($row) {
                return $row->users->count();
            })
            ->addColumn('status', function ($row) {
                $color = $row->is_active ? '#198754' : '#dc3545';
                $bgColor = $row->is_active ? '#30ff302b' : '#ffcccc85';
                return '<span class="badge-lg rounded px-3 py-1" style="font-size: 12px; font-weight: 500; color: ' . $color . '; background-color: ' . $bgColor . ';">' . 
                       ($row->is_active ? 'Active' : 'Inactive') . '</span>';
            })
            ->addColumn('actions', function ($row) {
                $deleteButton = '';
                if ($row->users->count() == 0) {
                    $deleteButton = '<form action="' . route('roles.destroy', $row) . '" method="POST" style="display:inline" class="delete-form">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-link text-danger p-0 m-0 align-baseline mx-2" style="font-size:inherit;" title="Delete">
                            <i class="fas fa-trash m-0"></i>
                        </button>
                    </form>
                    <span class="text-light">|</span>';
                }
                
                return '<div class="d-inline-flex gap-2 align-items-center">
                    <a href="' . route('roles.show', $row) . '" title="View" class="mx-2">
                        <i class="fas fa-eye"></i>
                    </a>
                    <span class="text-light">|</span>
                    <a href="' . route('roles.edit', $row) . '" title="Edit" class="mx-2">
                        <i class="fas fa-edit"></i>
                    </a>
                    <span class="text-light">|</span>
                    ' . $deleteButton . '
                    <form action="' . route('roles.toggle-status', $row) . '" method="POST" style="display:inline">
                        ' . csrf_field() . '
                        ' . method_field('PATCH') . '
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline mx-2" style="font-size:inherit;" title="' . ($row->is_active ? 'Deactivate' : 'Activate') . '">
                            <i class="fas fa-' . ($row->is_active ? 'ban' : 'check') . '"></i>
                        </button>
                    </form>
                </div>';
            });

        return $dataTable->rawColumns(['status', 'actions'])->make(true);
    }
} 