<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\ActivityFeed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        if ($product->isDirty('list_price')) {
            $newPrice = floatval($product->list_price);
            $oldPrice = floatval($product->getOriginal('list_price'));
            if ($newPrice != $oldPrice) {
                $this->logActivityFeed($product->id, $oldPrice, $newPrice);
            }
        }
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {

    }

    protected function logActivityFeed($productId, $oldPrice, $newPrice)
    {
        if(Auth::check()){
            ActivityFeed::create([
                'model_id' => $productId,
                'user_id' => Auth::user()->id, // Changed from moderator_id to user_id
                'price_old' => floatval($oldPrice),
                'price_new' => floatval($newPrice),
                'type' => 'product-price-update', //do not change this
                'created_at' => now(),
            ]);
        }
    }
}
