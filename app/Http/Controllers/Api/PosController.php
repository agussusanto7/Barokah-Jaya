<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    /**
     * Process checkout
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|string',
            'cash_received' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request) {
            $subtotal = 0;
            $validatedItems = [];

            // Validate each item and check stock
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok tidak mencukupi untuk: {$product->name}",
                        'available_stock' => $product->stock,
                        'requested' => $item['quantity'],
                    ], 422);
                }

                $itemTotal = $product->price * $item['quantity'];
                $subtotal += $itemTotal;

                $validatedItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $itemTotal,
                ];
            }

            $discount = $request->get('discount_amount', 0);
            $total = $subtotal - $discount;

            if ($request->cash_received < $total) {
                return response()->json([
                    'success' => false,
                    'message' => 'Uang tunai tidak mencukupi',
                    'required' => $total,
                    'received' => $request->cash_received,
                ], 422);
            }

            $change = $request->cash_received - $total;

            // Create transaction
            $transaction = Transaction::create([
                'user_id' => $request->user()->id,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => 0, // You can add tax calculation here
                'total' => $total,
                'paid' => $request->cash_received,
                'change' => $change,
                'payment_method' => $request->payment_method,
            ]);

            // Create transaction details and update stock
            foreach ($validatedItems as $item) {
                // Create transaction detail
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Update product stock
                $item['product']->decrement('stock', $item['quantity']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Checkout berhasil',
                'transaction' => $transaction->load('details.product'),
                'summary' => [
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'total' => $total,
                    'cash_received' => $request->cash_received,
                    'change' => $change,
                ]
            ], 201);
        });
    }

    /**
     * Generate receipt
     */
    public function receipt(string $id)
    {
        $transaction = Transaction::with(['details.product', 'user'])->findOrFail($id);

        return response()->json([
            'transaction' => $transaction,
            'company_info' => [
                'name' => 'POS Madura',
                'address' => 'Madura, Indonesia',
                'phone' => '+62 xxx-xxxx-xxxx',
                'email' => 'info@posmadura.com',
            ]
        ]);
    }

    /**
     * Get daily sales report for POS
     */
    public function dailySales(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        $transactions = Transaction::whereDate('created_at', $date)
            ->with(['details.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        $summary = [
            'date' => $date,
            'total_transactions' => $transactions->count(),
            'total_revenue' => $transactions->sum('total'),
            'total_cash' => $transactions->where('payment_method', 'cash')->sum('total'),
            'total_card' => $transactions->where('payment_method', 'card')->sum('total'),
            'total_transfer' => $transactions->where('payment_method', 'transfer')->sum('total'),
            'total_items_sold' => $transactions->sum(function ($transaction) {
                return $transaction->details->sum('quantity');
            }),
            'average_transaction' => $transactions->count() > 0
                ? $transactions->sum('total') / $transactions->count()
                : 0,
        ];

        // Top selling products for the day
        $topProducts = \App\Models\TransactionDetail::whereHas('transaction', function ($query) use ($date) {
            $query->whereDate('created_at', $date);
        })
            ->with('product')
            ->selectRaw('product_id, SUM(quantity) as total_quantity, SUM(subtotal) as total_revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        return response()->json([
            'summary' => $summary,
            'top_products' => $topProducts,
            'recent_transactions' => $transactions->take(10),
        ]);
    }

    /**
     * Get all products for POS
     * Specially designed for POS interface with stock information
     */
    public function getProducts(Request $request)
    {
        $query = Product::with('category');

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

        // Only show products with stock > 0 (default for POS)
        if ($request->get('in_stock_only', true)) {
            $query->where('stock', '>', 0);
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->get('min_price'));
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->get('max_price'));
        }

        // Low stock warning
        $lowStockThreshold = $request->get('low_stock_threshold', 10);

        $products = $query->orderBy('name')
            ->get()
            ->map(function ($product) use ($lowStockThreshold) {
                return [
                    'id' => $product->id,
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'image' => $product->image,
                    'category' => $product->category,
                    'is_low_stock' => $product->stock <= $lowStockThreshold,
                    'is_out_of_stock' => $product->stock == 0,
                    'formatted_price' => 'Rp ' . number_format($product->price, 0, ',', '.'),
                    'stock_status' => $product->stock == 0 ? 'Habis' :
                                   ($product->stock <= $lowStockThreshold ? 'Stok Rendah' : 'Tersedia')
                ];
            });

        return response()->json([
            'success' => true,
            'products' => $products,
            'summary' => [
                'total_products' => $products->count(),
                'out_of_stock' => $products->where('stock', 0)->count(),
                'low_stock' => $products->where('is_low_stock', true)->count(),
                'in_stock' => $products->where('stock', '>', 0)->count(),
            ]
        ]);
    }

    /**
     * Quick stock check for POS
     */
    public function stockCheck(Request $request)
    {
        $query = Product::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Only show products with stock
        if ($request->get('in_stock', false)) {
            $query->where('stock', '>', 0);
        }

        $products = $query->with('category')
            ->orderBy('name')
            ->limit($request->get('limit', 20))
            ->get();

        return response()->json([
            'products' => $products,
        ]);
    }
}