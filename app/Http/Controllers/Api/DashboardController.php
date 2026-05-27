<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function stats(Request $request)
    {
        $period = $request->get('period', 'today'); // today, week, month, year

        $query = Transaction::query();

        switch ($period) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
                break;
            case 'year':
                $query->whereYear('created_at', now()->year);
                break;
        }

        $stats = [
            'period' => $period,
            'total_transactions' => $query->count(),
            'total_revenue' => $query->sum('total'),
            'average_transaction_value' => $query->avg('total'),
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'low_stock_products' => Product::where('stock', '<', 10)->count(),
            'out_of_stock_products' => Product::where('stock', 0)->count(),
            'active_categories' => Category::has('products')->count(),
        ];

        // Payment method breakdown
        $stats['payment_methods'] = $query->selectRaw('payment_method, COUNT(*) as count, SUM(total) as total')
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method');

        // Daily sales trend (last 7 days)
        $stats['sales_trend'] = Transaction::selectRaw('DATE(created_at) as date, COUNT(*) as transactions, SUM(total) as revenue')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top selling products in the period
        $stats['top_products'] = \App\Models\TransactionDetail::whereHas('transaction', function ($q) use ($query) {
            if ($query->getQuery()->wheres) {
                $q->whereRaw("created_at IN (SELECT created_at FROM transactions WHERE " . $this->buildWhereClause($query->getQuery()->wheres) . ")");
            } else {
                $q->where('created_at', '>=', now()->subDays(30));
            }
        })
            ->with('product')
            ->selectRaw('product_id, SUM(quantity) as total_quantity, SUM(subtotal) as total_revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'product' => $item->product,
                    'total_quantity' => $item->total_quantity,
                    'total_revenue' => $item->total_revenue,
                ];
            });

        return response()->json([
            'stats' => $stats,
        ]);
    }

    private function buildWhereClause($wheres)
    {
        $conditions = [];
        foreach ($wheres as $where) {
            if (isset($where['column'], $where['operator'], $where['value'])) {
                $conditions[] = "{$where['column']} {$where['operator']} '{$where['value']}'";
            }
        }
        return implode(' AND ', $conditions);
    }
}