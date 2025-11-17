<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class StoreOdooProducts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $products;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $products)
    {
        $this->products = $products;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->products as $product) {
            try {
                // Validate that product is an array and has required fields
                if (!is_array($product) || !isset($product['id'])) {
                    Log::warning("Skipping invalid product data:", $product);
                    continue;
                }
                
                Product::updateOrCreate(
                    ['odoo_id' => $product['id']],
                    [
                        'name' => $product['name'] ?? null,
                        'default_code' => $product['default_code'] ?? null,
                        'list_price' => $product['list_price'] ?? 0,
                        'cost' => $product['standard_price'] ?? 0,
                        'barcode' => $product['barcode'] ?? null,
                    ]
                );
                
                Log::info("Successfully stored product with ID: " . $product['id']);
            } catch (\Exception $e) {
                Log::error("Failed to store product: " . $e->getMessage(), [
                    'product' => $product ?? 'null'
                ]);
            }
        }
    }
}
