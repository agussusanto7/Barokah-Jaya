<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class UpdateSeedFromDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-seed-from-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update seeder files dengan data terbaru dari database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mengupdate data seed dari database...');

        // Update CategorySeeder
        $this->updateCategorySeeder();

        // Update ProductSeeder
        $this->updateProductSeeder();

        $this->info('Seeder files berhasil diupdate!');
    }

    private function updateCategorySeeder()
    {
        $categories = DB::table('categories')->get();

        $categoryArray = [];
        foreach ($categories as $category) {
            $categoryArray[] = [
                'name' => $category->name,
                'description' => $category->description
            ];
        }

        $content = "<?php\n\nnamespace Database\\Seeders;\n\nuse Illuminate\\Database\\Seeder;\nuse Illuminate\\Support\\Facades\\DB;\n\nclass CategorySeeder extends Seeder\n{\n    public function run(): void\n    {\n        \$categories = " . $this->formatArray($categoryArray) . ";\n\n        foreach (\$categories as \$category) {\n            DB::table('categories')->insert([\n                'name' => \$category['name'],\n                'description' => \$category['description'],\n                'created_at' => now(),\n                'updated_at' => now(),\n            ]);\n        }\n    }\n}";

        File::put(database_path('seeders/CategorySeeder.php'), $content);
        $this->info('CategorySeeder berhasil diupdate!');
    }

    private function updateProductSeeder()
    {
        $products = DB::table('products')->get();

        $productArray = [];
        foreach ($products as $product) {
            $productArray[] = [
                'category_id' => $product->category_id,
                'name' => $product->name,
                'sku' => $product->sku,
                'description' => $product->description,
                'price' => $product->price,
                'stock' => $product->stock,
                'image' => $product->image ?? 'null'
            ];
        }

        $content = "<?php\n\nnamespace Database\\Seeders;\n\nuse Illuminate\\Database\\Seeder;\nuse Illuminate\\Support\\Facades\\DB;\nuse Illuminate\\Support\\Str;\n\nclass ProductSeeder extends Seeder\n{\n    public function run(): void\n    {\n        \$products = " . $this->formatArray($productArray) . ";\n\n        foreach (\$products as \$product) {\n            DB::table('products')->insert([\n                'category_id' => \$product['category_id'],\n                'name' => \$product['name'],\n                'sku' => \$product['sku'],\n                'description' => \$product['description'],\n                'price' => \$product['price'],\n                'stock' => \$product['stock'],\n                'image' => \$product['image'],\n                'created_at' => now(),\n                'updated_at' => now(),\n            ]);\n        }\n    }\n}";

        File::put(database_path('seeders/ProductSeeder.php'), $content);
        $this->info('ProductSeeder berhasil diupdate!');
    }

    private function formatArray($array)
    {
        $output = "[\n";
        foreach ($array as $item) {
            $output .= "            [\n";
            foreach ($item as $key => $value) {
                if (is_string($value)) {
                    $output .= "                '{$key}' => '" . addslashes($value) . "',\n";
                } else {
                    $output .= "                '{$key}' => " . ($value === 'null' ? 'null' : $value) . ",\n";
                }
            }
            $output .= "            ],\n";
        }
        $output .= "        ]";

        return $output;
    }
}
