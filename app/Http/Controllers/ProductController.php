<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PhoneLCDPartsScraper;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\Product;
use App\Models\Competitor;
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
        $categories = Product::whereNotNull('category')
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
            'categories' => $categories
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'odoo_id' => 'nullable',
            'competitor_id' => 'required|integer',
            'competitor_url' => 'required|url',
            'category' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
            'default_code' => 'nullable|string|max:255',
            'list_price' => 'nullable|numeric|min:0',
            'barcode' => 'nullable|string|max:255'
        ]);

        try {
            $competitor = Competitor::find($validated['competitor_id']);
            if (!$competitor) {
                return response()->json(['success' => false, 'message' => 'Competitor not found'], 404);
            }

            if (!$this->validateDomainMatch($validated['competitor_url'], $competitor->website)) {
                return response()->json(['success' => false, 'message' => "URL domain does not match competitor's website."], 400);
            }

            $productData = [
                'category' => $validated['category']
            ];

            // If Odoo ID is provided, fetch from Odoo
            if (!empty($validated['odoo_id'])) {
                $response = $this->odooService->fetchSpecificProduct($validated['odoo_id']);
                
                if (!isset($response['result']) || empty($response['result'])) {
                    return response()->json(['success' => false, 'message' => 'Product not found in Odoo'], 404);
                }

                $odooProduct = $response['result'][0];
                
                $productData = array_merge($productData, [
                    'odoo_id' => $odooProduct['id'],
                    'name' => $odooProduct['name'] ?? null,
                    'default_code' => $odooProduct['default_code'] ?? null,
                    'list_price' => $odooProduct['list_price'] ?? 0,
                    'barcode' => $odooProduct['barcode'] ?? null,
                ]);

                // Create or update product with category
                $product = Product::updateOrCreate(
                    ['odoo_id' => $odooProduct['id']],
                    $productData
                );
            } else {
                // Manual entry - validate required fields
                if (empty($validated['name'])) {
                    return response()->json(['success' => false, 'message' => 'Product name is required when Odoo ID is not provided'], 400);
                }

                // Generate manual product ID
                $manualOdooId = $this->generateManualProductId();
                
                // Ensure we have a valid ID
                if (empty($manualOdooId)) {
                    Log::error('Failed to generate manual product ID, using fallback');
                    $manualOdooId = '001'; // Fallback to 001 if generation fails
                }

                $productData = array_merge($productData, [
                    'odoo_id' => $manualOdooId,
                    'name' => $validated['name'],
                    'default_code' => $validated['default_code'] ?? null,
                    'list_price' => $validated['list_price'] ?? 0,
                    'barcode' => $validated['barcode'] ?? null,
                ]);

                // Log the data being inserted for debugging
                Log::info('Creating manual product', ['productData' => $productData]);

                // Create product with manual Odoo ID
                $product = Product::create($productData);
            }

            // Add competitor link and scrape price
            $productCompetitorPrice = ProductCompetitorPrice::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'competitor_id' => $validated['competitor_id']
                ],
                ['competitor_url' => $validated['competitor_url']]
            );

            $price = $this->scrapeCompetitorPrice($validated['competitor_url']);
            if ($price) {
                $productCompetitorPrice->update(['price' => $price]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product added successfully',
                'product' => $product,
                'price' => $price ?? null
            ]);
        } catch (\Exception $e) {
            Log::error('Store Product Error', [
                'error' => $e->getMessage(),
                'odoo_id' => $validated['odoo_id'] ?? null,
                'competitor_id' => $validated['competitor_id'],
                'url' => $validated['competitor_url']
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
