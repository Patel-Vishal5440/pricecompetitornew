<?php
namespace App\Repositories;

use App\Models\Product;
use App\Models\Competitor;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTables;

class ProductRepository
{
    public function dataSource(Request $request){
        $searchData = $request->get('searchData', null);

        $product = Product::query()
            ->when($searchData, function (Builder $query, $searchData) {
                return $query->where(function ($query) use ($searchData) {
                    $query->where('name', 'like', "%{$searchData}%")
                          ->orWhere('default_code', 'like', "%{$searchData}%");
                });
            })
            ->latest('id');

        return $this->productDataTable($product);
    }

    public function productDataTable($product)
    {
        $competitors = Competitor::orderBy('id','DESC')->get();

        $dataTable = DataTables::of($product)
            ->addColumn('action', function ($product) {
                $syncButton = "<a href='javascript:void(0);' 
                    class='btn btn-icon btn-sm btn-light-primary sync-product m-2 text-light' 
                    data-product-id='{$product->odoo_id}'>
                    <i class='fas fa-sync fs-6 m-0'></i></a>";
                    
                return $syncButton;
            })
            ->addColumn('status', function ($product) {
                return "<span class='badge " . ($product->status ? 'bg-success' : 'bg-danger') . "'>" . 
                    ($product->status ? 'Active' : 'Inactive') . 
                    "</span>";  
            });

            foreach ($competitors as $competitor) {
                $dataTable->addColumn("competitor_link_{$competitor->id}", function ($product) use ($competitor) {
                    $competitor_url = $product->competitorPrices()
                        ->where('competitor_id', $competitor->id)
                        ->value('competitor_url');
                    return $competitor_url ?: ' ';
                });

                $dataTable->addColumn("competitor_price_{$competitor->id}", function ($product) use ($competitor) {
                    $price = $product->competitorPrices()
                        ->where('competitor_id', $competitor->id)
                        ->value('price');
                    return $price ? number_format($price, 2) : '0.00';
                });
            }
            
            return $dataTable->rawColumns(['action', 'status'])->make(true);
    }
}
