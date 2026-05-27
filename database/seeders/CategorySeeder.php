<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Makanan Khas Madura',
                'description' => 'Berbagai macam makanan tradisional khas Madura'
            ],
            [
                'name' => 'Minuman',
                'description' => 'Minuman segar dan tradisional'
            ],
            [
                'name' => 'Sembako',
                'description' => 'Kebutuhan pokok sehari-hari'
            ],
            [
                'name' => 'Snack & Camilan',
                'description' => 'Makanan ringan dan camilan'
            ],
            [
                'name' => 'Bumbu Dapur',
                'description' => 'Bumbu-bumbu masakan tradisional'
            ]
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'name' => $category['name'],
                'description' => $category['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}