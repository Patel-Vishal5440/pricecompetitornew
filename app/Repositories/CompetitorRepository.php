<?php
namespace App\Repositories;

use App\Models\Competitor;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;

class CompetitorRepository
{
    public function dataSource(Request $request){
        $searchData = $request->get('searchData', null);

        Log::info('DataTables request received', [
            'searchData' => $searchData,
            'user' => auth()->user() ? auth()->user()->id : 'not authenticated'
        ]);

        $competitor = Competitor::query()
            ->when($searchData, function (Builder $query, $searchData) {
                return $query->where(function ($query) use ($searchData) {
                    $query->where('name', 'like', "%{$searchData}%")
                          ->orWhere('website', 'like', "%{$searchData}%")
                          ->orWhere('shortname', 'like', "%{$searchData}%")
                          ->orWhere('price_class_name', 'like', "%{$searchData}%");
                });
            })
            ->latest('id');

        try {
            $result = $this->competitorDataTable($competitor);
            Log::info('DataTables response generated successfully');
            return $result;
        } catch (\Exception $e) {
            Log::error('DataTables Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'An error occurred while loading data: ' . $e->getMessage()
            ], 500);
        }
    }


    public function competitorDataTable($competitor)
    {
        $dataTable = DataTables::of($competitor)
        ->addColumn('website_link', function ($competitor) {
            if ($competitor->website) {
                return "<a href='{$competitor->website}' target='_blank' class='text-decoration-none'>
                    {$competitor->website}
                </a>";
            }
            return "<span class='text-muted'>N/A</span>";
        })
        ->editColumn('name', function ($competitor) {
            return $competitor->name ?? 'N/A';
        })
        ->editColumn('shortname', function ($competitor) {
            return $competitor->shortname ?? 'N/A';
        })
        ->editColumn('price_class_name', function ($competitor) {
            return $competitor->price_class_name ?? 'N/A';
        })
        ->addColumn('actions', function ($competitor) {
            $deleteButton = '<span class="text-light">|</span>
                    <form action="' . route('competitor.destroy', $competitor) . '" method="POST" style="display:inline" class="delete-form">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-link text-danger p-0 m-0 align-baseline mx-2" style="font-size:inherit;" title="Delete">
                            <i class="fas fa-trash m-0"></i>
                        </button>
                    </form>';
            
            return '<div class="d-inline-flex gap-2 align-items-center">
                <a href="' . route('competitor.edit', $competitor) . '" class="mx-2" title="Edit"><i class="fas fa-edit"></i></a>
                ' . $deleteButton . '
            </div>';
        });

        return $dataTable->rawColumns(['name', 'website_link', 'shortname', 'price_class_name', 'actions'])->make(true);
    }
}