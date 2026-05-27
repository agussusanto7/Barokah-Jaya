<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Category;

class ProductSeederUpdated extends Seeder
{
    public function run(): void
    {
        $products = [
            // Makanan Khas Madura
            [
                'category_name' => 'Makanan Khas Madura',
                'name' => 'Sate Madura',
                'sku' => 'MKM001',
                'description' => 'Sate ayam khas Madura dengan bumbu kacang spesial',
                'price' => 25000,
                'stock' => 50,
            ],
            [
                'category_name' => 'Makanan Khas Madura',
                'name' => 'Bebek Sinjay',
                'sku' => 'MKM002',
                'description' => 'Bebek goreng khas Sinjai Madura dengan sambal terasi',
                'price' => 35000,
                'stock' => 25,
            ],
            [
                'category_name' => 'Makanan Khas Madura',
                'name' => 'Rujak Madura',
                'sku' => 'MKM003',
                'description' => 'Rujak buah dengan bumbu petis khas Madura',
                'price' => 15000,
                'stock' => 30,
            ],

            // Minuman
            [
                'category_name' => 'Minuman',
                'name' => 'Es Dawet',
                'sku' => 'MIN001',
                'description' => 'Es dawet sirup khas Madura dengan santan gurih',
                'price' => 8000,
                'stock' => 60,
            ],
            [
                'category_name' => 'Minuman',
                'name' => 'Kopi Tuban',
                'sku' => 'MIN002',
                'description' => 'Kopi tuban khas Madura, aroma dan rasa yang khas',
                'price' => 5000,
                'stock' => 45,
            ],

            // Sembako
            [
                'category_name' => 'Sembako',
                'name' => 'Beras Pandan Wangi',
                'sku' => 'SEM001',
                'description' => 'Beras pandan wangi kualitas premium',
                'price' => 12500,
                'stock' => 100,
            ],
            [
                'category_name' => 'Sembako',
                'name' => 'Minyak Goreng',
                'sku' => 'SEM002',
                'description' => 'Minyak goreng kemasan 1 liter',
                'price' => 18000,
                'stock' => 75,
            ],

            // Snack & Camilan
            [
                'category_name' => 'Snack & Camilan',
                'name' => 'Kerupuk Udang',
                'sku' => 'SNK001',
                'description' => 'Kerupuk udang khas Madura, renyah dan gurih',
                'price' => 8000,
                'stock' => 65,
            ],
            [
                'category_name' => 'Snack & Camilan',
                'name' => 'Kue Lapis',
                'sku' => 'SNK002',
                'description' => 'Kue lapis tradisional Madura',
                'price' => 12000,
                'stock' => 30,
            ],

            // Bumbu Dapur
            [
                'category_name' => 'Bumbu Dapur',
                'name' => 'Bumbu Sate',
                'sku' => 'BUM001',
                'description' => 'Bumbu sate Madura siap pakai',
                'price' => 15000,
                'stock' => 25,
            ]
        ];

        foreach ($products as $productData) {
            // Cari category_id berdasarkan nama category
            $category = Category::where('name', $productData['category_name'])->first();

            if ($category) {
                Product::updateOrCreate(
                    ['sku' => $productData['sku']], // Key untuk mencari
                    [
                        'category_id' => $category->id,
                        'name' => $productData['name'],
                        'sku' => $productData['sku'],
                        'description' => $productData['description'],
                        'price' => $productData['price'],
                        'stock' => $productData['stock'],
                        'image' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        $this->command->info('Products seeded successfully!');
    }
}