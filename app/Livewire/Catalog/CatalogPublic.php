<?php

namespace App\Livewire\Catalog;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use Livewire\WithPagination;

class CatalogPublic extends Component
{
    use WithPagination;

    public $search = '';
    public $category_id = '';
    public $sortBy = 'name';
    public $sortDirection = 'asc';
    public $perPage = 12;

    protected $queryString = [
        'search' => ['except' => ''],
        'category_id' => ['except' => ''],
        'sortBy' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function addToCart($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            $this->dispatch('cart-error', 'Produk tidak ditemukan!');
            return;
        }

        if ($product->stock <= 0) {
            $this->dispatch('cart-error', 'Stok produk habis!');
            return;
        }

        // Simulasi cart dengan session sederhana
        $cart = session()->get('cart', []);
        $cart[] = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'image' => $product->image,
            'category' => $product->category->name,
            'qty' => 1
        ];
        session()->put('cart', $cart);

        $this->dispatch('cart-added', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function mount()
    {
        // Set default page title
        $this->title = 'Katalog Produk';
        $this->subtitle = 'Temukan produk terbaik kami';
    }

    public function render()
    {
        $query = Product::with('category')
            ->where('stock', '>', 0);

        // Filter by search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Filter by category
        if ($this->category_id) {
            $query->where('category_id', $this->category_id);
        }

        // Sort
        $sortFields = ['name', 'price', 'created_at'];
        if (in_array($this->sortBy, $sortFields)) {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        $products = $query->paginate($this->perPage);
        $categories = Category::get();

        return view('livewire.catalog.catalog-public', [
            'products' => $products,
            'categories' => $categories,
        ])->layout('components.layouts.app', [
            'title' => 'Katalog - Toko Barokah Jaya',
            'subtitle' => 'Belanja produk berkualitas dengan harga terbaik'
        ]);
    }
}