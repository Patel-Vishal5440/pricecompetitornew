<?php
namespace App\Repositories;

use App\Models\ActivityFeed;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;

class PriceHistoryRepository
{
    public function dataSource(Request $request){
        $searchData = $request->get('searchData', null);

        Log::info('PriceHistory DataTables request received', [
            'searchData' => $searchData,
            'user' => auth()->user() ? auth()->user()->id : 'not authenticated'
        ]);

        $priceHistory = ActivityFeed::query()
            ->with(['product', 'user'])
            ->when($searchData, function (Builder $query, $searchData) {
                return $query->where(function ($query) use ($searchData) {
                    $query->where('price_old', 'like', "%{$searchData}%")
                          ->orWhere('price_new', 'like', "%{$searchData}%")
                          ->orWhere('type', 'like', "%{$searchData}%")
                          ->orWhere('model_id', 'like', "%{$searchData}%")
                          ->orWhere('user_id', 'like', "%{$searchData}%")
                          // Filter by product name
                          ->orWhereHas('product', function($q2) use ($searchData) {
                              $q2->where('name', 'like', "%{$searchData}%");
                          })
                          // Filter by user name
                          ->orWhereHas('user', function($q3) use ($searchData) {
                              $q3->where('name', 'like', "%{$searchData}%");
                          });
                });
            })
            ->latest('created_at');

        try {
            $result = $this->priceHistoryDataTable($priceHistory);
            Log::info('PriceHistory DataTables response generated successfully');
            return $result;
        } catch (\Exception $e) {
            Log::error('PriceHistory DataTables Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'An error occurred while loading data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function priceHistoryDataTable($priceHistory)
    {
        $dataTable = DataTables::of($priceHistory)
            ->addColumn('date', function ($row) {
                return date('m/d/Y H:i', strtotime($row->created_at));
            })
            ->addColumn('product_name', function ($row) {
                return $row->product ? $row->product->name : 'N/A';
            })
            ->addColumn('price_old', function ($row) {
                return '$' . number_format($row->price_old ?? 0, 2);
            })
            ->addColumn('price_new', function ($row) {
                $priceClass = '';
                if ($row->price_new > $row->price_old) {
                    $priceClass = 'text-danger';
                } elseif ($row->price_new < $row->price_old) {
                    $priceClass = 'text-success';
                }
                
                $arrow = '';
                if ($row->price_new > $row->price_old) {
                    $arrow = ' <i class="la la-arrow-up"></i>';
                } elseif ($row->price_new < $row->price_old) {
                    $arrow = ' <i class="la la-arrow-down"></i>';
                }
                
                return '<span class="' . $priceClass . '">$' . number_format($row->price_new ?? 0, 2) . $arrow . '</span>';
            })
            ->addColumn('performed_by', function ($row) {
                return $row->user ? $row->user->name : 'System';
            });

        return $dataTable->rawColumns(['price_new'])->make(true);
    }
} 