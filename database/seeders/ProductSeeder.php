<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'odoo_id' => 1,
                'name' => 'iPhone 14 Pro Screen Replacement',
                'default_code' => 'IPH14-PRO-SCR',
                'list_price' => 299.99,
                'barcode' => '1234567890123',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'odoo_id' => 2,
                'name' => 'Samsung Galaxy S23 Battery',
                'default_code' => 'SAM-S23-BAT',
                'list_price' => 79.99,
                'barcode' => '1234567890124',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'odoo_id' => 3,
                'name' => 'iPhone 13 Charging Port',
                'default_code' => 'IPH13-CHG-PORT',
                'list_price' => 149.99,
                'barcode' => '1234567890125',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'odoo_id' => 4,
                'name' => 'Google Pixel 7 Camera Module',
                'default_code' => 'PIX7-CAM-MOD',
                'list_price' => 189.99,
                'barcode' => '1234567890126',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'odoo_id' => 5,
                'name' => 'OnePlus 11 Speaker Assembly',
                'default_code' => 'OP11-SPK-ASM',
                'list_price' => 39.99,
                'barcode' => '1234567890127',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
