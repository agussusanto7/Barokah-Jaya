<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;

class CategorySeederUpdated extends Seeder
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
            Category::updateOrCreate(
                ['name' => $category['name']], // Key untuk mencari
                [
                    'description' => $category['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('Categories seeded successfully!');
    }
}