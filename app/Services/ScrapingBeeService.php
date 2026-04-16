<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ScrapingBeeService
{
    /** @var string[] */
    private array $apiKeys;
    private string $baseUrl = 'https://app.scrapingbee.com/api/v1/';

    public function __construct()
    {
        $this->apiKeys = $this->parseKeys(config('services.scrapingbee.key'));
    }

    public function isEnabled(): bool
    {
        return !empty($this->apiKeys);
    }

    public function scrape(string $url): string
    {
        $apiKey = $this->pickKey();
        $renderJs = filter_var(env('SCRAPINGBEE_RENDER_JS', false), FILTER_VALIDATE_BOOL);
        $premiumProxy = filter_var(env('SCRAPINGBEE_PREMIUM_PROXY', false), FILTER_VALIDATE_BOOL);

        $response = Http::timeout(60)->get($this->baseUrl, [
            'api_key' => $apiKey,
            'url' => $url,
            'render_js' => $renderJs ? 'true' : 'false',
            'premium_proxy' => $premiumProxy ? 'true' : 'false',
        ]);

        $response->throw();

        return (string) $response->body();
    }

    /**
     * @return string[]
     */
    private function parseKeys(mixed $value): array
    {
        if (is_array($value)) {
            $keys = $value;
        } else {
            $keys = preg_split('/\s*,\s*/', (string) $value, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        }

        $keys = array_values(array_filter(array_map('trim', $keys), fn ($k) => $k !== ''));
        return array_values(array_unique($keys));
    }

    private function pickKey(): string
    {
        if (empty($this->apiKeys)) {
            throw new \RuntimeException('ScrapingBee API key is not configured.');
        }
        return $this->apiKeys[array_rand($this->apiKeys)];
    }
}

