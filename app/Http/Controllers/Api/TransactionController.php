<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the transactions.
     */
    public function index(Request $request)
    {
        $query = Transaction::with(['details.product', 'user']);

        // Filter by date range
        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->get('start_date'));
        }
        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->get('end_date'));
        }

        // Search by invoice number
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('invoice_number', 'like', "%{$search}%");
        }

        // Filter by payment method
        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->get('payment_method'));
        }

        $transactions = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'transactions' => $transactions,
        ]);
    }

    /**
     * Store a newly created transaction.
     */
    public function store(Request $request)
    {
        $request->validate([
            'details' => 'required|array|min:1',
            'details.*.product_id' => 'required|exists:products,id',
            'details.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|string',
            'paid' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request) {
            $subtotal = 0;
            $details = [];

            // Calculate subtotal and validate stock
            foreach ($request->details as $item) {
                $product = \App\Models\Product::findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    return response()->json([
                        'message' => "Insufficient stock for product: {$product->name}",
                        'available_stock' => $product->stock,
                        'requested' => $item['quantity'],
                    ], 422);
                }

                $itemSubtotal = $product->price * $item['quantity'];
                $subtotal += $itemSubtotal;

                $details[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $itemSubtotal,
                ];

                // Update stock
                $product->decrement('stock', $item['quantity']);
            }

            $discount = $request->get('discount', 0);
            $tax = 0; // You can add tax logic here
            $total = $subtotal - $discount + $tax;

            if ($request->paid < $total) {
                return response()->json([
                    'message' => 'Insufficient payment',
                    'total' => $total,
                    'paid' => $request->paid,
                ], 422);
            }

            $change = $request->paid - $total;

            // Create transaction
            $transaction = Transaction::create([
                'user_id' => $request->user()->id,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'paid' => $request->paid,
                'change' => $change,
                'payment_method' => $request->payment_method,
            ]);

            // Create transaction details
            foreach ($details as &$detail) {
                $detail['transaction_id'] = $transaction->id;
            }
            TransactionDetail::insert($details);

            return response()->json([
                'message' => 'Transaction created successfully',
                'transaction' => $transaction->load('details.product'),
            ], 201);
        });
    }

    /**
     * Display the specified transaction.
     */
    public function show(string $id)
    {
        $transaction = Transaction::with(['details.product', 'user'])->findOrFail($id);

        return response()->json([
            'transaction' => $transaction,
        ]);
    }

    /**
     * Update the specified transaction.
     */
    public function update(Request $request, string $id)
    {
        // For POS systems, transactions are typically not updated
        // Only allow status updates or similar non-critical changes

        $transaction = Transaction::findOrFail($id);

        // Example: update payment method or notes
        $request->validate([
            'payment_method' => 'sometimes|required|string',
        ]);

        $transaction->update($request->only('payment_method'));

        return response()->json([
            'message' => 'Transaction updated successfully',
            'transaction' => $transaction->load('details.product'),
        ]);
    }

    /**
     * Remove the specified transaction.
     */
    public function destroy(string $id)
    {
        return DB::transaction(function () use ($id) {
            $transaction = Transaction::with('details')->findOrFail($id);

            // Restore stock
            foreach ($transaction->details as $detail) {
                $detail->product->increment('stock', $detail->quantity);
            }

            // Delete transaction details first
            $transaction->details()->delete();

            // Delete transaction
            $transaction->delete();

            return response()->json([
                'message' => 'Transaction deleted successfully',
            ]);
        });
    }

    /**
     * Get daily report
     */
    public function dailyReport(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        $transactions = Transaction::whereDate('created_at', $date)
            ->with(['details.product'])
            ->get();

        $summary = [
            'date' => $date,
            'total_transactions' => $transactions->count(),
            'total_revenue' => $transactions->sum('total'),
            'total_items_sold' => $transactions->sum(function ($transaction) {
                return $transaction->details->sum('quantity');
            }),
            'payment_methods' => $transactions->groupBy('payment_method')
                ->map->sum('total')
        ];

        return response()->json([
            'summary' => $summary,
            'transactions' => $transactions,
        ]);
    }

    /**
     * Get transaction summary
     */
    public function summary(Request $request)
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

        $summary = [
            'period' => $period,
            'total_transactions' => $query->count(),
            'total_revenue' => $query->sum('total'),
            'average_transaction_value' => $query->avg('total'),
            'best_selling_products' => $this->getBestSellingProducts($query),
        ];

        return response()->json([
            'summary' => $summary,
        ]);
    }

    private function getBestSellingProducts($transactionQuery)
    {
        $transactionIds = $transactionQuery->pluck('id');

        return \App\Models\TransactionDetail::whereIn('transaction_id', $transactionIds)
            ->with('product')
            ->selectRaw('product_id, SUM(quantity) as total_quantity, SUM(subtotal) as total_revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();
    }
}