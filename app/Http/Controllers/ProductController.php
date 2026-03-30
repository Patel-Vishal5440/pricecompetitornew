<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PhoneLCDPartsScraper;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\Product;
use App\Models\Competitor;
use App\Models\Category;
use App\Services\OdooService;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Http;
use App\Models\ProductCompetitorPrice;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\ScrapeCompetitorPrice;
use App\Jobs\StoreOdooProducts;
use App\Jobs\ProcessBulkProductImport;
use App\Jobs\ProcessBulkPriceImport;
use App\Models\ActivityFeed;
use App\Models\BulkImportJob;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Traits\ScrapesCompetitorPrice;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    use ScrapesCompetitorPrice;
    protected $repo;
    protected $odooService;
    public function __construct(OdooService $odooService)
    {
        $this->odooService = $odooService;
    }

    public function index(ProductRepository $productRepository,Request $request)
    {

        $competitors = Competitor::getCompetitorNames();
        // Get categories from Category model
        $categories = Category::active()->orderBy('name')->get();
        // Also get legacy categories from products table for backward compatibility
        $legacyCategories = Product::whereNotNull('category')
            ->whereNull('category_id')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->toArray();

        if (request()->ajax()) {
            $this->repo = $productRepository;
           return $this->repo->dataSource($request);
        }

        return view('product.products', [
            'pageTitle' => 'Product',
            'pageDescription' => 'Product',
            'competitors' => $competitors,
            'categories' => $categories,
            'legacyCategories' => $legacyCategories
        ]);
        
    }

    public function importStatusPage()
    {
        return view('product.import-status', [
            'pageTitle' => 'Import Status',
            'pageDescription' => 'Bulk import job status details',
        ]);
    }

    public function syncProducts()
    {    
        $response = $this->odooService->fetchProducts();
        $products = $response['result'] ?? [];

        if (!empty($products)) {
            StoreOdooProducts::dispatch($products);
            Log::info('Dispatched ' . count($products) . ' products to queue.');
        }

        return response()->json(['message' => "Synced  products successfully!"], 200);
    }

    public function getCompetitorUrls(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id'
        ]);

        try {
            $product = Product::findOrFail($request->product_id);
            $competitorUrls = [];
            
            foreach ($product->competitorPrices as $competitorPrice) {
                $competitorUrls[] = [
                    'competitor_id' => $competitorPrice->competitor_id,
                    'url' => $competitorPrice->competitor_url
                ];
            }
            
            return response()->json([
                'success' => true,
                'urls' => $competitorUrls
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to get competitor URLs: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get competitor URLs'
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:products,id',
            'category_id' => 'nullable|integer|exists:categories,id',
            'list_price' => 'nullable|numeric|min:0',
            'competitor_urls' => 'nullable|array',
            // Allow sending competitor_id with empty URL to clear it
            'competitor_urls.*.competitor_id' => 'required|integer|exists:competitors,id',
            'competitor_urls.*.competitor_url' => 'nullable|url'
        ]);

        try {
            $product = Product::findOrFail($request->id);
            $updateData = [];
            $priceChanged = false;
            
            // Update category
            if ($request->has('category_id')) {
                if ($request->category_id) {
                    $category = Category::find($request->category_id);
                    if ($category) {
                        $updateData['category_id'] = $request->category_id;
                        $updateData['category'] = $category->name;
                    }
                } else {
                    $updateData['category_id'] = null;
                    $updateData['category'] = null;
                }
            }
            
            // Update price - only call Odoo API if price actually changed
            if ($request->has('list_price') && $request->list_price !== null) {
                $newPrice = (float)$request->list_price;
                $currentPrice = (float)($product->list_price ?? 0);
                
                // Check if price actually changed
                if (abs($newPrice - $currentPrice) > 0.01) { // Allow for floating point precision
                    $updateData['list_price'] = $newPrice;
                    $priceChanged = true;
                    
                    // Only update in Odoo if price changed and product has Odoo ID
                    if ($product->odoo_id && !$this->isManualProduct($product->odoo_id)) {
                        try {
                            $this->odooService->updateProductPrice($product->odoo_id, $newPrice);
                            Log::info("Price updated in Odoo", [
                                'product_id' => $product->id,
                                'odoo_id' => $product->odoo_id,
                                'old_price' => $currentPrice,
                                'new_price' => $newPrice
                            ]);
                        } catch (\Exception $e) {
                            Log::warning("Failed to update price in Odoo: " . $e->getMessage());
                            // Continue with local update even if Odoo update fails
                        }
                    }
                }
            }
            
            // Update product
            if (!empty($updateData)) {
                $product->update($updateData);
            }
            
            // Update competitor URLs
            if ($request->has('competitor_urls') && is_array($request->competitor_urls)) {
                foreach ($request->competitor_urls as $urlData) {
                    if (!empty($urlData['competitor_url'])) {
                        // Check if URL already exists for this product-competitor combination
                        $existingCompetitorPrice = ProductCompetitorPrice::where('product_id', $product->id)
                            ->where('competitor_id', $urlData['competitor_id'])
                            ->first();
                        
                        $urlChanged = false;
                        if ($existingCompetitorPrice) {
                            // Check if URL actually changed
                            $existingUrl = $existingCompetitorPrice->competitor_url ?? '';
                            if (trim($existingUrl) !== trim($urlData['competitor_url'])) {
                                $urlChanged = true;
                            }
                        } else {
                            // New URL, so it's a change
                            $urlChanged = true;
                        }
                        
                        $competitorPrice = ProductCompetitorPrice::updateOrCreate(
                            [
                                'product_id' => $product->id,
                                'competitor_id' => $urlData['competitor_id']
                            ],
                            [
                                'competitor_url' => $urlData['competitor_url']
                            ]
                        );
                        
                        // Trigger price scraping only if URL changed
                        if ($urlChanged) {
                            try {
                                $price = $this->scrapeCompetitorPrice($urlData['competitor_url']);
                                if ($price) {
                                    $competitorPrice->update(['price' => $price]);
                                    Log::info("Competitor price scraped successfully", [
                                        'product_id' => $product->id,
                                        'competitor_id' => $urlData['competitor_id'],
                                        'url' => $urlData['competitor_url'],
                                        'price' => $price
                                    ]);
                                } else {
                                    Log::warning("No price found when scraping", [
                                        'product_id' => $product->id,
                                        'competitor_id' => $urlData['competitor_id'],
                                        'url' => $urlData['competitor_url']
                                    ]);
                                }
                            } catch (\Exception $e) {
                                Log::warning("Failed to scrape competitor price: " . $e->getMessage(), [
                                    'product_id' => $product->id,
                                    'competitor_id' => $urlData['competitor_id'],
                                    'url' => $urlData['competitor_url']
                                ]);
                            }
                        }
                    } else {
                        // Remove URL if empty
                        ProductCompetitorPrice::where('product_id', $product->id)
                            ->where('competitor_id', $urlData['competitor_id'])
                            ->update(['competitor_url' => null, 'price' => null]);
                    }
                }
            }
            
            $message = 'Product updated successfully';
            if ($priceChanged) {
                $message .= ' (Price updated in Odoo)';
            }
            
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to update product: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product: ' . $e->getMessage()
            ], 500);
        }
    }

    private function isManualProduct($odooId)
    {
        if (is_null($odooId)) {
            return true;
        }
        $odooIdString = (string) $odooId;
        return strpos($odooIdString, '002') === 0;
    }

    public function updatePrice(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'list_price' => 'required|numeric|min:0'
        ]);

        $newPrice = (float) $request->list_price;

        $response = $this->odooService->updateProductPrice($request->id, $newPrice);
        $response['success'] = true;
        if ($response['success']) {
            try {
                $product = Product::where('odoo_id', $request->id)->first();
                if ($product) {
                    $product->update(['list_price' => $newPrice]);                 

                    return response()->json(['success' => true, 'message' => 'Price updated successfully in Odoo and local database']);
                }
                Log::info("Product with Odoo ID {$request->id} not found in local database");
                return response()->json(['success' => true, 'message' => 'Price updated in Odoo but product not found in local database']);
            } catch (\Exception $e) {
                Log::error("Failed to update local database: " . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Updated in Odoo but failed to update local database'], 500);
            }
        }

        Log::error("Failed to update price in Odoo: " . ($response['error'] ?? 'Unknown error'));
        return response()->json([
            'success' => false, 
            'message' => 'Failed to update price in Odoo',
            'error' => $response['error'] ?? null
        ], 500);
    }
        
    public function syncSpecificProduct(Request $request)
    {
        

        $request->validate([
            'odoo_id' => 'required|integer'
        ]);

        $response = $this->odooService->fetchSpecificProduct($request->odoo_id);
// dd($response);
        if (isset($response['result']) && !empty($response['result'])) {
            $product = $response['result'][0];
            
            // Check if product already exists to preserve category
            $existingProduct = Product::where('odoo_id', $product['id'])->first();
            $updateData = [
                'name' => $product['name'] ?? null,
                'default_code' => $product['default_code'] ?? null,
                'list_price' => $product['list_price'] ?? 0,
                'cost' => $product['standard_price'] ?? 0,
                'barcode' => $product['barcode'] ?? null,
            ];
            
            // Preserve existing category if it exists
            if ($existingProduct && $existingProduct->category) {
                $updateData['category'] = $existingProduct->category;
            }
            
            Product::updateOrCreate(
                ['odoo_id' => $product['id']],
                $updateData
            );

            return response()->json(['success' => true, 'message' => 'Product synced successfully!']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to sync product'], 404);
    }

    public function bulkSyncPricing(Request $request)
    {
        $validated = $request->validate([
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'integer|exists:products,id',
        ]);

        $products = Product::with('competitorPrices')->whereIn('id', $validated['product_ids'])->get();

        $sourceUpdated = 0;
        $competitorUpdated = 0;
        $failed = 0;

        foreach ($products as $product) {
            try {
                if ($product->odoo_id && !$this->isManualProduct($product->odoo_id)) {
                    $sourceResponse = $this->odooService->fetchSpecificProduct($product->odoo_id);
                    if (isset($sourceResponse['result'][0])) {
                        $sourceProduct = $sourceResponse['result'][0];
                        $product->update([
                            'name' => $sourceProduct['name'] ?? $product->name,
                            'default_code' => $sourceProduct['default_code'] ?? $product->default_code,
                            'list_price' => $sourceProduct['list_price'] ?? $product->list_price,
                            'cost' => $sourceProduct['standard_price'] ?? $product->cost,
                            'barcode' => $sourceProduct['barcode'] ?? $product->barcode,
                        ]);
                        $sourceUpdated++;
                    }
                }

                foreach ($product->competitorPrices as $competitorPrice) {
                    if (empty($competitorPrice->competitor_url)) {
                        continue;
                    }

                    try {
                        $price = $this->scrapeCompetitorPrice($competitorPrice->competitor_url);
                        if ($price !== null) {
                            $competitorPrice->update(['price' => $price]);
                            $competitorUpdated++;
                        }
                    } catch (\Exception $e) {
                        Log::warning('Bulk competitor pricing sync failed', [
                            'product_id' => $product->id,
                            'competitor_id' => $competitorPrice->competitor_id,
                            'url' => $competitorPrice->competitor_url,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            } catch (\Exception $e) {
                $failed++;
                Log::warning('Bulk pricing sync failed for product', [
                    'product_id' => $product->id,
                    'odoo_id' => $product->odoo_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Bulk pricing sync completed.',
            'summary' => [
                'products_processed' => $products->count(),
                'source_updated' => $sourceUpdated,
                'competitor_prices_updated' => $competitorUpdated,
                'failed_products' => $failed,
            ],
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'integer|exists:products,id',
        ]);

        $deletedCount = Product::whereIn('id', $validated['product_ids'])->delete();

        return response()->json([
            'success' => true,
            'message' => "Deleted {$deletedCount} product(s) successfully.",
            'deleted_count' => $deletedCount,
        ]);
    }

    public function bulkAssignCategory(Request $request)
    {
        $validated = $request->validate([
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'integer|exists:products,id',
            'category_id' => 'nullable|integer|exists:categories,id',
            'clear' => 'nullable|boolean'
        ]);

        try {
            $clear = (bool) ($validated['clear'] ?? false);
            $updateData = [];

            if ($clear) {
                $updateData['category_id'] = null;
                $updateData['category'] = null;
            } else {
                $category = null;
                if (!empty($validated['category_id'])) {
                    $category = Category::find($validated['category_id']);
                }
                if (!$category) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected category not found.'
                    ], 422);
                }
                $updateData['category_id'] = $category->id;
                $updateData['category'] = $category->name;
            }

            $affected = Product::whereIn('id', $validated['product_ids'])->update($updateData);

            return response()->json([
                'success' => true,
                'message' => $clear
                    ? "Cleared category for {$affected} product(s)."
                    : "Assigned category to {$affected} product(s).",
                'updated_count' => $affected
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk Assign Category Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Bulk category assignment failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'odoo_id' => 'nullable|integer|required_without:sku',
            'sku' => 'nullable|string|required_without:odoo_id',
            'competitor_urls' => 'nullable|array',
            'competitor_urls.*.competitor_id' => 'required_with:competitor_urls.*.competitor_url|integer|exists:competitors,id',
            'competitor_urls.*.competitor_url' => 'required_with:competitor_urls.*.competitor_id|url',
            'category_id' => 'nullable|integer|exists:categories,id'
        ]);

        try {
            $productData = [];

            // Get category name from category_id if provided
            if (!empty($validated['category_id'])) {
                $category = Category::find($validated['category_id']);
                if ($category) {
                    $productData['category_id'] = $validated['category_id'];
                    $productData['category'] = $category->name;
                }
            }

            // Fetch product from Odoo by either Odoo ID or SKU
            if (!empty($validated['odoo_id'])) {
                // Fetch by Odoo ID
                $response = $this->odooService->fetchSpecificProduct($validated['odoo_id']);
                $errorMessage = 'Product not found in Odoo (Odoo ID: ' . $validated['odoo_id'] . ')';
            } elseif (!empty($validated['sku'])) {
                // Fetch by SKU
                $response = $this->odooService->fetchProductBySku($validated['sku']);
                $errorMessage = 'Product not found in Odoo (SKU: ' . $validated['sku'] . ')';
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Either odoo_id or sku must be provided'
                ], 400);
            }

            // If not found, return error
            if (!$response['success'] || empty($response['result'])) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 404);
            }

            $odooProduct = $response['result'][0];

            // Use Odoo data for product information (form data is ignored except for category)
            $productData = array_merge($productData, [
                'odoo_id' => $odooProduct['id'],
                'name' => $odooProduct['name'] ?? null,
                'default_code' => $odooProduct['default_code'] ?? null,
                'list_price' => $odooProduct['list_price'] ?? 0,
                'cost' => $odooProduct['standard_price'] ?? 0,
                'barcode' => $odooProduct['barcode'] ?? null,
            ]);

            // Create or update product with Odoo data
            $product = Product::updateOrCreate(
                ['odoo_id' => $odooProduct['id']],
                $productData
            );

            // Validate and add competitor URLs (if provided)
            $scrapedPrices = [];
            if (!empty($validated['competitor_urls']) && is_array($validated['competitor_urls'])) {
                foreach ($validated['competitor_urls'] as $competitorUrlData) {
                    $competitor = Competitor::find($competitorUrlData['competitor_id']);
                    if (!$competitor) {
                        Log::warning('Competitor not found', ['competitor_id' => $competitorUrlData['competitor_id']]);
                        continue;
                    }

                    // Validate domain match
                    if (!$this->validateDomainMatch($competitorUrlData['competitor_url'], $competitor->website)) {
                        Log::warning('URL domain mismatch', [
                            'competitor_id' => $competitorUrlData['competitor_id'],
                            'url' => $competitorUrlData['competitor_url'],
                            'expected_domain' => $competitor->website
                        ]);
                        continue;
                    }

                    // Add competitor link and scrape price
                    $productCompetitorPrice = ProductCompetitorPrice::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'competitor_id' => $competitorUrlData['competitor_id']
                        ],
                        ['competitor_url' => $competitorUrlData['competitor_url']]
                    );

                    $price = $this->scrapeCompetitorPrice($competitorUrlData['competitor_url']);
                    if ($price) {
                        $productCompetitorPrice->update(['price' => $price]);
                        $scrapedPrices[$competitor->name] = $price;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Product added successfully from Odoo',
                'product' => $product,
                'prices' => $scrapedPrices
            ]);
        } catch (\Exception $e) {
            Log::error('Store Product Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'odoo_id' => $validated['odoo_id'] ?? null
            ]);
            return response()->json(['success' => false, 'message' => 'Internal server error: ' . $e->getMessage()], 500);
        }
    }

    public function addLink(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'competitor_id' => 'required|integer',
            'competitor_url' => 'required|url'
        ]);

        try {
            $product = Product::find($validated['product_id']);
            if (!$product) {
                return response()->json(['success' => false, 'message' => 'Product not found'], 404);
            }

            $competitor = Competitor::find($validated['competitor_id']);
            if (!$competitor) {
                return response()->json(['success' => false, 'message' => 'Competitor not found'], 404);
            }

            if (!$this->validateDomainMatch($validated['competitor_url'], $competitor->website)) {
                return response()->json(['success' => false, 'message' => "URL domain does not match competitor's website."], 400);
            }

            $productCompetitorPrice = ProductCompetitorPrice::updateOrCreate(
                [
                    'product_id' => $validated['product_id'],
                    'competitor_id' => $validated['competitor_id']
                ],
                ['competitor_url' => $validated['competitor_url']]
            );

            $price = $this->scrapeCompetitorPrice($validated['competitor_url']);
            if (!$price) {
                return response()->json(['success' => false, 'message' => 'Failed to extract price'], 400);
            }

            $productCompetitorPrice->update(['price' => $price]);

            return response()->json([
                'success' => true,
                'message' => 'Price scraped successfully',
                'price' => $price
            ]);
        } catch (\Exception $e) {
            Log::error('addLink Error', [
                'error' => $e->getMessage(),
                'product_id' => $validated['product_id'],
                'competitor_id' => $validated['competitor_id'],
                'url' => $validated['competitor_url']
            ]);
            return response()->json(['success' => false, 'message' => 'Internal server error'], 500);
        }
    }

    public function removeLink(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'competitor_id' => 'required|integer|exists:competitors,id',
        ]);

        try {
            $record = ProductCompetitorPrice::where('product_id', $validated['product_id'])
                ->where('competitor_id', $validated['competitor_id'])
                ->first();

            if ($record) {
                $record->update(['competitor_url' => null, 'price' => null]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Competitor link and price cleared'
            ]);
        } catch (\Exception $e) {
            Log::error('removeLink Error', [
                'error' => $e->getMessage(),
                'product_id' => $validated['product_id'] ?? null,
                'competitor_id' => $validated['competitor_id'] ?? null
            ]);
            return response()->json(['success' => false, 'message' => 'Internal server error'], 500);
        }
    }

    /**
     * Import and update prices from CSV file
     * CSV format: SKU, Price
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importPriceUpdate(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240'
        ]);

        try {
            $file = $request->file('file');
            $storedPath = $file->store('imports');
            $absolutePath = storage_path('app/' . $storedPath);
            $totalRows = $this->countCsvDataRows($absolutePath);

            $importJob = BulkImportJob::create([
                'user_id' => Auth::id(),
                'type' => 'price',
                'status' => 'queued',
                'total_rows' => $totalRows,
                'processed_rows' => 0,
                'success_count' => 0,
                'failed_count' => 0,
                'errors' => [],
                'uploaded_file_path' => $storedPath,
                'message' => 'Price import queued.',
            ]);

            ProcessBulkPriceImport::dispatch($importJob->id, $storedPath);

            return response()->json([
                'success' => true,
                'message' => 'Price import queued successfully. Processing started in background.',
                'import_job' => $this->formatImportJobResponse($importJob)
            ]);
        } catch (\Exception $e) {
            Log::error('Import Price Update Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if (!empty($storedPath ?? null)) {
                Storage::disk('local')->delete($storedPath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to queue price import: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import bulk products from CSV file
     * CSV format: SKU, Category, Competitor URL 1, Competitor URL 2, ... (supports any competitors)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importBulkProducts(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240' // 10MB max
        ]);

        try {
            $file = $request->file('file');
            $storedPath = $file->store('imports');
            $absolutePath = storage_path('app/' . $storedPath);
            $totalRows = $this->countCsvDataRows($absolutePath);

            $importJob = BulkImportJob::create([
                'user_id' => Auth::id(),
                'type' => 'products',
                'status' => 'queued',
                'total_rows' => $totalRows,
                'processed_rows' => 0,
                'success_count' => 0,
                'failed_count' => 0,
                'errors' => [],
                'uploaded_file_path' => $storedPath,
                'message' => 'Import queued.',
            ]);

            ProcessBulkProductImport::dispatch($importJob->id, $storedPath);

            return response()->json([
                'success' => true,
                'message' => 'Import queued successfully. Processing started in background.',
                'import_job' => $this->formatImportJobResponse($importJob)
            ]);

        } catch (\Exception $e) {
            Log::error('Import Bulk Products Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            if (!empty($storedPath ?? null)) {
                Storage::disk('local')->delete($storedPath);
            }
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
                        }
                    }

    public function bulkImportStatus(int $id)
    {
        $importJob = BulkImportJob::where('type', 'products')->findOrFail($id);

        return response()->json([
            'success' => true,
            'import_job' => $this->formatImportJobResponse($importJob),
        ]);
                        }

    public function bulkImportJobs(Request $request)
    {
        $showAll = $request->boolean('all', false);
        $limit = (int) $request->get('limit', 10);
        $limit = max(1, min(500, $limit));

        $jobsQuery = BulkImportJob::where('type', 'products')
            ->orderByDesc('id');

        if (!$showAll) {
            $jobsQuery->limit($limit);
        }

        $jobs = $jobsQuery->get()
            ->map(function ($job) {
                return $this->formatImportJobResponse($job);
            });

        return response()->json([
            'success' => true,
            'jobs' => $jobs,
        ]);
    }

    public function priceImportStatus(int $id)
    {
        $importJob = BulkImportJob::where('type', 'price')->findOrFail($id);

        return response()->json([
            'success' => true,
            'import_job' => $this->formatImportJobResponse($importJob),
        ]);
    }

    public function priceImportJobs(Request $request)
    {
        $showAll = $request->boolean('all', false);
        $limit = (int) $request->get('limit', 10);
        $limit = max(1, min(500, $limit));

        $jobsQuery = BulkImportJob::where('type', 'price')
            ->orderByDesc('id');

        if (!$showAll) {
            $jobsQuery->limit($limit);
        }

        $jobs = $jobsQuery->get()
            ->map(function ($job) {
                return $this->formatImportJobResponse($job);
            });

        return response()->json([
            'success' => true,
            'jobs' => $jobs,
        ]);
    }

    public function importStatusJobs(Request $request)
    {
        if ($request->has('draw')) {
            $draw = (int) $request->get('draw', 1);
            $start = max(0, (int) $request->get('start', 0));
            $length = max(1, min(100, (int) $request->get('length', 10)));
            $type = trim((string) $request->get('import_type', ''));
            $search = trim((string) $request->get('searchData', ''));

            $baseQuery = BulkImportJob::query();
            if (in_array($type, ['products', 'price'], true)) {
                $baseQuery->where('type', $type);
            }

            $filteredQuery = clone $baseQuery;
            if ($search !== '') {
                $filteredQuery->where(function ($q) use ($search) {
                    $q->where('status', 'like', '%' . $search . '%')
                        ->orWhere('message', 'like', '%' . $search . '%')
                        ->orWhere('type', 'like', '%' . $search . '%');

                    if (preg_match('/^\d+$/', $search)) {
                        $q->orWhere('id', (int) $search);
                    }
                });
            }

            $recordsTotal = (clone $baseQuery)->count();
            $recordsFiltered = (clone $filteredQuery)->count();

            $jobs = $filteredQuery
                ->orderByDesc('id')
                ->skip($start)
                ->take($length)
                ->get()
                ->map(function ($job) {
                    return $this->formatImportJobResponse($job);
                })
                ->values();

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => (int) $recordsTotal,
                'recordsFiltered' => (int) $recordsFiltered,
                'data' => $jobs,
            ]);
        }

        $type = trim((string) $request->get('type', ''));
        $search = trim((string) $request->get('search', ''));
        $page = max(1, (int) $request->get('page', 1));
        $perPage = max(1, min(100, (int) $request->get('per_page', 10)));

        $jobsQuery = BulkImportJob::query()->orderByDesc('id');

        if (in_array($type, ['products', 'price'], true)) {
            $jobsQuery->where('type', $type);
        }

        if ($search !== '') {
            $jobsQuery->where(function ($q) use ($search) {
                $q->where('status', 'like', '%' . $search . '%')
                    ->orWhere('message', 'like', '%' . $search . '%')
                    ->orWhere('type', 'like', '%' . $search . '%');

                if (preg_match('/^\d+$/', $search)) {
                    $q->orWhere('id', (int) $search);
                }
            });
        }

        $total = (int) $jobsQuery->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);

        $jobs = $jobsQuery
            ->forPage($page, $perPage)
            ->get()
            ->map(function ($job) {
                return $this->formatImportJobResponse($job);
            })
            ->values();

        return response()->json([
            'success' => true,
            'jobs' => $jobs,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => $lastPage,
            ],
        ]);
    }

    private function countCsvDataRows(string $absolutePath): int
    {
        $count = 0;
        $handle = fopen($absolutePath, 'r');
        if ($handle === false) {
            return 0;
        }

        // Skip header
        fgetcsv($handle);
        while (($row = fgetcsv($handle)) !== false) {
            if (!empty(array_filter($row))) {
                $count++;
            }
        }
        fclose($handle);

        return $count;
                        }

    private function formatImportJobResponse(BulkImportJob $job): array
    {
        $totalRows = (int) $job->total_rows;
        $processedRows = (int) $job->processed_rows;
        $percent = $totalRows > 0 ? (int) floor(($processedRows / $totalRows) * 100) : 0;
        if (in_array($job->status, ['completed', 'failed'], true)) {
            $percent = 100;
        }

        return [
            'id' => $job->id,
            'type' => $job->type,
            'type_label' => $job->type === 'price' ? 'Product Price Update' : 'Product Import',
            'status' => $job->status,
            'total_rows' => $totalRows,
            'processed_rows' => $processedRows,
            'success_count' => (int) $job->success_count,
            'failed_count' => (int) $job->failed_count,
            'progress_percent' => min(100, max(0, $percent)),
            'message' => $job->message,
            'errors' => is_array($job->errors) ? $job->errors : [],
            'started_at' => optional($job->started_at)->toDateTimeString(),
            'completed_at' => optional($job->completed_at)->toDateTimeString(),
            'created_at' => optional($job->created_at)->toDateTimeString(),
            'updated_at' => optional($job->updated_at)->toDateTimeString(),
        ];
    }

    /**
     * Download sample CSV file for price update import
     * 
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadPriceUpdateSample()
    {
        $filename = 'price_update_sample.csv';
        
        return response()->streamDownload(function () {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header row
            fputcsv($file, ['SKU', 'Price']);
            
            // Sample data rows
            fputcsv($file, ['SKU001', '29.99']);
            
            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Download sample CSV file for bulk products import
     * 
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadBulkProductsSample()
    {
        $filename = 'bulk_products_sample.csv';
        
        return response()->streamDownload(function () {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Get actual competitors from database for example
            $competitors = Competitor::limit(3)->get();
            $header = ['SKU (FOR US)', 'Category'];
            
            // Add competitor columns based on available competitors
            foreach ($competitors as $competitor) {
                $header[] = $competitor->name . ' URL';
            }
            
            // If no competitors, use example columns
            if ($competitors->isEmpty()) {
                $header = ['SKU (FOR US)', 'Category', 'Competitor 1 URL', 'Competitor 2 URL'];
            }
            
            // Header row
            fputcsv($file, $header);
            
            // Get sample categories for example
            $categories = Category::limit(2)->get();
            $sampleCategories = $categories->pluck('name')->toArray();
            if (empty($sampleCategories)) {
                $sampleCategories = ['Electronics', 'Accessories'];
            }
            
            // Sample data rows
            if (!$competitors->isEmpty()) {
                // Use actual competitor websites if available
                $urls = [];
                foreach ($competitors as $competitor) {
                    if ($competitor->website) {
                        $urls[] = rtrim($competitor->website, '/') . '/product/example1';
                    } else {
                        $urls[] = 'https://example.com/product/example1';
                    }
                }
                // Add sample rows with different categories
                fputcsv($file, array_merge(['SKU001', $sampleCategories[0] ?? 'Electronics'], $urls));
                if (count($sampleCategories) > 1) {
                    fputcsv($file, array_merge(['SKU002', $sampleCategories[1] ?? 'Accessories'], $urls));
                }
            } else {
                // Fallback example
                fputcsv($file, [
                    'SKU001',
                    $sampleCategories[0] ?? 'Electronics',
                    'https://competitor1.com/product/example1',
                    'https://competitor2.com/product/example1'
                ]);
            }
            
            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Generate a unique manual product ID with sequential numbering
     * Format: 001, 002, 003, etc.
     * 
     * @return string
     */
    private function generateManualProductId()
    {
        try {
            // Get all products with odoo_id that starts with "00" and is 3 characters long
            $allProducts = Product::whereNotNull('odoo_id')->pluck('odoo_id')->toArray();
            
            $maxNumber = 0;
            
            // Find the highest number from existing manual product IDs (format: 001, 002, etc.)
            foreach ($allProducts as $odooId) {
                $odooIdStr = trim((string) $odooId);
                // Check if it matches the pattern: exactly 3 characters starting with "00"
                if (strlen($odooIdStr) === 3 && preg_match('/^00(\d+)$/', $odooIdStr, $matches)) {
                    $number = (int)$matches[1];
                    if ($number > $maxNumber) {
                        $maxNumber = $number;
                    }
                }
            }
            
            // Increment to get next number
            $nextNumber = $maxNumber + 1;
            // Ensure the number doesn't exceed 999 (to keep 3-digit format)
            if ($nextNumber > 999) {
                throw new \Exception('Maximum manual product ID limit reached (999)');
            }
            
            // Generate ID with leading zeros (001, 002, 003, etc.)
            $manualId = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            
            // Double-check uniqueness (in case of race condition)
            $exists = Product::where('odoo_id', $manualId)->exists();
            if ($exists) {
                // If exists, find next available number
                $counter = $nextNumber;
                do {
                    $counter++;
                    if ($counter > 999) {
                        throw new \Exception('Maximum manual product ID limit reached (999)');
                    }
                    $manualId = str_pad($counter, 3, '0', STR_PAD_LEFT);
                    $exists = Product::where('odoo_id', $manualId)->exists();
                } while ($exists);
            }
            
            return $manualId;
        } catch (\Exception $e) {
            // Log the error and return a fallback ID based on timestamp
            Log::error('Error generating manual product ID: ' . $e->getMessage());
            // Fallback: use timestamp-based ID if the main method fails
            $timestamp = substr(time(), -6);
            return '00' . $timestamp;
        }
    }
}
