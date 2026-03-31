<?php

namespace App\Jobs;

use App\Models\BulkImportJob;
use App\Models\Product;
use App\Services\OdooService;
use App\Traits\ScrapesCompetitorPrice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessBulkPricingSync implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ScrapesCompetitorPrice;

    public $timeout = 3600;
    public $tries = 3;

    private int $importJobId;
    /** @var int[] */
    private array $productIds;

    /**
     * @param int $importJobId
     * @param int[] $productIds
     */
    public function __construct(int $importJobId, array $productIds)
    {
        $this->importJobId = $importJobId;
        $this->productIds = array_values(array_unique(array_map('intval', $productIds)));
    }

    public function handle(OdooService $odooService): void
    {
        $importJob = BulkImportJob::find($this->importJobId);
        if (!$importJob) {
            return;
        }

        $totalRows = (int) ($importJob->total_rows ?: count($this->productIds));
        $rowDelayMicroseconds = $this->getRowDelayMicroseconds($totalRows);

        $importJob->update([
            'status' => 'processing',
            'started_at' => now(),
            'message' => 'Pricing sync is processing.',
        ]);

        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
            'source_updated' => 0,
            'competitor_prices_updated' => 0,
        ];
        $maxErrorDetails = 200;
        $processedRows = 0;

        $products = Product::with('competitorPrices')->whereIn('id', $this->productIds)->get();

        foreach ($products as $product) {
            $processedRows++;

            try {
                // Refresh source product fields from Odoo (skip "manual" products)
                if ($product->odoo_id && !$this->isManualProduct($product->odoo_id)) {
                    $sourceResponse = $odooService->fetchSpecificProduct($product->odoo_id);
                    if (isset($sourceResponse['result'][0])) {
                        $sourceProduct = $sourceResponse['result'][0];
                        $product->update([
                            'name' => $sourceProduct['name'] ?? $product->name,
                            'default_code' => $sourceProduct['default_code'] ?? $product->default_code,
                            'list_price' => $sourceProduct['list_price'] ?? $product->list_price,
                            'cost' => $sourceProduct['standard_price'] ?? $product->cost,
                            'barcode' => $sourceProduct['barcode'] ?? $product->barcode,
                        ]);
                        $results['source_updated']++;
                    }
                }

                // Refresh competitor prices by scraping URLs
                foreach ($product->competitorPrices as $competitorPrice) {
                    if (empty($competitorPrice->competitor_url)) {
                        continue;
                    }

                    try {
                        $price = $this->scrapeCompetitorPrice($competitorPrice->competitor_url);
                        if ($price !== null) {
                            $competitorPrice->update(['price' => $price]);
                            $results['competitor_prices_updated']++;
                        }
                    } catch (\Throwable $e) {
                        if (count($results['errors']) < $maxErrorDetails) {
                            $results['errors'][] = "Product {$product->id}: Competitor {$competitorPrice->competitor_id} scrape failed - " . $e->getMessage();
                        }
                        Log::warning('Queued competitor pricing sync failed', [
                            'import_job_id' => $importJob->id,
                            'product_id' => $product->id,
                            'competitor_id' => $competitorPrice->competitor_id,
                            'url' => $competitorPrice->competitor_url,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                $results['success']++;
            } catch (\Throwable $e) {
                $results['failed']++;
                if (count($results['errors']) < $maxErrorDetails) {
                    $results['errors'][] = "Product {$product->id}: Pricing sync failed - " . $e->getMessage();
                }
                Log::warning('Queued pricing sync failed for product', [
                    'import_job_id' => $importJob->id,
                    'product_id' => $product->id,
                    'odoo_id' => $product->odoo_id,
                    'error' => $e->getMessage(),
                ]);
            }

            $this->syncProgress($importJob, $processedRows, $results);
            if ($rowDelayMicroseconds > 0) {
                usleep($rowDelayMicroseconds);
            }
        }

        // If some IDs were missing, count them as failed so totals line up.
        $missing = max(0, count($this->productIds) - $products->count());
        if ($missing > 0) {
            $results['failed'] += $missing;
            if (count($results['errors']) < $maxErrorDetails) {
                $results['errors'][] = "{$missing} product(s) not found.";
            }
        }

        $this->syncProgress($importJob, $processedRows, $results, true);

        $importJob->update([
            'status' => 'completed',
            'processed_rows' => $processedRows,
            'success_count' => $results['success'],
            'failed_count' => $results['failed'],
            'errors' => $results['errors'],
            'message' => "Pricing sync completed. Products: {$processedRows}. Source updated: {$results['source_updated']}. Competitor prices updated: {$results['competitor_prices_updated']}. Failed: {$results['failed']}.",
            'completed_at' => now(),
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        $importJob = BulkImportJob::find($this->importJobId);
        if (!$importJob) {
            return;
        }

        $importJob->update([
            'status' => 'failed',
            'message' => 'Pricing sync job failed: ' . $exception->getMessage(),
            'completed_at' => now(),
        ]);
    }

    private function syncProgress(BulkImportJob $importJob, int $processedRows, array $results, bool $force = false): void
    {
        if (!$force && !$this->shouldSyncProgress($processedRows)) {
            return;
        }

        $importJob->update([
            'processed_rows' => $processedRows,
            'success_count' => (int) ($results['success'] ?? 0),
            'failed_count' => (int) ($results['failed'] ?? 0),
            'errors' => $results['errors'] ?? [],
        ]);
    }

    private function shouldSyncProgress(int $processedRows): bool
    {
        if ($processedRows <= 0) {
            return false;
        }

        $everyRows = max(1, (int) env('IMPORT_PROGRESS_SYNC_EVERY_ROWS', 25));
        return $processedRows % $everyRows === 0;
    }

    private function getRowDelayMicroseconds(int $totalRows = 0): int
    {
        $baseDelayMs = (int) env('PRICING_SYNC_ROW_DELAY_MS', env('IMPORT_ROW_DELAY_MS', 75));
        $mediumDelayMs = (int) env('PRICING_SYNC_ROW_DELAY_MS_MEDIUM', $baseDelayMs + 25);
        $largeDelayMs = (int) env('PRICING_SYNC_ROW_DELAY_MS_LARGE', $baseDelayMs + 50);

        $mediumRowsStart = max(1, (int) env('IMPORT_MEDIUM_ROWS_START', 1000));
        $largeRowsStart = max($mediumRowsStart + 1, (int) env('IMPORT_LARGE_ROWS_START', 1500));

        if ($totalRows >= $largeRowsStart) {
            $delayMs = $largeDelayMs;
        } elseif ($totalRows >= $mediumRowsStart) {
            $delayMs = $mediumDelayMs;
        } else {
            $delayMs = $baseDelayMs;
        }

        return max(0, $delayMs) * 1000;
    }

    private function isManualProduct($odooId): bool
    {
        if (is_null($odooId)) {
            return true;
        }
        $odooIdString = (string) $odooId;
        return strpos($odooIdString, '002') === 0;
    }

    public function backoff(): int
    {
        return max(1, (int) env('IMPORT_JOB_RETRY_DELAY_SECONDS', 10));
    }
}

