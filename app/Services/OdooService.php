<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\OdooUser;

class OdooService
{
    protected $url;
    protected $db;
    protected $username;
    protected $api_key;
    protected $user_id;

    public function __construct()
    {
        $this->url = config('services.odoo.url');
        $this->db = config('services.odoo.db');
        $this->username = config('services.odoo.username');
        $this->api_key = config('services.odoo.api_key');
        $this->user_id = OdooUser::where('username', $this->username)->first()->odoo_user_id ?? null;
    }

    public function authenticate()
    {
        $response = Http::post($this->url, [
            "jsonrpc" => "2.0",
            "method" => "call",
            "params" => [
                "service" => "common",
                "method" => "authenticate",
                "args" => [
                    $this->db,
                    $this->username,
                    $this->api_key,
                    []
                ]
            ]
        ]);
        $result = $response->json();
        $this->storeOdooCredentials($this->username, $this->api_key, $result['result']);
        return $result['result'] ?? null;
    }
    public function fetchProducts()
    {   
        if (!$this->user_id) {
            $result = $this->authenticate();
            $this->user_id = $result;
        }
        $response = Http::post($this->url, [
            "jsonrpc" => "2.0",
            "method" => "call",
            "params" => [
                "service" => "object",
                "method" => "execute_kw",
                "args" => [
                    $this->db,
                    $this->user_id,
                    $this->api_key,
                    "product.product",
                    "search_read",
                    [],
                    [
                        "fields" => ["id", "name", "default_code", "list_price", "standard_price", "qty_available", "barcode"],
                        "limit" => 10
                    ]
                ]
            ]
        ]);
        return $response->json();
    }
    public function updateProductPrice($productId, $newPrice)
    {
        try {
            // Ensure authentication
            if (!$this->user_id) {
                $result = $this->authenticate();
                $this->user_id = $result;
            }
            
            // First, update the price
            $updateResponse = Http::post($this->url, [
                "jsonrpc" => "2.0",
                "id" => 10,
                "method" => "call",
                "params" => [
                    "service" => "object",
                    "method" => "execute_kw",
                    "args" => [
                        $this->db,
                        $this->user_id,
                        $this->api_key,
                        "product.product",
                        "write",
                        [[intval($productId)], ['list_price' => floatval($newPrice)]]
                    ]
                ]
            ]);

            // dd("OKQQ");
            $updateResult = $updateResponse->json();

            if (isset($updateResult['error'])) {
                return [
                    'success' => false,
                    'message' => $updateResult['error']['data']['message'] ?? 'Failed to update price in Odoo',
                    'error' => $updateResult['error']
                ];
            }

            $readResponse = Http::post($this->url, [
                "jsonrpc" => "2.0",
                "id" => 11,
                "method" => "call",
                "params" => [
                    "service" => "object",
                    "method" => "execute_kw",
                    "args" => [
                        $this->db,
                        $this->user_id,
                        $this->api_key,
                        "product.product",
                        "read",
                        [[intval($productId)]],
                        ["fields" => ["list_price"]]
                    ]
                ]
            ]);

            $readResult = $readResponse->json();
         

            if (isset($readResult['error'])) {
                return [
                    'success' => false,
                    'message' => 'Price updated but failed to verify new value',
                    'error' => $readResult['error']
                ];
            }

            return [
                'success' => true,
                'message' => 'Price updated successfully in Odoo',
                'data' => [
                    'product_id' => $productId,
                    'new_price' => $readResult['result'][0]['list_price'] ?? $newPrice
                ]
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Request failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }
    public function fetchSpecificProduct($odooId)
    {
        try {
            if (!$this->user_id) {
                $result = $this->authenticate();
                $this->user_id = $result;
            }
            
            $response = Http::post($this->url, [
                "jsonrpc" => "2.0",
                "id" => 10,
                "method" => "call",
                "params" => [
                    "service" => "object",
                    "method" => "execute_kw",
                    "args" => [
                        $this->db,
                        $this->user_id,
                        $this->api_key,
                        "product.product",
                        "search_read",
                        [[['id', '=', (int)$odooId]]],
                        [
                            "fields" => ["id", "name", "default_code", "list_price", "standard_price", "qty_available", "barcode"],
                            "limit" => 1
                        ]
                    ]
                ]
            ]);

            $result = $response->json();


            if (isset($result['error'])) {
                return [
                    'success' => false,
                    'message' => $result['error']['data']['message'] ?? 'Error fetching product from Odoo',
                    'error' => $result['error']
                ];
            }

            if (empty($result['result'])) {
                return [
                    'success' => false,
                    'message' => 'Product not found in Odoo',
                    'data' => null
                ];
            }

            return [
                'success' => true,
                'message' => 'Product fetched successfully from Odoo',
                'result' => $result['result']
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to fetch product from Odoo',
                'error' => $e->getMessage()
            ];
        }
    }

    public function fetchProductBySku($sku)
    {
        try {
            if (!$this->user_id) {
                $result = $this->authenticate();
                $this->user_id = $result;
            }
            
            $response = Http::post($this->url, [
                "jsonrpc" => "2.0",
                "id" => 11,
                "method" => "call",
                "params" => [
                    "service" => "object",
                    "method" => "execute_kw",
                    "args" => [
                        $this->db,
                        $this->user_id,
                        $this->api_key,
                        "product.product",
                        "search_read",
                        [[['default_code', '=', $sku]]],
                        [
                            "fields" => ["id", "name", "default_code", "list_price", "standard_price", "qty_available", "barcode"],
                            "limit" => 1
                        ]
                    ]
                ]
            ]);

            $result = $response->json();

            if (isset($result['error'])) {
                return [
                    'success' => false,
                    'message' => $result['error']['data']['message'] ?? 'Error fetching product from Odoo by SKU',
                    'error' => $result['error']
                ];
            }

            if (empty($result['result'])) {
                return [
                    'success' => false,
                    'message' => 'Product not found in Odoo by SKU',
                    'data' => null
                ];
            }

            return [
                'success' => true,
                'message' => 'Product fetched successfully from Odoo by SKU',
                'result' => $result['result']
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to fetch product from Odoo by SKU',
                'error' => $e->getMessage()
            ];
        }
    }
    public function storeOdooCredentials($username, $token, $userId)
    {
        OdooUser::updateOrCreate(
            ['username' => $username],
            [
                'api_key' => $token,
                'odoo_user_id' => $userId,
            ]
        );
    }
}