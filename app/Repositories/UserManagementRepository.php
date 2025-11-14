<?php
namespace App\Repositories;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;

class UserManagementRepository
{
    public function dataSource(Request $request){
        $searchData = $request->get('searchData', null);

        Log::info('UserManagement DataTables request received', [
            'searchData' => $searchData,
            'user' => auth()->user() ? auth()->user()->id : 'not authenticated'
        ]);

        $users = User::query()
            ->with('role')
            ->when($searchData, function (Builder $query, $searchData) {
                return $query->where(function ($query) use ($searchData) {
                    $query->where('name', 'like', "%{$searchData}%")
                          ->orWhere('email', 'like', "%{$searchData}%")
                          ->orWhere('company_name', 'like', "%{$searchData}%")
                          ->orWhere('city', 'like', "%{$searchData}%")
                          ->orWhere('country', 'like', "%{$searchData}%");
                });
            })
            ->latest('created_at');

        try {
            $result = $this->userManagementDataTable($users);
            Log::info('UserManagement DataTables response generated successfully');
            return $result;
        } catch (\Exception $e) {
            Log::error('UserManagement DataTables Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'An error occurred while loading data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function userManagementDataTable($users)
    {
        $dataTable = DataTables::of($users)
            ->addColumn('email', function ($row) {
                return $row->email;
            })
            ->addColumn('role', function ($row) {
                if ($row->role) {
                    return '<span class="badge-lg text-primary rounded px-3 py-1" style="font-size: 12px;font-weight: 500;background-color: #5f63f221;">' . strtolower($row->role->name) . '</span>';
                } else {
                    return '<span class="badge-lg text-danger rounded px-3 py-1" style="font-size: 12px;font-weight: 500;background-color: #ff4d4f21;">Unassigned Role</span>';
                }
            })
            ->addColumn('company', function ($row) {
                return $row->company_name ?? 'N/A';
            })
            ->addColumn('location', function ($row) {
                if ($row->city && $row->country) {
                    return $row->city . ', ' . $row->country;
                } elseif($row->city) {
                    return $row->city;
                } elseif($row->country) {
                    return $row->country;
                } else {
                    return 'N/A';
                }
            })
            ->addColumn('created', function ($row) {
                return $row->created_at->format('M d, Y');
            })
            ->addColumn('actions', function ($row) {
                $deleteButton = '';
                if ($row->id !== auth()->id()) {
                    $deleteButton = '<span class="text-light">|</span>
                        <form action="' . route('user-management.destroy', $row) . '" method="POST" style="display:inline" class="delete-form">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="btn btn-link text-danger p-0 m-0 align-baseline mx-2" style="font-size:inherit;" title="Delete">
                                <i class="fas fa-trash m-0"></i>
                            </button>
                        </form>';
                }
                
                return '<div class="d-inline-flex gap-2 align-items-center">
                    <a href="' . route('user-management.show', $row) . '" class="mx-2" title="View"><i class="fas fa-eye"></i></a>
                    <span class="text-light">|</span>
                    <a href="' . route('user-management.edit', $row) . '" class="mx-2" title="Edit"><i class="fas fa-edit"></i></a>
                    <span class="text-light">|</span>
                    <a href="' . route('user-management.permissions', $row) . '" class="mx-2" title="Permissions"><i class="fas fa-key"></i></a>
                    ' . $deleteButton . '
                </div>';
            });

        return $dataTable->rawColumns(['role', 'actions'])->make(true);
    }
} 