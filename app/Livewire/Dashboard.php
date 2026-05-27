<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
#[Title('Dashboard - Toko Barokah Jaya')]
class Dashboard extends Component
{
    public $totalProducts;
    public $totalCategories;
    public $todayTransactions;
    public $todayRevenue;
    public $lowStockProducts;
    public $recentTransactions;
    public $topProducts;

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        $this->totalProducts = Product::count();
        $this->totalCategories = Category::count();
        $this->todayTransactions = Transaction::whereDate('created_at', today())->count();
        $this->todayRevenue = Transaction::whereDate('created_at', today())->sum('total');
        $this->lowStockProducts = Product::where('stock', '<', 10)->count();

        $this->recentTransactions = Transaction::with('user')
            ->latest()
            ->take(5)
            ->get();

        $this->topProducts = Product::select(
                'products.id',
                'products.name',
                'products.price',
                'products.stock',
                'products.category_id',
                'products.image',
                'products.created_at',
                'products.updated_at',
                DB::raw('COALESCE(SUM(transaction_details.quantity), 0) as total_sold')
            )
            ->leftJoin('transaction_details', 'products.id', '=', 'transaction_details.product_id')
            ->groupBy('products.id', 'products.name', 'products.price', 'products.stock', 'products.category_id', 'products.image', 'products.created_at', 'products.updated_at')
            ->orderBy('total_sold', 'DESC')
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
