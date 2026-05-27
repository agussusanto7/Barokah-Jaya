<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class Cart extends Component
{
    public $cart = [];
    public $total = 0;

    protected $listeners = ['cartUpdated' => '$refresh'];

    public function mount()
    {
        $this->loadCart();
    }

    public function hydrate()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        $cart = session()->get('cart', []);

        // Debug: Log cart data
        \Log::info('Cart data from session: ', $cart);

        // Load stock info for each item from database
        foreach ($cart as $index => $item) {
            if (!isset($item['stock'])) {
                $product = Product::find($item['id']);
                if ($product) {
                    $cart[$index]['stock'] = $product->stock;
                } else {
                    $cart[$index]['stock'] = 0;
                }
            }
        }

        session()->put('cart', $cart);
        $this->cart = $cart;
        $this->calculateTotal();
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);

        if (!$product || $product->stock <= 0) {
            $this->dispatch('cartError', 'Produk tidak tersedia');
            return;
        }

        $cart = session()->get('cart', []);

        // Cek apakah produk sudah ada di cart
        $existingIndex = -1;
        foreach ($cart as $index => $item) {
            if ($item['id'] == $productId) {
                $existingIndex = $index;
                break;
            }
        }

        if ($existingIndex >= 0) {
            // Update quantity jika produk sudah ada
            $newQty = $cart[$existingIndex]['qty'] + 1;
            if ($newQty <= $product->stock) {
                $cart[$existingIndex]['qty'] = $newQty;
                $this->dispatch('cartSuccess', 'Quantity updated!');
            } else {
                $this->dispatch('cartError', 'Stok tidak mencukupi');
                return;
            }
        } else {
            // Tambah produk baru
            $cart[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image,
                'category' => $product->category->name,
                'qty' => 1,
                'stock' => $product->stock
            ];
            $this->dispatch('cartSuccess', 'Produk ditambahkan ke keranjang!');
        }

        session()->put('cart', $cart);
        $this->loadCart();
        $this->dispatch('cartUpdated');
    }

    public function updateQuantity($index, $quantity)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$index])) {
            $product = Product::find($cart[$index]['id']);

            if ($quantity > 0 && $quantity <= $product->stock) {
                $cart[$index]['qty'] = $quantity;
                session()->put('cart', $cart);
                $this->loadCart();
                $this->dispatch('cartUpdated');
            } elseif ($quantity > $product->stock) {
                $this->dispatch('cartError', 'Stok tidak mencukupi');
            }
        }
    }

    public function removeFromCart($index)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$index])) {
            unset($cart[$index]);
            $cart = array_values($cart); // Re-index array
            session()->put('cart', $cart);
            $this->loadCart();
            $this->dispatch('cartUpdated');
            $this->dispatch('cartSuccess', 'Produk dihapus dari keranjang!');
        }
    }

    public function clearCart()
    {
        session()->forget('cart');
        $this->loadCart();
        $this->dispatch('cartUpdated');
        $this->dispatch('cartSuccess', 'Keranjang dikosongkan!');
    }

    private function calculateTotal()
    {
        $this->total = 0;
        foreach ($this->cart as $item) {
            $this->total += $item['price'] * $item['qty'];
        }
    }

    public function checkout()
    {
        if (!Auth::check() || !Auth::user()->isCustomer()) {
            return redirect()->route('login');
        }

        if (empty($this->cart)) {
            $this->dispatch('cartError', 'Keranjang belanja kosong!');
            return;
        }

        // Redirect ke checkout page (akan dibuat selanjutnya)
        return redirect()->route('customer.checkout');
    }

    public function getCartCountProperty()
    {
        $cart = session()->get('cart', []);
        return count($cart);
    }

    public function render()
    {
        return view('livewire.customer.cart')
            ->layout('components.layouts.app', [
                'title' => 'Keranjang Belanja',
                'subtitle' => 'Kelola produk di keranjang Anda'
            ]);
    }
}
