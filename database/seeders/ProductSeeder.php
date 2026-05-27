<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Makanan Khas Madura (Category 1)
            [
                'category_id' => 1,
                'name' => 'Sate Madura',
                'sku' => 'MKM001',
                'description' => 'Sate ayam khas Madura dengan bumbu kacang spesial',
                'price' => 25000,
                'stock' => 50,
                'image' => null,
            ],
            [
                'category_id' => 1,
                'name' => 'Bebek Sinjay',
                'sku' => 'MKM002',
                'description' => 'Bebek goreng khas Sinjai Madura dengan sambal terasi',
                'price' => 35000,
                'stock' => 25,
                'image' => null,
            ],
            [
                'category_id' => 1,
                'name' => 'Rujak Madura',
                'sku' => 'MKM003',
                'description' => 'Rujak buah dengan bumbu petis khas Madura',
                'price' => 15000,
                'stock' => 30,
                'image' => null,
            ],
            [
                'category_id' => 1,
                'name' => 'Nasi Serpang',
                'sku' => 'MKM004',
                'description' => 'Nasi campur khas Sumenep dengan lauk pauk lengkap',
                'price' => 20000,
                'stock' => 40,
                'image' => null,
            ],

            // Minuman (Category 2)
            [
                'category_id' => 2,
                'name' => 'Es Dawet',
                'sku' => 'MIN001',
                'description' => 'Es dawet sirup khas Madura dengan santan gurih',
                'price' => 8000,
                'stock' => 60,
                'image' => null,
            ],
            [
                'category_id' => 2,
                'name' => 'Kopi Tuban',
                'sku' => 'MIN002',
                'description' => 'Kopi tuban khas Madura, aroma dan rasa yang khas',
                'price' => 5000,
                'stock' => 45,
                'image' => null,
            ],
            [
                'category_id' => 2,
                'name' => 'Es Kelapa Muda',
                'sku' => 'MIN003',
                'description' => 'Es kelapa muda segar langsung dari pohon',
                'price' => 12000,
                'stock' => 35,
                'image' => null,
            ],
            [
                'category_id' => 2,
                'name' => 'Teh Jahe Madura',
                'sku' => 'MIN004',
                'description' => 'Teh jahe hangat khas Madura, menghangatkan badan',
                'price' => 7000,
                'stock' => 55,
                'image' => null,
            ],

            // Sembako (Category 3)
            [
                'category_id' => 3,
                'name' => 'Beras Pandan Wangi',
                'sku' => 'SEM001',
                'description' => 'Beras pandan wangi kualitas premium',
                'price' => 12500,
                'stock' => 100,
                'image' => null,
            ],
            [
                'category_id' => 3,
                'name' => 'Minyak Goreng',
                'sku' => 'SEM002',
                'description' => 'Minyak goreng kemasan 1 liter',
                'price' => 18000,
                'stock' => 75,
                'image' => null,
            ],
            [
                'category_id' => 3,
                'name' => 'Gula Pasir',
                'sku' => 'SEM003',
                'description' => 'Gula pasir kemasan 1 kg',
                'price' => 14000,
                'stock' => 80,
                'image' => null,
            ],
            [
                'category_id' => 3,
                'name' => 'Telur Ayam',
                'sku' => 'SEM004',
                'description' => 'Telur ayam negeri segar per kg',
                'price' => 26000,
                'stock' => 40,
                'image' => null,
            ],

            // Snack & Camilan (Category 4)
            [
                'category_id' => 4,
                'name' => 'Kerupuk Udang',
                'sku' => 'SNK001',
                'description' => 'Kerupuk udang khas Madura, renyah dan gurih',
                'price' => 8000,
                'stock' => 65,
                'image' => null,
            ],
            [
                'category_id' => 4,
                'name' => 'Kue Lapis',
                'sku' => 'SNK002',
                'description' => 'Kue lapis tradisional Madura',
                'price' => 12000,
                'stock' => 30,
                'image' => null,
            ],

            // Bumbu Dapur (Category 5)
            [
                'category_id' => 5,
                'name' => 'Bumbu Sate',
                'sku' => 'BUM001',
                'description' => 'Bumbu sate Madura siap pakai',
                'price' => 15000,
                'stock' => 25,
                'image' => null,
            ]
        ];

        foreach ($products as $product) {
            DB::table('products')->insert([
                'category_id' => $product['category_id'],
                'name' => $product['name'],
                'sku' => $product['sku'],
                'description' => $product['description'],
                'price' => $product['price'],
                'stock' => $product['stock'],
                'image' => $product['image'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}