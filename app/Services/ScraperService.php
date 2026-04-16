<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ScraperService
{
    public function __construct(
        private ScrapingBeeService $scrapingBee,
        private ScraperApiService $scraperApi,
    ) {
    }

    /**
     * Pick one provider per call (random), scrape, and return HTML.
     *
     * @return array{ok: true, provider: string, html: string}|array{ok: false, provider?: string, message: string}
     */
    public function scrapeOnce(string $url): array
    {
        $providers = $this->enabledProviders();
        if (empty($providers)) {
            return ['ok' => false, 'message' => 'No scraper provider is enabled (missing API keys).'];
        }

        $picked = $this->pickProvider($providers);

        try {
            $html = $this->scrapeWithProvider($picked, $url);
            return ['ok' => true, 'provider' => $picked, 'html' => $html];
        } catch (\Throwable $e) {
            Log::warning('Scrape failed (picked provider)', [
                'provider' => $picked,
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            // Fallback: try the other enabled providers (still "one-time pick" primary, but we don't fail hard).
            foreach ($providers as $provider) {
                if ($provider === $picked) {
                    continue;
                }
                try {
                    $html = $this->scrapeWithProvider($provider, $url);
                    return ['ok' => true, 'provider' => $provider, 'html' => $html];
                } catch (\Throwable $ignored) {
                    Log::warning('Scrape failed (fallback provider)', [
                        'provider' => $provider,
                        'url' => $url,
                        'error' => $ignored->getMessage(),
                    ]);
                }
            }

            return ['ok' => false, 'provider' => $picked, 'message' => $e->getMessage()];
        }
    }

    /**
     * @param string[] $providers
     */
    private function pickProvider(array $providers): string
    {
        return $providers[array_rand($providers)];
    }

    /**
     * @return string[]
     */
    private function enabledProviders(): array
    {
        $providers = [];
        if ($this->scrapingBee->isEnabled()) {
            $providers[] = 'scrapingbee';
        }
        if ($this->scraperApi->isEnabled()) {
            $providers[] = 'scraperapi';
        }
        return $providers;
    }

    private function scrapeWithProvider(string $provider, string $url): string
    {
        return match ($provider) {
            'scrapingbee' => $this->scrapingBee->scrape($url),
            'scraperapi' => $this->scraperApi->scrape($url),
            default => throw new \InvalidArgumentException("Unknown provider: {$provider}"),
        };
    }
}

