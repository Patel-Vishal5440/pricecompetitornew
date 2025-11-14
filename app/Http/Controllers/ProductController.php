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

        if (request()->ajax()) {
            $this->repo = $productRepository;
           return $this->repo->dataSource($request);
        }

        return view('product.products', [
            'pageTitle' => 'Product',
            'pageDescription' => 'Product',
            'competitors' => $competitors
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
            
            Product::updateOrCreate(
                ['odoo_id' => $product['id']],
                [
                    'name' => $product['name'] ?? null,
                    'default_code' => $product['default_code'] ?? null,
                    'list_price' => $product['list_price'] ?? 0,
                    'barcode' => $product['barcode'] ?? null,
                ]
            );

            return response()->json(['success' => true, 'message' => 'Product synced successfully!']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to sync product'], 404);
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
            if (!$product) return $this->jsonError('Product not found', 404);

            $competitor = Competitor::find($validated['competitor_id']);
            if (!$competitor) return $this->jsonError('Competitor not found', 404);

            if (!$this->validateDomainMatch($validated['competitor_url'], $competitor->website)) {
                return $this->jsonError("URL domain does not match competitor's website.");
            }

            $productCompetitorPrice = ProductCompetitorPrice::updateOrCreate(
                [
                    'product_id' => $validated['product_id'],
                    'competitor_id' => $validated['competitor_id']
                ],
                ['competitor_url' => $validated['competitor_url']]
            );

            $price = $this->scrapeCompetitorPrice($validated['competitor_url']);
            if (!$price) return $this->jsonError('Failed to extract price');

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
            return $this->jsonError('Internal server error', 500);
        }
    }
}
