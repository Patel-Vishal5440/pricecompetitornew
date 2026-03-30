<?php
namespace App\Repositories;

use App\Models\Product;
use App\Models\Competitor;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTables;

class ProductRepository
{
    private function resolveCategoryName($product): string
    {
        if (!empty($product->category_id)) {
            // Prefer relation when available, but avoid relation/attribute naming collision on "category".
            $relationValue = $product->getRelationValue('category');
            if ($relationValue && !empty($relationValue->name)) {
                return $relationValue->name;
            }

            $category = Category::find($product->category_id);
            if ($category && !empty($category->name)) {
                return $category->name;
            }
        }

        if (!empty($product->category) && is_string($product->category)) {
            return $product->category;
        }

        return 'No Category';
    }

    public function dataSource(Request $request){
        $searchData = $request->get('searchData', null); // backward compatibility
        $filterName = $request->get('filter_name', null);
        $filterSku = $request->get('filter_sku', null);
        $category = $request->get('category', null);
        $competitorId = $request->get('competitor_id', null);
        $priceSort = $request->get('price_sort', null); // 'low_to_high' or 'high_to_low'
        $productPriceSort = $request->get('product_price_sort', null); // 'low_to_high' or 'high_to_low'
        $priceComparison = $request->get('price_comparison', null); // 'higher' or 'lower'
        $isExport = $request->get('export', false);
        $length = $request->get('length', 10);

        $product = Product::query()
            ->when($searchData, function (Builder $query, $searchData) {
                return $query->where(function ($query) use ($searchData) {
                    $query->where('name', 'like', "%{$searchData}%")
                          ->orWhere('default_code', 'like', "%{$searchData}%");
                });
            })
            ->when($filterName, function (Builder $query, $filterName) {
                return $query->where('name', 'like', "%{$filterName}%");
            })
            ->when($filterSku, function (Builder $query, $filterSku) {
                return $query->where('default_code', 'like', "%{$filterSku}%");
            })
            ->when($category, function (Builder $query, $category) {
                // Check if category is numeric (category_id) or string (legacy category name)
                if (is_numeric($category)) {
                    return $query->where('category_id', $category);
                } else {
                    return $query->where('category', $category);
                }
            })
            ->when($competitorId, function (Builder $query, $competitorId) {
                return $query->whereHas('competitorPrices', function ($q) use ($competitorId) {
                    $q->where('competitor_id', $competitorId);
                });
            });

        // Determine if we need to join the competitor prices table
        // Only join when we have a specific competitor (for price comparison or sorting)
        // For "All Competitors" comparison, we use whereHas instead of join
        $needsJoin = ($priceComparison && $competitorId) || ($priceSort && $competitorId);
        
        // Handle price comparison filter (higher/lower than product's own price)
        if ($priceComparison) {
            if ($competitorId) {
                // Specific competitor comparison
                $product->leftJoin('product_competitor_prices', function($join) use ($competitorId) {
                    $join->on('products.id', '=', 'product_competitor_prices.product_id')
                         ->where('product_competitor_prices.competitor_id', '=', $competitorId)
                         ->whereNotNull('product_competitor_prices.price');
                })
                ->whereNotNull('products.list_price')
                ->whereNotNull('product_competitor_prices.price');
                
                if ($priceComparison === 'higher') {
                    // Competitor price is higher than product's own price
                    $product->whereColumn('product_competitor_prices.price', '>', 'products.list_price');
                } elseif ($priceComparison === 'lower') {
                    // Competitor price is lower than product's own price
                    $product->whereColumn('product_competitor_prices.price', '<', 'products.list_price');
                }
            } else {
                // All competitors comparison - check if ANY competitor meets the condition
                $product->whereNotNull('products.list_price')
                        ->whereHas('competitorPrices', function($q) use ($priceComparison) {
                            $q->whereNotNull('price');
                            if ($priceComparison === 'higher') {
                                // At least one competitor price is higher than product's own price
                                $q->whereRaw('product_competitor_prices.price > (SELECT list_price FROM products WHERE products.id = product_competitor_prices.product_id)');
                            } elseif ($priceComparison === 'lower') {
                                // At least one competitor price is lower than product's own price
                                $q->whereRaw('product_competitor_prices.price < (SELECT list_price FROM products WHERE products.id = product_competitor_prices.product_id)');
                            }
                        });
            }
        }

        // Handle price sorting by competitor price
        if ($priceSort && $competitorId) {
            // If not already joined for price comparison, join now
            if (!$priceComparison) {
                $product->leftJoin('product_competitor_prices', function($join) use ($competitorId) {
                    $join->on('products.id', '=', 'product_competitor_prices.product_id')
                         ->where('product_competitor_prices.competitor_id', '=', $competitorId);
                });
            }
        }

        // Apply select and distinct if we did a join (must be before orderBy)
        if ($needsJoin) {
            $product->select('products.*', 'product_competitor_prices.price as competitor_price')
                    ->with('category')
                    ->distinct();
        } else {
            $product->with('category');
        }

        // Handle price sorting by competitor price (after select)
        if ($priceSort && $competitorId) {
            // Ensure numeric sorting and push NULLs last
            if ($priceSort === 'high_to_low') {
                $product->orderByRaw('product_competitor_prices.price IS NULL ASC, CAST(product_competitor_prices.price AS DECIMAL(15,4)) DESC');
            } else {
                $product->orderByRaw('product_competitor_prices.price IS NULL ASC, CAST(product_competitor_prices.price AS DECIMAL(15,4)) ASC');
            }
        } elseif ($productPriceSort) {
            // Ensure numeric sorting and push NULLs last
            if ($productPriceSort === 'high_to_low') {
                $product->orderByRaw('products.list_price IS NULL ASC, CAST(products.list_price AS DECIMAL(15,4)) DESC');
            } else {
                $product->orderByRaw('products.list_price IS NULL ASC, CAST(products.list_price AS DECIMAL(15,4)) ASC');
            }
        } else {
            // Default ordering if no price sort is applied
            $product->latest('id');
        }

        return $this->productDataTable($product, $priceSort, $competitorId, $isExport, $length);
    }

