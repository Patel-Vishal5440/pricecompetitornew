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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\ScrapeCompetitorPrice;
use App\Jobs\StoreOdooProducts;
use App\Models\ActivityFeed;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Traits\ScrapesCompetitorPrice;

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
            'competitor_urls.*.competitor_id' => 'required_with:competitor_urls.*.competitor_url|integer|exists:competitors,id',
            'competitor_urls.*.competitor_url' => 'required_with:competitor_urls.*.competitor_id|url'
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

    /**
     * Import and update prices from CSV file
     * CSV format: SKU, Price
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importPriceUpdate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:csv,txt|max:10240'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: ' . implode(', ', $validator->errors()->all())
                ], 422);
            }

            $file = $request->file('file');
            
            if (!$file || !$file->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file uploaded'
                ], 400);
            }

            $path = $file->getRealPath();
            
            if (!file_exists($path) || !is_readable($path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File cannot be read. Please check file permissions.'
                ], 400);
            }
            
            $results = [
                'success' => 0,
                'failed' => 0,
                'errors' => []
            ];

            $handle = fopen($path, 'r');
            if ($handle === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to open CSV file. Please ensure the file is a valid CSV format.'
                ], 400);
            }

            // Read header row and handle BOM
            $header = fgetcsv($handle);
            if ($header && !empty($header[0])) {
                // Remove BOM if present
                $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
            }
            $rowNumber = 1;

            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }
                
                if (count($row) < 2) {
                    $results['failed']++;
                    $results['errors'][] = "Row {$rowNumber}: Insufficient columns (expected: SKU, Price). Found: " . count($row) . " columns";
                    continue;
                }

                // Remove BOM from first column if present
                $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', $row[0]);
                $sku = trim($row[0]);
                $price = trim($row[1]);

                if (empty($sku)) {
                    $results['failed']++;
                    $results['errors'][] = "Row {$rowNumber}: SKU is required";
                    continue;
                }

                if (empty($price) || !is_numeric($price) || $price < 0) {
                    $results['failed']++;
                    $results['errors'][] = "Row {$rowNumber}: Valid price is required";
                    continue;
                }

                // Find product by SKU
                $product = Product::where('default_code', $sku)->first();
                
                if (!$product || !$product->odoo_id) {
                    $results['failed']++;
                    $results['errors'][] = "Row {$rowNumber}: Product with SKU '{$sku}' not found or has no Odoo ID";
                    continue;
                }

                // Update price in Odoo
                try {
                    $response = $this->odooService->updateProductPrice($product->odoo_id, (float)$price);
                    
                    if (isset($response['success']) && $response['success']) {
                        // Update local database
                        $product->update(['list_price' => (float)$price]);
                        $results['success']++;
                    } else {
                        $results['failed']++;
                        $errorMsg = $response['message'] ?? ($response['error'] ?? 'Unknown error');
                        $results['errors'][] = "Row {$rowNumber}: Failed to update price for SKU '{$sku}' - " . $errorMsg;
                        Log::warning("Price update failed for SKU: {$sku}", [
                            'odoo_id' => $product->odoo_id,
                            'price' => $price,
                            'response' => $response
                        ]);
                    }
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "Row {$rowNumber}: Exception updating price for SKU '{$sku}' - " . $e->getMessage();
                    Log::error("Price update exception for SKU: {$sku}", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            fclose($handle);

            return response()->json([
                'success' => true,
                'message' => "Import completed. Success: {$results['success']}, Failed: {$results['failed']}",
                'results' => $results
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors()->all();
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', $errors)
            ], 422);
        } catch (\Exception $e) {
            Log::error('Import Price Update Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $errorMessage = 'Failed to import prices';
            if (strpos($e->getMessage(), 'file') !== false) {
                $errorMessage = 'File processing error: ' . $e->getMessage();
            } elseif (strpos($e->getMessage(), 'database') !== false || strpos($e->getMessage(), 'SQL') !== false) {
                $errorMessage = 'Database error occurred. Please try again later.';
            } elseif (strpos($e->getMessage(), 'connection') !== false || strpos($e->getMessage(), 'timeout') !== false) {
                $errorMessage = 'Connection error. Please check your network and try again.';
            }
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage
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
            // Get all competitors for domain matching
            $competitors = Competitor::all();
            
            if ($competitors->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No competitors found in database'
                ], 400);
            }

            $file = $request->file('file');
            $path = $file->getRealPath();
            
            $results = [
                'success' => 0,
                'failed' => 0,
                'errors' => []
            ];

            // Read CSV file
            $handle = fopen($path, 'r');
            if ($handle === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to read CSV file'
                ], 400);
            }

            // Read header row and handle BOM
            $header = fgetcsv($handle);
            if ($header && !empty($header[0])) {
                // Remove BOM if present
                $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
            }
            $rowNumber = 1;
            
            Log::info('Bulk import started', [
                'header' => $header,
                'competitors_count' => $competitors->count()
            ]);

            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }
                
                if (count($row) < 2) {
                    $results['failed']++;
                    $results['errors'][] = "Row {$rowNumber}: Insufficient columns (expected: SKU, Category, and at least one Competitor URL). Found: " . count($row) . " columns";
                    continue;
                }

                // Remove BOM from first column if present
                $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', $row[0]);
                $sku = trim($row[0]);
                $categoryName = isset($row[1]) ? trim($row[1]) : '';
                
                if (empty($sku)) {
                    $results['failed']++;
                    $results['errors'][] = "Row {$rowNumber}: SKU is required";
                    continue;
                }

                // Fetch product from Odoo by SKU
                try {
                    $response = $this->odooService->fetchProductBySku($sku);
                    
                    if (!isset($response['success']) || !$response['success'] || empty($response['result'])) {
                        $results['failed']++;
                        $errorMsg = $response['message'] ?? 'Product not found in Odoo';
                        $results['errors'][] = "Row {$rowNumber}: Product with SKU '{$sku}' not found in Odoo - " . $errorMsg;
                        Log::warning("Bulk import: Product not found", ['sku' => $sku, 'response' => $response]);
                        continue;
                    }

                    $odooProduct = $response['result'][0];

                    // Initialize error tracking
                    $urlsAdded = 0;
                    $rowErrors = [];

                    // Prepare product data
                    $productData = [
                        'name' => $odooProduct['name'] ?? null,
                        'default_code' => $odooProduct['default_code'] ?? null,
                        'list_price' => $odooProduct['list_price'] ?? 0,
                        'barcode' => $odooProduct['barcode'] ?? null,
                    ];

                    // Handle category assignment if provided
                    if (!empty($categoryName)) {
                        // Find category by name (case-insensitive)
                        $category = Category::whereRaw('LOWER(name) = ?', [strtolower($categoryName)])->first();
                        
                        if ($category) {
                            $productData['category_id'] = $category->id;
                            $productData['category'] = $category->name;
                        } else {
                            $rowErrors[] = "Category '{$categoryName}' not found. Please create the category first before importing.";
                        }
                    }

                    // Create or update product
                    $product = Product::updateOrCreate(
                        ['odoo_id' => $odooProduct['id']],
                        $productData
                    );

                    // Process all URL columns (starting from index 2, after SKU and Category)
                    for ($i = 2; $i < count($row); $i++) {
                        $url = trim($row[$i]);
                        
                        if (empty($url)) {
                            continue; // Skip empty URLs
                        }

                        if (!filter_var($url, FILTER_VALIDATE_URL)) {
                            $rowErrors[] = "Column " . ($i + 1) . ": Invalid URL format";
                            continue;
                        }

                        // Find matching competitor by domain
                        $matchedCompetitor = null;
                        foreach ($competitors as $competitor) {
                            if ($competitor->website && $this->validateDomainMatch($url, $competitor->website)) {
                                $matchedCompetitor = $competitor;
                                break;
                            }
                        }

                        if (!$matchedCompetitor) {
                            $parsedUrl = parse_url($url, PHP_URL_HOST);
                            $rowErrors[] = "Column " . ($i + 1) . ": URL domain '{$parsedUrl}' does not match any competitor";
                            Log::warning("Bulk import: Domain mismatch", [
                                'url' => $url,
                                'parsed_host' => $parsedUrl,
                                'competitors' => $competitors->pluck('website')->toArray()
                            ]);
                            continue;
                        }

                        try {
                            // Add competitor URL
                            $productCompetitorPrice = ProductCompetitorPrice::updateOrCreate(
                                [
                                    'product_id' => $product->id,
                                    'competitor_id' => $matchedCompetitor->id
                                ],
                                ['competitor_url' => $url]
                            );

                            // Scrape price (non-blocking - don't fail if scraping fails)
                            try {
                                $price = $this->scrapeCompetitorPrice($url);
                                if ($price) {
                                    $productCompetitorPrice->update(['price' => $price]);
                                }
                            } catch (\Exception $e) {
                                Log::warning("Bulk import: Price scraping failed", [
                                    'url' => $url,
                                    'error' => $e->getMessage()
                                ]);
                                // Continue even if scraping fails
                            }
                            
                            $urlsAdded++;
                        } catch (\Exception $e) {
                            $rowErrors[] = "Column " . ($i + 1) . ": Failed to add URL - " . $e->getMessage();
                            Log::error("Bulk import: Failed to add competitor URL", [
                                'url' => $url,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }

                    // Add row-level errors to results
                    if (!empty($rowErrors)) {
                        foreach ($rowErrors as $error) {
                            $results['errors'][] = "Row {$rowNumber}: {$error}";
                        }
                    }

                    // Consider import successful if product was created/updated, even if no URLs were added
                    // But mark as failed if there were critical errors (like category not found)
                    $hasCriticalError = false;
                    foreach ($rowErrors as $error) {
                        if (stripos($error, 'category') !== false && stripos($error, 'not found') !== false) {
                            $hasCriticalError = true;
                            break;
                        }
                    }

                    if (!$hasCriticalError) {
                        $results['success']++;
                    } else {
                        $results['failed']++;
                    }
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "Row {$rowNumber}: Exception processing SKU '{$sku}' - " . $e->getMessage();
                    Log::error("Bulk import: Exception", [
                        'sku' => $sku,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            fclose($handle);

            return response()->json([
                'success' => true,
                'message' => "Import completed. Success: {$results['success']}, Failed: {$results['failed']}",
                'results' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Import Bulk Products Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
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
