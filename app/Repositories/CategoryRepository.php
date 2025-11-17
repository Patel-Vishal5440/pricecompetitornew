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
            $borderColor = $category->status === 'active' ? '#28a745' : '#dc3545';
            $textColor = $category->status === 'active' ? '#28a745' : '#dc3545';
            $statusIcon = $category->status === 'active' ? 'fa-check-circle' : 'fa-times-circle';
            return "<span style='display: inline-block; padding: 6px 12px; border: 1px solid {$borderColor}; border-radius: 4px; background-color: transparent; color: {$textColor}; font-size: 12px; font-weight: 500; text-align: center; min-width: 80px;'>
                <i class='fas {$statusIcon} me-1'></i>" . ucfirst($category->status) . "
            </span>";
        })
        ->addColumn('products_count', function ($category) {
            $count = $category->products()->count();
            $borderColor = '#17a2b8';
            $textColor = '#17a2b8';
            return "<span style='display: inline-block; padding: 6px 12px; border: 1px solid {$borderColor}; border-radius: 4px; background-color: transparent; color: {$textColor}; font-size: 12px; font-weight: 500; text-align: center; min-width: 60px;'>
                <i class='fas fa-folder me-1'></i><br>{$count}
                <span style='font-size: 10px; color: {$textColor};'>Products</span>
            </span>";
        })
        ->addColumn('actions', function ($category) {
            $editButton = '';
            $deleteButton = '';
            
            if (auth()->user()->isAdmin() || auth()->user()->hasPermission('category.edit')) {
                $editButton = '<button type="button" class="btn btn-link p-0 m-0 align-baseline mx-2 edit-category-btn" 
                    style="font-size:inherit;" 
                    data-id="' . $category->id . '" 
                    data-name="' . htmlspecialchars($category->name) . '" 
                    data-description="' . htmlspecialchars($category->description ?? '') . '" 
                    data-status="' . $category->status . '" 
                    title="Edit Category">
                    <i class="fas fa-edit m-0"></i>
                </button>';
            }
            
            if (auth()->user()->isAdmin() || auth()->user()->hasPermission('category.delete')) {
                $deleteButton = '<span class="text-light">|</span>
                    <button type="button" class="btn btn-link text-danger p-0 m-0 align-baseline mx-2 delete-category-btn" 
                        style="font-size:inherit;" 
                        data-id="' . $category->id . '" 
                        data-name="' . htmlspecialchars($category->name) . '" 
                        title="Delete Category">
                        <i class="fas fa-trash m-0"></i>
                    </button>';
            }
            
            return '<div class="d-inline-flex gap-2 align-items-center">' . $editButton . $deleteButton . '</div>';
        });

        return $dataTable->rawColumns(['name', 'description', 'status', 'products_count', 'actions'])->make(true);
    }
}

