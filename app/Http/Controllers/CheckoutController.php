<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        if (!auth()->check() || !auth()->user()->isCustomer()) {
            return redirect()->route('login');
        }

        if (!session()->has('cart') || count(session()->get('cart')) == 0) {
            return redirect()->route('cart.simple')->with('error', 'Keranjang belanja kosong!');
        }

        return view('checkout');
    }

    public function process(Request $request)
    {
        if (!auth()->check() || !auth()->user()->isCustomer()) {
            return redirect()->route('login');
        }

        $request->validate([
            'phone' => 'required|string|regex:/^[0-9]{10,13}$/',
            'address' => 'required|string|min:10',
            'payment_method' => 'required|in:transfer,cod',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.simple')->with('error', 'Keranjang belanja kosong!');
        }

        try {
            DB::beginTransaction();

            // Calculate total
            $total = 0;
            foreach ($cart as $item) {
                $total += $item['price'] * $item['qty'];
            }

            // Create transaction
            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'subtotal' => $total,
                'total' => $total,
                'paid' => 0,
                'payment_method' => $request->payment_method,
            ]);

            // Create transaction details and update stock
            foreach ($cart as $item) {
                $product = Product::find($item['id']);

                if (!$product) {
                    throw new \Exception('Product not found: ' . $item['name']);
                }

                if ($product->stock < $item['qty']) {
                    throw new \Exception('Insufficient stock for product: ' . $item['name']);
                }

                // Create transaction detail
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['qty'],
                ]);

                // Update product stock
                $product->decrement('stock', $item['qty']);
            }

            DB::commit();

            // Clear cart
            session()->forget('cart');

            return redirect()->route('checkout.success', $transaction->id);

        } catch (\Exception $e) {
            DB::rollBack();
            // Log error for debugging
            \Log::error('Checkout Error: ' . $e->getMessage());
            \Log::error('Stack Trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function success($transactionId)
    {
        $transaction = Transaction::with('details.product')
                                ->where('id', $transactionId)
                                ->where('user_id', auth()->id())
                                ->first();

        if (!$transaction) {
            return redirect()->route('catalog')->with('error', 'Transaksi tidak ditemukan');
        }

        return view('checkout-success', compact('transaction'));
    }
}
