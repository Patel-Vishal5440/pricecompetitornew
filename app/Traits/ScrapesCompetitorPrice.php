<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

trait ScrapesCompetitorPrice
{
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
            if (str_contains($url, 'injuredgadgets.com')) {
                $response = Http::timeout(30)->get('http://api.scraperapi.com', [
                    'api_key' => env('CAPTCHA_API_KEY'),
                    'url' => $url
                ]);
                if (!$response->successful()) {
                    Log::warning('ScraperAPI failed for injuredgadgets.com', ['url' => $url, 'status' => $response->status()]);
                    return null;
                }

                $html = $response->body();
                $class = '.price-wrapper .price';
            } else {
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                    'Accept-Language' => 'en-US,en;q=0.5',
                    'Accept-Encoding' => 'gzip, deflate',
                    'Connection' => 'keep-alive',
                    'Upgrade-Insecure-Requests' => '1',
                ])->timeout(30)->get($url);

                if (!$response->successful()) {
                    Log::warning('HTTP request failed', ['url' => $url, 'status' => $response->status()]);
                    return null;
                }

                $html = $response->body();
            }

            if (empty($html)) {
                Log::warning('Empty HTML response', ['url' => $url]);
                return null;
            }

            $crawler = new Crawler($html);

            if (str_contains($url, 'mobilesentrix.com')) {
                $amount = $crawler->filter('.product-cart-pay')->attr('data-pp-amount');
            } elseif (str_contains($url, 'injuredgadgets.com')) {
                $amount = $crawler->filter('.price-wrapper')->attr('data-price-amount');
            } else {
                $class = '.price-final_price';
                $amount = $crawler->filter('.price-wrapper')->attr('data-price-amount');
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
