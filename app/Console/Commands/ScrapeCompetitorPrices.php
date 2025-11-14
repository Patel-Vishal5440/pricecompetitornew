<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\ProductCompetitorPrice;
use Illuminate\Support\Facades\Log;

class ScrapeCompetitorPrices extends Command
{
    protected $signature = 'scrape:prices';
    protected $description = 'Scrape competitor prices using Symfony HttpClient';

    public function handle()
    {
        $competitor_scrape_price = ProductCompetitorPrice::all();
        $lastSiteProcessed = null;
        
        foreach ($competitor_scrape_price as $scrapePrice) {       
            try {
                $website = $scrapePrice->competitor_url;
                
                // Validate URL
                if (empty($website) || !filter_var($website, FILTER_VALIDATE_URL)) {
                    Log::error("Invalid URL format: " . ($website ?? 'null'));
                    $this->error("Invalid URL format: " . ($website ?? 'null'));
                    continue;
                }

                $currentSite = parse_url($website, PHP_URL_HOST);
                
                // If switching to a different site, add extra delay
                if ($lastSiteProcessed && $lastSiteProcessed !== $currentSite) {
                    $siteDelay = rand(5, 10);
                    $this->info("Switching from {$lastSiteProcessed} to {$currentSite}, waiting {$siteDelay} seconds...");
                    sleep($siteDelay);
                } else {
                    // Random delay between requests on same site
                    $delay = rand(2, 5);
                    sleep($delay);
                }

                $headers = $this->getRandomHeaders();
                $response = Http::withHeaders($headers)->timeout(20)->get($website);
                
                // Verify response status
                if (!$response->successful()) {
                    throw new \Exception("Invalid response status: " . $response->status());
                }
                
                $html = $response->body();
                $crawler = new Crawler($html);

                // Use the same scraping logic as ProductController
                if (strpos($website, 'injuredgadgets.com') !== false) {
                    $class = '.price-wrapper .price';
                    $amount = $crawler->filter('.price-wrapper')->attr('data-price-amount');
                } elseif (strpos($website, 'mobilesentrix.com') !== false) {
                    $class = '.regular-price.price';
                    $amount = $crawler->filter('.product-cart-pay')->attr('data-pp-amount');
                } else {
                    $class = '.price-final_price';
                    $amount = $crawler->filter('.price-wrapper')->attr('data-price-amount');
                }

                $priceElements = $crawler->filter($class);
                if ($priceElements->count() > 0 || $amount) {
                    if ($amount) {
                        // Use data attribute value
                    } else {
                        $priceText = $priceElements->first()->text();
                        preg_match('/[0-9]+(?:\.[0-9]{1,2})?/', $priceText, $matches);
                        $amount = $matches[0] ?? null;
                    }
                } else {
                    Log::error("No price elements found for {$website}");
                    throw new \Exception("No price elements found");
                }

                if (!$amount) {
                    Log::error("Price not found for {$website}");
                    throw new \Exception("Price not found");
                }

                $scrapePrice->update(['price' => $amount]);
                $this->info("Updated price for {$scrapePrice->competitor->name}: $" . $amount);
                Log::info("Updated price for {$scrapePrice->competitor->name}: $" . $amount);
                
                $lastSiteProcessed = $currentSite;

            } catch (\Exception $e) {
                Log::error("Failed to scrape {$website}: " . $e->getMessage());
                $this->error("Failed to scrape: {$website}");
                sleep(3); // Delay even after error
                $lastSiteProcessed = $currentSite ?? $lastSiteProcessed;
            }
        } 
        $this->info("All prices updated successfully");
    }

    private function getRandomHeaders()
    {
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/121.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1 Safari/605.1.15',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
        ];

        $acceptLanguages = [
            'en-US,en;q=0.9',
            'en-GB,en;q=0.9,en-US;q=0.8',
            'en-US,en;q=0.8,es;q=0.6',
            'en-CA,en;q=0.9,fr;q=0.8'
        ];

        return [
            'User-Agent' => $userAgents[array_rand($userAgents)],
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language' => $acceptLanguages[array_rand($acceptLanguages)],
            'Accept-Encoding' => 'gzip, deflate, br',
            'Connection' => 'keep-alive',
            'Upgrade-Insecure-Requests' => '1',
            'Sec-Fetch-Dest' => 'document',
            'Sec-Fetch-Mode' => 'navigate',
            'Sec-Fetch-Site' => 'none',
            'Cache-Control' => 'max-age=0'
        ];
    }
}
