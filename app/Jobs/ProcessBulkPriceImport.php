<?php

namespace App\Jobs;

use App\Models\BulkImportJob;
use App\Models\Product;
use App\Services\OdooService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessBulkPriceImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;
    public $tries = 3;

    private int $importJobId;
    private string $filePath;

    public function __construct(int $importJobId, string $filePath)
    {
        $this->importJobId = $importJobId;
        $this->filePath = $filePath;
    }

    public function handle(OdooService $odooService): void
    {
        $skipOdoo = (bool) env('PRICE_IMPORT_SKIP_ODOO', env('BULK_IMPORT_SKIP_ODOO', false));
        
        $importJob = BulkImportJob::find($this->importJobId);
        if (!$importJob) {
            return;
        }
        $rowDelayMicroseconds = $this->getRowDelayMicroseconds((int) $importJob->total_rows);

        $importJob->update([
            'status' => 'processing',
            'started_at' => now(),
            'message' => 'Price import is processing.',
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
                    $results['errors'][] = "Row {$rowNumber}: Insufficient columns (expected: SKU, Price).";
                }
                $this->syncProgress($importJob, $processedRows, $results);
                continue;
            }

            $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', $row[0]);
            $sku = trim($row[0]);
            $price = trim($row[1]);

            if ($sku === '') {
                $results['failed']++;
                if (count($results['errors']) < $maxErrorDetails) {
                    $results['errors'][] = "Row {$rowNumber}: SKU is required.";
                }
                $this->syncProgress($importJob, $processedRows, $results);
                continue;
            }

            if ($price === '' || !is_numeric($price) || (float) $price < 0) {
                $results['failed']++;
                if (count($results['errors']) < $maxErrorDetails) {
                    $results['errors'][] = "Row {$rowNumber}: Valid price is required.";
                }
                $this->syncProgress($importJob, $processedRows, $results);
                continue;
            }

            try {
                $product = Product::where('default_code', $sku)->first();
                if (!$product) {
                    $results['failed']++;
                    if (count($results['errors']) < $maxErrorDetails) {
                        $results['errors'][] = "Row {$rowNumber}: Product with SKU '{$sku}' not found.";
                    }
                    $this->syncProgress($importJob, $processedRows, $results);
                    continue;
                }

                if ($skipOdoo) {
                    $product->update(['list_price' => (float) $price]);
                    $results['success']++;
                } else {
                    if (!$product->odoo_id) {
                        $results['failed']++;
                        if (count($results['errors']) < $maxErrorDetails) {
                            $results['errors'][] = "Row {$rowNumber}: Product with SKU '{$sku}' has no Odoo ID.";
                        }
                        $this->syncProgress($importJob, $processedRows, $results);
                        continue;
                    }

                    $response = $odooService->updateProductPrice($product->odoo_id, (float) $price);
                    if (isset($response['success']) && $response['success']) {
                        $product->update(['list_price' => (float) $price]);
                        $results['success']++;
                    } else {
                        $results['failed']++;
                        $errorMsg = $response['message'] ?? ($response['error'] ?? 'Unknown error');
                        if (count($results['errors']) < $maxErrorDetails) {
                            $results['errors'][] = "Row {$rowNumber}: Failed to update price for SKU '{$sku}' - {$errorMsg}";
                        }
                    }
                }
            } catch (\Throwable $e) {
                $results['failed']++;
                if (count($results['errors']) < $maxErrorDetails) {
                    $results['errors'][] = "Row {$rowNumber}: Exception updating price for SKU '{$sku}' - " . $e->getMessage();
                }
                Log::error('Queued price import row failed', [
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
            'message' => "Price import completed. Success: {$results['success']}, Failed: {$results['failed']}.",
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
            'message' => 'Price import job failed: ' . $exception->getMessage(),
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
        $baseDelayMs = (int) env('PRICE_IMPORT_ROW_DELAY_MS', env('IMPORT_ROW_DELAY_MS', 75));
        $mediumDelayMs = (int) env('PRICE_IMPORT_ROW_DELAY_MS_MEDIUM', $baseDelayMs + 25);
        $largeDelayMs = (int) env('PRICE_IMPORT_ROW_DELAY_MS_LARGE', $baseDelayMs + 50);

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
