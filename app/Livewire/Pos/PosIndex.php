<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('POS Kasir - Toko Barokah Jaya')]
class PosIndex extends Component
{
    public $categories;
    public $products = [];
    public $selectedCategory = '';
    public $search = '';
    public $cart = [];
    public $subtotal = 0;
    public $discount = 0;
    public $tax = 0;
    public $total = 0;
    public $paymentMethod = 'cash';
    public $paid = 0;
    public $change = 0;
    public $showPaymentModal = false;
    public $showSuccessModal = false;
    public $lastInvoice = '';

    public function mount()
    {
        $this->categories = Category::all();
        $this->loadProducts();
    }

    public function loadProducts()
    {
        $query = Product::with('category')->where('stock', '>', 0);

        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $this->products = $query->get();
    }

    public function updatedSelectedCategory()
    {
        $this->loadProducts();
    }

    public function updatedSearch()
    {
        $this->loadProducts();
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);

        if (!$product || $product->stock <= 0) {
            return;
        }

        $existingIndex = null;
        foreach ($this->cart as $index => $item) {
            if ($item['id'] === $productId) {
                $existingIndex = $index;
                break;
            }
        }

        if ($existingIndex !== null) {
            if ($this->cart[$existingIndex]['quantity'] < $product->stock) {
                $this->cart[$existingIndex]['quantity']++;
                $this->cart[$existingIndex]['subtotal'] = $this->cart[$existingIndex]['quantity'] * $this->cart[$existingIndex]['price'];
            }
        } else {
            $this->cart[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'subtotal' => $product->price,
                'stock' => $product->stock
            ];
        }

        $this->calculateTotal();
    }

    public function updateQuantity($index, $action)
    {
        if ($action === 'increase') {
            if ($this->cart[$index]['quantity'] < $this->cart[$index]['stock']) {
                $this->cart[$index]['quantity']++;
            }
        } elseif ($action === 'decrease') {
            if ($this->cart[$index]['quantity'] > 1) {
                $this->cart[$index]['quantity']--;
            }
        }

        $this->cart[$index]['subtotal'] = $this->cart[$index]['quantity'] * $this->cart[$index]['price'];
        $this->calculateTotal();
    }

    public function removeFromCart($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->subtotal = array_sum(array_column($this->cart, 'subtotal'));

        // Pastikan semua nilai numerik
        $discount = is_numeric($this->discount) ? (float) $this->discount : 0;
        $tax = is_numeric($this->tax) ? (float) $this->tax : 0;

        $this->total = (float) $this->subtotal - $discount + $tax;
    }


    public function updatedDiscount()
    {
        $this->calculateTotal();
    }

    public function updatedTax()
    {
        $this->calculateTotal();
    }

    public function openPaymentModal()
    {
        if (empty($this->cart)) {
            return;
        }
        $this->showPaymentModal = true;
        $this->paid = $this->total;
        $this->calculateChange();
    }

    public function updatedPaid()
    {
        $this->calculateChange();
    }

    public function calculateChange()
    {
        $paid = is_numeric($this->paid) ? (float) $this->paid : 0;
        $this->change = max(0, $paid - (float) $this->total);
    }


    public function processPayment()
    {
        $paid = (float) $this->paid;
        $total = (float) $this->total;

        if ($paid < $total) {
            session()->flash('error', 'Jumlah pembayaran kurang dari total transaksi.');
            return;
        }

        DB::beginTransaction();

        try {
            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'subtotal' => (float) $this->subtotal,
                'discount' => (float) $this->discount,
                'tax' => (float) $this->tax,
                'total' => $total,
                'paid' => $paid,
                'change' => (float) $this->change,
                'payment_method' => $this->paymentMethod
            ]);

            foreach ($this->cart as $item) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal']
                ]);

                Product::find($item['id'])->decrement('stock', $item['quantity']);
            }

            DB::commit();

            $this->lastInvoice = $transaction->invoice_number ?? '';
            $this->showPaymentModal = false;
            $this->showSuccessModal = true;

            $this->resetTransaction();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function resetTransaction()
    {
        $this->cart = [];
        $this->subtotal = 0;
        $this->discount = 0;
        $this->tax = 0;
        $this->total = 0;
        $this->paid = 0;
        $this->change = 0;
        $this->paymentMethod = 'cash';
        $this->loadProducts();
    }

    public function closeSuccessModal()
    {
        $this->showSuccessModal = false;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
    }

    public function render()
    {
        return view('livewire.pos.pos-index');
    }
}
