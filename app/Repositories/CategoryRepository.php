<?php
namespace App\Repositories;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;

class CategoryRepository
{
    public function dataSource(Request $request){
        $searchData = $request->get('searchData', null);

        Log::info('Category DataTables request received', [
            'searchData' => $searchData,
            'user' => auth()->user() ? auth()->user()->id : 'not authenticated'
        ]);

        $category = Category::query()
            ->when($searchData, function (Builder $query, $searchData) {
                return $query->where(function ($query) use ($searchData) {
                    $query->where('name', 'like', "%{$searchData}%")
                          ->orWhere('description', 'like', "%{$searchData}%");
                });
            })
            ->latest('id');

        try {
            $result = $this->categoryDataTable($category);
            Log::info('Category DataTables response generated successfully');
            return $result;
        } catch (\Exception $e) {
            Log::error('Category DataTables Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'An error occurred while loading data: ' . $e->getMessage()
            ], 500);
        }
    }


    public function categoryDataTable($category)
    {
        $dataTable = DataTables::of($category)
        ->editColumn('name', function ($category) {
            return $category->name ?? 'N/A';
        })
        ->editColumn('description', function ($category) {
            return $category->description ?? 'N/A';
        })
        ->addColumn('status', function ($category) {
            $statusClass = $category->status === 'active' ? 'bg-success' : 'bg-danger';
            $statusIcon = $category->status === 'active' ? 'fa-check-circle' : 'fa-times-circle';
            return "<span class='badge {$statusClass} px-3 py-2'>
                <i class='fas {$statusIcon} me-1'></i>" . ucfirst($category->status) . "
            </span>";
        })
        ->addColumn('products_count', function ($category) {
            $count = $category->products()->count();
            return "<span class='badge bg-info px-3 py-2'>
                <i class='fas fa-folder me-1'></i>{$count}
            </span>";
        })
        ->addColumn('actions', function ($category) {
            $editButton = '';
            $deleteButton = '';
            
            if (auth()->user()->isAdmin() || auth()->user()->hasPermission('category.edit')) {
                $editButton = '<button type="button" class="btn btn-sm btn-warning edit-category-btn" 
                    data-id="' . $category->id . '" 
                    data-name="' . htmlspecialchars($category->name) . '" 
                    data-description="' . htmlspecialchars($category->description ?? '') . '" 
                    data-status="' . $category->status . '" 
                    title="Edit Category">
                    <i class="fas fa-edit"></i>
                </button>';
            }
            
            if (auth()->user()->isAdmin() || auth()->user()->hasPermission('category.delete')) {
                $deleteButton = '<button type="button" class="btn btn-sm btn-danger delete-category-btn" 
                    data-id="' . $category->id . '" 
                    data-name="' . htmlspecialchars($category->name) . '" 
                    title="Delete Category">
                    <i class="fas fa-trash"></i>
                </button>';
            }
            
            return '<div class="d-flex justify-content-center gap-2">' . $editButton . $deleteButton . '</div>';
        });

        return $dataTable->rawColumns(['name', 'description', 'status', 'products_count', 'actions'])->make(true);
    }
}

