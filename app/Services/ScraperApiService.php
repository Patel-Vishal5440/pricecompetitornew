<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ScraperApiService
{
    /** @var string[] */
    private array $apiKeys;
    private string $baseUrl = 'http://api.scraperapi.com';

    public function __construct()
    {
        $this->apiKeys = $this->parseKeys(config('services.scraperapi.key'));
    }

    public function isEnabled(): bool
    {
        return !empty($this->apiKeys);
    }

    public function scrape(string $url): string
    {
        $apiKey = $this->pickKey();
        $response = Http::timeout(60)->get($this->baseUrl, [
            'api_key' => $apiKey,
            'url' => $url,
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
            throw new \RuntimeException('ScraperAPI key is not configured.');
        }
        return $this->apiKeys[array_rand($this->apiKeys)];
    }
}

