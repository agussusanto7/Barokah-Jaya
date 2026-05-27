<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Category;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Generate sales report
     */
    public function salesReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'group_by' => 'in:daily,weekly,monthly',
        ]);

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $groupBy = $request->get('group_by', 'daily');

        $transactions = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->with(['details.product', 'user']);

        // Group by period
        switch ($groupBy) {
            case 'daily':
                $groupByClause = 'DATE(created_at)';
                break;
            case 'weekly':
                $groupByClause = 'YEARWEEK(created_at)';
                break;
            case 'monthly':
                $groupByClause = 'DATE_FORMAT(created_at, "%Y-%m")';
                break;
        }

        $salesData = Transaction::selectRaw("$groupByClause as period, COUNT(*) as transactions, SUM(total) as revenue, AVG(total) as avg_transaction")
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        // Payment method breakdown
        $paymentBreakdown = Transaction::selectRaw('payment_method, COUNT(*) as count, SUM(total) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('payment_method')
            ->get();

        // Top selling products
        $topProducts = TransactionDetail::whereHas('transaction', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        })
            ->with('product')
            ->selectRaw('product_id, SUM(quantity) as total_quantity, SUM(subtotal) as total_revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'product' => $item->product,
                    'total_quantity' => $item->total_quantity,
                    'total_revenue' => $item->total_revenue,
                ];
            });

        // Sales by category
        $salesByCategory = TransactionDetail::whereHas('transaction', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        })
            ->with('product.category')
            ->selectRaw('products.category_id, SUM(transaction_details.quantity) as total_quantity, SUM(transaction_details.subtotal) as total_revenue')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->groupBy('products.category_id')
            ->orderByDesc('total_revenue')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->product->category,
                    'total_quantity' => $item->total_quantity,
                    'total_revenue' => $item->total_revenue,
                ];
            });

        // Customer statistics (top customers)
        $topCustomers = Transaction::with('user')
            ->selectRaw('user_id, COUNT(*) as transaction_count, SUM(total) as total_spent')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        return response()->json([
            'report_period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'group_by' => $groupBy,
            ],
            'summary' => [
                'total_transactions' => Transaction::whereBetween('created_at', [$startDate, $endDate])->count(),
                'total_revenue' => Transaction::whereBetween('created_at', [$startDate, $endDate])->sum('total'),
                'average_transaction' => Transaction::whereBetween('created_at', [$startDate, $endDate])->avg('total'),
                'unique_customers' => Transaction::whereBetween('created_at', [$startDate, $endDate])
                    ->whereNotNull('user_id')
                    ->distinct('user_id')->count(),
            ],
            'sales_data' => $salesData,
            'payment_breakdown' => $paymentBreakdown,
            'top_products' => $topProducts,
            'sales_by_category' => $salesByCategory,
            'top_customers' => $topCustomers,
        ]);
    }

    /**
     * Generate inventory report
     */
    public function inventoryReport(Request $request)
    {
        $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'low_stock_threshold' => 'nullable|integer|min:0',
        ]);

        $query = Product::with('category');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

        $lowStockThreshold = $request->get('low_stock_threshold', 10);

        $products = $query->get();

        // Calculate inventory statistics
        $totalProducts = $products->count();
        $totalStock = $products->sum('stock');
        $totalValue = $products->sum(function ($product) {
            return $product->stock * $product->price;
        });

        $lowStockProducts = $products->where('stock', '<=', $lowStockThreshold);
        $outOfStockProducts = $products->where('stock', 0);

        // Inventory by category
        $inventoryByCategory = $products->groupBy('category_id')
            ->map(function ($categoryProducts) {
                $category = $categoryProducts->first()->category;
                return [
                    'category' => $category,
                    'total_products' => $categoryProducts->count(),
                    'total_stock' => $categoryProducts->sum('stock'),
                    'total_value' => $categoryProducts->sum(function ($product) {
                        return $product->stock * $product->price;
                    }),
                ];
            })->values();

        // Get sales velocity (products sold in last 30 days)
        $salesVelocity = TransactionDetail::whereHas('transaction', function ($q) {
            $q->where('created_at', '>=', now()->subDays(30));
        })
            ->selectRaw('product_id, SUM(quantity) as total_sold')
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        // Calculate inventory turnover and recommendations
        $inventoryAnalysis = $products->map(function ($product) use ($salesVelocity, $lowStockThreshold) {
            $soldLast30Days = $salesVelocity->get($product->id)?->total_sold ?? 0;
            $dailyAverage = $soldLast30Days / 30;
            $daysOfStock = $dailyAverage > 0 ? $product->stock / $dailyAverage : 999;

            $status = 'normal';
            if ($product->stock == 0) {
                $status = 'out_of_stock';
            } elseif ($product->stock <= $lowStockThreshold) {
                $status = 'low_stock';
            } elseif ($daysOfStock > 90) {
                $status = 'overstock';
            }

            return [
                'product' => $product,
                'current_stock' => $product->stock,
                'sold_last_30_days' => $soldLast30Days,
                'daily_average' => round($dailyAverage, 2),
                'days_of_stock' => round($daysOfStock, 1),
                'status' => $status,
                'total_value' => $product->stock * $product->price,
            ];
        });

        return response()->json([
            'summary' => [
                'total_products' => $totalProducts,
                'total_stock_items' => $totalStock,
                'total_inventory_value' => $totalValue,
                'low_stock_products' => $lowStockProducts->count(),
                'out_of_stock_products' => $outOfStockProducts->count(),
                'average_stock_per_product' => $totalProducts > 0 ? round($totalStock / $totalProducts, 2) : 0,
            ],
            'inventory_by_category' => $inventoryByCategory,
            'inventory_analysis' => $inventoryAnalysis->sortBy('status')->values(),
            'low_stock_products' => $inventoryAnalysis->where('status', 'low_stock')->values(),
            'out_of_stock_products' => $inventoryAnalysis->where('status', 'out_of_stock')->values(),
            'overstock_products' => $inventoryAnalysis->where('status', 'overstock')->values(),
        ]);
    }
}