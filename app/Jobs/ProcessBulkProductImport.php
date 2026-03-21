<?php

namespace App\Jobs;

use App\Models\BulkImportJob;
use App\Models\Category;
use App\Models\Competitor;
use App\Models\Product;
use App\Models\ProductCompetitorPrice;
use App\Services\OdooService;
use App\Traits\ScrapesCompetitorPrice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessBulkProductImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ScrapesCompetitorPrice;

    public $timeout = 3600;
    public $tries = 3;

    private int $importJobId;
    private string $filePath;

    /**
     * Create a new job instance.
     */
    public function __construct(int $importJobId, string $filePath)
    {
        $this->importJobId = $importJobId;
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     */
    public function handle(OdooService $odooService): void
    {
        $importJob = BulkImportJob::find($this->importJobId);
        if (!$importJob) {
            return;
        }
        $rowDelayMicroseconds = $this->getRowDelayMicroseconds((int) $importJob->total_rows);

        $importJob->update([
            'status' => 'processing',
            'started_at' => now(),
            'message' => 'Import is processing.',
        ]);

        $fullPath = storage_path('app/' . $this->filePath);
        if (!is_readable($fullPath)) {
            $importJob->update([
                'status' => 'failed',
                'message' => 'Uploaded file is not readable.',
                'completed_at' => now(),
            ]);
            return;
        }

        $competitors = Competitor::all();
        if ($competitors->isEmpty()) {
            $importJob->update([
                'status' => 'failed',
                'message' => 'No competitors found in database.',
                'completed_at' => now(),
            ]);
            return;
        }

        $competitorHostMap = [];
        foreach ($competitors as $competitor) {
            if (empty($competitor->website)) {
                continue;
            }

            $websiteHost = parse_url($competitor->website, PHP_URL_HOST);
            if (!$websiteHost) {
                $websiteHost = parse_url('https://' . ltrim($competitor->website, '/'), PHP_URL_HOST);
            }

            if ($websiteHost) {
                $websiteHost = strtolower(preg_replace('/^www\./', '', $websiteHost));
                $competitorHostMap[$websiteHost] = $competitor;
            }
        }

        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];
        $maxErrorDetails = 200;
        $processedRows = 0;

        $handle = fopen($fullPath, 'r');
        if ($handle === false) {
            $importJob->update([
                'status' => 'failed',
                'message' => 'Failed to open uploaded file.',
                'completed_at' => now(),
            ]);
            return;
        }

        $header = fgetcsv($handle);
        if ($header && !empty($header[0])) {
            $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
        }

        $rowNumber = 1;
        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            if (empty(array_filter($row))) {
                continue;
            }

            $processedRows++;

            if (count($row) < 2) {
                $results['failed']++;
                if (count($results['errors']) < $maxErrorDetails) {
                    $results['errors'][] = "Row {$rowNumber}: Insufficient columns.";
                }
                $this->syncProgress($importJob, $processedRows, $results);
                continue;
            }

            $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', $row[0]);
            $sku = trim($row[0]);
            $categoryName = isset($row[1]) ? trim($row[1]) : '';

            if (empty($sku)) {
                $results['failed']++;
                if (count($results['errors']) < $maxErrorDetails) {
                    $results['errors'][] = "Row {$rowNumber}: SKU is required.";
                }
                $this->syncProgress($importJob, $processedRows, $results);
                continue;
            }

            try {
                $rowErrors = [];
                $skipOdoo = (bool) env('BULK_IMPORT_SKIP_ODOO', false);

                $existingProduct = Product::where('default_code', $sku)->first();
                $productData = [];
                $productIdentifier = ['default_code' => $sku];

                if (!$skipOdoo) {
                    $response = $odooService->fetchProductBySku($sku);
                    if (!isset($response['success']) || !$response['success'] || empty($response['result'])) {
                        $results['failed']++;
                        $errorMsg = $response['message'] ?? 'Product not found in Odoo';
                        if (count($results['errors']) < $maxErrorDetails) {
                            $results['errors'][] = "Row {$rowNumber}: SKU '{$sku}' not found in Odoo - {$errorMsg}";
                        }
                        $this->syncProgress($importJob, $processedRows, $results);
                        continue;
                    }

                    $odooProduct = $response['result'][0];
                    $productIdentifier = ['odoo_id' => $odooProduct['id']];
                    $productData = [
                        'name' => $odooProduct['name'] ?? ($existingProduct->name ?? $sku),
                        'default_code' => $odooProduct['default_code'] ?? $sku,
                        'list_price' => $odooProduct['list_price'] ?? ($existingProduct->list_price ?? 0),
                        'cost' => $odooProduct['standard_price'] ?? ($existingProduct->cost ?? 0),
                        'barcode' => $odooProduct['barcode'] ?? ($existingProduct->barcode ?? null),
                    ];
                } else {
                    // Test mode: skip Odoo and use local SKU-only import.
                    $productData = [
                        'default_code' => $sku,
                        'name' => $existingProduct->name ?? $sku,
                    ];
                }

                if (!empty($categoryName)) {
                    $category = Category::whereRaw('LOWER(name) = ?', [strtolower($categoryName)])->first();
                    if ($category) {
                        $productData['category_id'] = $category->id;
                        $productData['category'] = $category->name;
                    } else {
                        $rowErrors[] = "Category '{$categoryName}' not found.";
                    }
                }

                $product = Product::updateOrCreate(
                    $productIdentifier,
                    $productData
                );

                for ($i = 2; $i < count($row); $i++) {
                    $url = trim($row[$i]);
                    if (empty($url)) {
                        continue;
                    }

                    if (!filter_var($url, FILTER_VALIDATE_URL)) {
                        $rowErrors[] = 'Column ' . ($i + 1) . ': Invalid URL format';
                        continue;
                    }

                    $matchedCompetitor = null;
                    $parsedHost = parse_url($url, PHP_URL_HOST);
                    $normalizedHost = $parsedHost ? strtolower(preg_replace('/^www\./', '', $parsedHost)) : null;
                    if ($normalizedHost && isset($competitorHostMap[$normalizedHost])) {
                        $matchedCompetitor = $competitorHostMap[$normalizedHost];
                    } else {
                        foreach ($competitors as $competitor) {
                            if ($competitor->website && $this->validateDomainMatch($url, $competitor->website)) {
                                $matchedCompetitor = $competitor;
                                break;
                            }
                        }
                    }

                    if (!$matchedCompetitor) {
                        $rowErrors[] = "Column " . ($i + 1) . ": URL domain '{$parsedHost}' does not match any competitor";
                        continue;
                    }

                    ProductCompetitorPrice::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'competitor_id' => $matchedCompetitor->id,
                        ],
                        ['competitor_url' => $url]
                    );
                }

                $hasCriticalError = false;
                foreach ($rowErrors as $error) {
                    if (stripos($error, 'category') !== false && stripos($error, 'not found') !== false) {
                        $hasCriticalError = true;
                    }
                    if (count($results['errors']) < $maxErrorDetails) {
                        $results['errors'][] = "Row {$rowNumber}: {$error}";
                    }
                }

                if ($hasCriticalError) {
                    $results['failed']++;
                } else {
                    $results['success']++;
                }
            } catch (\Throwable $e) {
                $results['failed']++;
                if (count($results['errors']) < $maxErrorDetails) {
                    $results['errors'][] = "Row {$rowNumber}: Exception processing SKU '{$sku}' - " . $e->getMessage();
                }
                Log::error('Queued bulk import row failed', [
                    'import_job_id' => $importJob->id,
                    'row' => $rowNumber,
                    'sku' => $sku,
                    'error' => $e->getMessage(),
                ]);
            }

            $this->syncProgress($importJob, $processedRows, $results);
            if ($rowDelayMicroseconds > 0) {
                usleep($rowDelayMicroseconds);
            }
        }

        fclose($handle);
        $this->syncProgress($importJob, $processedRows, $results, true);

        $importJob->update([
            'status' => 'completed',
            'processed_rows' => $processedRows,
            'success_count' => $results['success'],
            'failed_count' => $results['failed'],
            'errors' => $results['errors'],
            'message' => "Import completed. Success: {$results['success']}, Failed: {$results['failed']}.",
            'completed_at' => now(),
        ]);

        Storage::disk('local')->delete($this->filePath);
    }

    public function failed(\Throwable $exception): void
    {
        $importJob = BulkImportJob::find($this->importJobId);
        if (!$importJob) {
            return;
        }

        $importJob->update([
            'status' => 'failed',
            'message' => 'Import job failed: ' . $exception->getMessage(),
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
            'success_count' => $results['success'],
            'failed_count' => $results['failed'],
            'errors' => $results['errors'],
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
        $baseDelayMs = (int) env('PRODUCT_IMPORT_ROW_DELAY_MS', env('IMPORT_ROW_DELAY_MS', 75));
        $mediumDelayMs = (int) env('PRODUCT_IMPORT_ROW_DELAY_MS_MEDIUM', $baseDelayMs + 25);
        $largeDelayMs = (int) env('PRODUCT_IMPORT_ROW_DELAY_MS_LARGE', $baseDelayMs + 50);

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

    public function backoff(): int
    {
        return max(1, (int) env('IMPORT_JOB_RETRY_DELAY_SECONDS', 10));
    }
}
