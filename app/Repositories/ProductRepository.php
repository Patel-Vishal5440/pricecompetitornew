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
        $category = $request->get('category', null);
        $competitorId = $request->get('competitor_id', null);
        $priceSort = $request->get('price_sort', null); // 'low_to_high' or 'high_to_low'

        $product = Product::query()
            ->when($searchData, function (Builder $query, $searchData) {
                return $query->where(function ($query) use ($searchData) {
                    $query->where('name', 'like', "%{$searchData}%")
                          ->orWhere('default_code', 'like', "%{$searchData}%");
                });
            })
            ->when($category, function (Builder $query, $category) {
                return $query->where('category', $category);
            })
            ->when($competitorId, function (Builder $query, $competitorId) {
                return $query->whereHas('competitorPrices', function ($q) use ($competitorId) {
                    $q->where('competitor_id', $competitorId);
                });
            });

        // Handle price sorting by competitor price
        if ($priceSort && $competitorId) {
            $product->leftJoin('product_competitor_prices', function($join) use ($competitorId) {
                $join->on('products.id', '=', 'product_competitor_prices.product_id')
                     ->where('product_competitor_prices.competitor_id', '=', $competitorId);
            })
            ->select('products.*', 'product_competitor_prices.price as competitor_price')
            ->distinct()
            ->orderBy('product_competitor_prices.price', $priceSort === 'high_to_low' ? 'desc' : 'asc');
        } else {
            $product->latest('id');
        }

        return $this->productDataTable($product, $priceSort, $competitorId);
    }

    public function productDataTable($product, $priceSort = null, $competitorId = null)
    {
        $competitors = Competitor::orderBy('id','DESC')->get();
        $dataTable = DataTables::of($product);

        $dataTable->addColumn('action', function ($product) {
                // Only show sync button if product has a valid Odoo ID (not manually created)
                if ($product->odoo_id && !$this->isManualProduct($product->odoo_id)) {
                    $syncButton = "<a href='javascript:void(0);' 
                        class='btn btn-icon btn-sm btn-light-primary sync-product m-2 text-light' 
                        data-product-id='{$product->odoo_id}'>
                        <i class='fas fa-sync fs-6 m-0'></i></a>";
                    return $syncButton;
                }
                return '<span class="text-muted">Manual</span>';
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

    /**
     * Check if product is manually created (has special odoo_id format starting with 002)
     */
    private function isManualProduct($odooId)
    {
        if (is_null($odooId)) {
            return true;
        }
        // Convert to string and check if odoo_id starts with "002" which indicates manual product
        $odooIdString = (string) $odooId;
        return strpos($odooIdString, '002') === 0;
    }
}