    public function productDataTable($product, $priceSort = null, $competitorId = null, $isExport = false, $length = 10)
    {
        $competitors = Competitor::orderBy('id','DESC')->get();
        
        // For export, get all records without pagination
        if ($isExport && $length == -1) {
            $dataTable = DataTables::of($product->get());
        } else {
            $dataTable = DataTables::of($product);
        }

        // Add category display column - matching category module status format
        $dataTable->addColumn('category_display', function ($product) {
            try {
                $categoryName = $this->resolveCategoryName($product);
                
                // Match category module status display format - rectangle with border
                $borderColor = '#6c757d'; // Gray border for category
                $textColor = '#6c757d';
                return "<span style='display: inline-block; padding: 6px 12px; border: 1px solid {$borderColor}; border-radius: 4px; background-color: transparent; color: {$textColor}; font-size: 12px; font-weight: 500; text-align: center; min-width: 80px;'>
                    {$categoryName}
                </span>";
            } catch (\Exception $e) {
                $categoryName = $this->resolveCategoryName($product);
                $borderColor = '#6c757d';
                $textColor = '#6c757d';
                return "<span style='display: inline-block; padding: 6px 12px; border: 1px solid {$borderColor}; border-radius: 4px; background-color: transparent; color: {$textColor}; font-size: 12px; font-weight: 500; text-align: center; min-width: 80px;'>
                    {$categoryName}
                </span>";
            }
        })
        ->addColumn('category_name', function ($product) {
            return $this->resolveCategoryName($product);
        })
        ->addColumn('category_id', function ($product) {
            return $product->category_id ?? null;
        });

        $dataTable->addColumn('action', function ($product) use ($competitors) {
                $buttons = '';
                
                // Edit button - match category module button style (btn-link)
                $buttons .= "<button type='button' 
                    class='btn btn-link p-0 m-0 align-baseline mx-2 edit-product-btn' 
                    style='font-size:inherit;' 
                    data-product-id='{$product->id}'
                    data-product-name='" . htmlspecialchars($product->name, ENT_QUOTES) . "'
                    data-product-sku='" . htmlspecialchars($product->default_code ?? '', ENT_QUOTES) . "'
                    data-product-price='{$product->list_price}'
                    data-product-category-id='" . ($product->category_id ?? '') . "'
                    title='Edit Product'>
                    <i class='fas fa-edit m-0'></i>
                </button>";
                
                // Sync button - only show if product has a valid Odoo ID (not manually created)
                if ($product->odoo_id && !$this->isManualProduct($product->odoo_id)) {
                    $buttons .= "<span class='text-light'>|</span>
                        <a href='javascript:void(0);' 
                            class='btn btn-link p-0 m-0 align-baseline mx-2 sync-product' 
                            style='font-size:inherit;'
                            data-product-id='{$product->odoo_id}'
                            title='Sync Product'>
                            <i class='fas fa-sync m-0'></i>
                        </a>";
                }
                
                return '<div class="d-inline-flex gap-2 align-items-center">' . $buttons . '</div>';
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
            
            return $dataTable->rawColumns(['action', 'status', 'category_display'])->make(true);
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
