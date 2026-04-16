<?php

namespace App\Traits;

use App\Services\ScraperService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

trait ScrapesCompetitorPrice
{
    private function parsePriceFromText(?string $text): ?string
    {
        if (!$text) {
            return null;
        }

        // Normalize formatted prices like "$1,234.56".
        $normalized = str_replace(',', '', $text);
        if (preg_match('/([0-9]+(?:\.[0-9]{1,2})?)/', $normalized, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function extractPriceFromSelectors(Crawler $crawler, array $selectors): ?string
    {
        foreach ($selectors as $selector) {
            try {
                if ($crawler->filter($selector)->count() > 0) {
                    $text = trim($crawler->filter($selector)->first()->text());
                    $amount = $this->parsePriceFromText($text);
                    if ($amount) {
                        return $amount;
                    }
                }
            } catch (\Throwable $e) {
                // Ignore selector-specific failures and continue with fallback selectors.
            }
        }

        return null;
    }

    public function validateDomainMatch($providedUrl, $expectedWebsite): bool
    {
        if (!$expectedWebsite) {
            Log::warning('Competitor website not set, skipping domain check.');
            return true;
        }

        $providedHost = parse_url($providedUrl, PHP_URL_HOST);
        $expectedHost = parse_url($expectedWebsite, PHP_URL_HOST);

        if (!$providedHost || !$expectedHost) {
            return false;
        }

        $providedHost = preg_replace('/^www\./', '', strtolower($providedHost));
        $expectedHost = preg_replace('/^www\./', '', strtolower($expectedHost));

        return $providedHost === $expectedHost;
    }

    public function scrapeCompetitorPrice(string $url): ?string
    {
        $html = null;
        $amount = null;
        $class = null;

        try {
            // Try direct fetch first (cheap). Only use scraper providers if blocked/challenged.
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.5',
                'Accept-Encoding' => 'gzip, deflate',
                'Connection' => 'keep-alive',
                'Upgrade-Insecure-Requests' => '1',
            ])->timeout(30)->get($url);

            $isCloudflareChallenge = $response->header('cf-mitigated') === 'challenge'
                || str_contains(strtolower((string) $response->body()), 'cf-challenge');

            if ($response->successful() && !$isCloudflareChallenge) {
                $html = $response->body();
            } else {
                /** @var ScraperService $scraper */
                $scraper = app(ScraperService::class);
                $scrapeResult = $scraper->scrapeOnce($url);

                if (($scrapeResult['ok'] ?? false) !== true) {
                    Log::warning('Scrape failed (direct + scraper providers)', [
                        'url' => $url,
                        'direct_status' => $response->status(),
                        'provider' => $scrapeResult['provider'] ?? null,
                        'message' => $scrapeResult['message'] ?? null,
                    ]);
                    return null;
                }

                $html = (string) ($scrapeResult['html'] ?? '');
            }

            // Keep legacy selector hint (not required, but harmless) for injuredgadgets.
            if (str_contains($url, 'injuredgadgets.com')) {
                $class = '.price-wrapper .price';
            }

            if (empty($html)) {
                Log::warning('Empty HTML response', ['url' => $url]);
                return null;
            }

            $crawler = new Crawler($html);

            if (str_contains($url, 'mobilesentrix.com')) {
                // Primary source (legacy/current button attribute).
                if ($crawler->filter('.product-cart-pay')->count() > 0) {
                    $amount = $crawler->filter('.product-cart-pay')->first()->attr('data-pp-amount');
                }

                // Fallbacks for current PDP markup variants.
                if (!$amount) {
                    $amount = $this->extractPriceFromSelectors($crawler, [
                        '.view-product-price .price-info-span',
                        '.view-product-price .regular-price .price',
                        '.view-product-price .price',
                        '.product-info-price .price',
                        '.product-info-main .price'
                    ]);
                }
            } elseif (str_contains($url, 'injuredgadgets.com')) {
                if ($crawler->filter('.price-wrapper')->count() > 0) {
                    $amount = $crawler->filter('.price-wrapper')->first()->attr('data-price-amount');
                }
            } else {
                $class = '.price-final_price';
                if ($crawler->filter('.price-wrapper')->count() > 0) {
                    $amount = $crawler->filter('.price-wrapper')->first()->attr('data-price-amount');
                }
            }

            if (!$amount && isset($class) && $crawler->filter($class)->count() > 0) {
                $text = $crawler->filter($class)->first()->text();
                preg_match('/[0-9]+(?:\.[0-9]{1,2})?/', $text, $matches);
                $amount = $matches[0] ?? null;
            }

            if ($amount) {
                Log::info('Successfully scraped price', ['url' => $url, 'amount' => $amount]);
            } else {
                Log::warning('No price found in HTML', ['url' => $url]);
            }

            return $amount;
        } catch (\Exception $e) {
            Log::error('Scrape Error', ['url' => $url, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return null;
        }
    }

    public function jsonError(string $message, int $status = 400)
    {
        return response()->json(['success' => false, 'message' => $message], $status);
    }
}
