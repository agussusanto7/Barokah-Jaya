<div class="min-h-screen bg-slate-50">
    @include('livewire.includes.sidebar')

    <div class="lg:pl-64">
        @include('livewire.includes.header', ['title' => 'Point of Sale', 'subtitle' => 'Sistem kasir'])

        <main class="p-6">
            @if (session()->has('error'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-red-800 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Product List -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Search & Filter -->
                    <div class="bg-white rounded-xl p-4 border border-slate-200 card-shadow">
                        <div class="flex flex-col md:flex-row gap-3">
                            <div class="flex-1 relative">
                                <input type="text" wire:model.live.debounce.300ms="search"
                                    placeholder="Cari produk..."
                                    class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900 focus:border-transparent">
                                <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2.5" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <select wire:model.live="selectedCategory"
                                class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900 focus:border-transparent">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                        @forelse($products as $product)
                            <div wire:click="addToCart({{ $product->id }})"
                                class="bg-white rounded-xl p-4 border border-slate-200 card-shadow cursor-pointer hover:shadow-lg hover:border-slate-300 transition-all">
                                @if ($product->image)
                                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                                        class="w-full h-32 object-cover rounded-lg mb-3">
                                @else
                                    <div
                                        class="w-full h-32 bg-slate-100 rounded-lg flex items-center justify-center mb-3">
                                        <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                @endif
                                <h3 class="font-semibold text-slate-900 mb-1 text-sm">{{ $product->name }}</h3>
                                <p class="text-xs text-slate-500 mb-2">Stok: {{ $product->stock }}</p>
                                <p class="font-bold text-slate-900">Rp {{ number_format($product->price, 0, ',', '.') }}
                                </p>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-12">
                                <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <p class="text-slate-500">Tidak ada produk tersedia</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Cart -->
                <div class="bg-white rounded-xl border border-slate-200 card-shadow h-fit sticky top-24">
                    <div class="p-4 border-b border-slate-200">
                        <h3 class="font-bold text-slate-900">Keranjang</h3>
                    </div>

                    <div class="p-4 space-y-3 max-h-96 overflow-y-auto">
                        @forelse($cart as $index => $item)
                            <div class="flex items-center space-x-3 p-3 bg-slate-50 rounded-lg">
                                <div class="flex-1">
                                    <p class="font-medium text-slate-900 text-sm">{{ $item['name'] }}</p>
                                    <p class="text-xs text-slate-500">Rp
                                        {{ number_format($item['price'], 0, ',', '.') }}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button wire:click="updateQuantity({{ $index }}, 'decrease')"
                                        class="w-6 h-6 flex items-center justify-center bg-slate-200 hover:bg-slate-300 rounded">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 12H4"></path>
                                        </svg>
                                    </button>
                                    <span class="w-8 text-center font-semibold text-sm">{{ $item['quantity'] }}</span>
                                    <button wire:click="updateQuantity({{ $index }}, 'increase')"
                                        class="w-6 h-6 flex items-center justify-center bg-slate-200 hover:bg-slate-300 rounded">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                </div>
                                <button wire:click="removeFromCart({{ $index }})"
                                    class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                <p class="text-slate-500 text-sm">Keranjang kosong</p>
                            </div>
                        @endforelse
                    </div>

                    @if (!empty($cart))
                        <div class="p-4 border-t border-slate-200 space-y-3">
                            <!-- Discount -->
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Diskon</label>
                                <input type="number" wire:model.live="discount"
                                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900 focus:border-transparent"
                                    placeholder="0">
                            </div>

                            <!-- Tax -->
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">Pajak</label>
                                <input type="number" wire:model.live="tax"
                                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900 focus:border-transparent"
                                    placeholder="0">
                            </div>

                            <!-- Summary -->
                            <div class="space-y-2 pt-3 border-t border-slate-200">
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500">Subtotal</span>
                                    <span class="font-medium">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                </div>
                                @if ($discount > 0)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-slate-500">Diskon</span>
                                        <span class="font-medium text-red-600">- Rp
                                            {{ number_format($discount, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                @if ($tax > 0)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-slate-500">Pajak</span>
                                        <span class="font-medium">Rp {{ number_format($tax, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between text-lg font-bold pt-2 border-t border-slate-200">
                                    <span>Total</span>
                                    <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="space-y-2 pt-3">
                                <button wire:click="openPaymentModal"
                                    class="w-full bg-slate-900 text-white py-3 rounded-lg hover:bg-slate-800 transition-colors font-semibold">
                                    Bayar
                                </button>
                                <button wire:click="resetTransaction" wire:confirm="Yakin ingin reset transaksi?"
                                    class="w-full border border-slate-300 text-slate-700 py-2 rounded-lg hover:bg-slate-50 transition-colors">
                                    Reset
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>

    <!-- Payment Modal -->
    @if ($showPaymentModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-900 bg-opacity-50"
                    wire:click="closePaymentModal"></div>

                <div
                    class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="processPayment">
                        <div class="bg-white px-6 py-4 border-b border-slate-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-bold text-slate-900">Pembayaran</h3>
                                <button type="button" wire:click="closePaymentModal"
                                    class="text-slate-400 hover:text-slate-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="bg-white px-6 py-4 space-y-4">
                            <!-- Total to Pay -->
                            <div class="bg-slate-50 p-4 rounded-lg">
                                <p class="text-sm text-slate-500 mb-1">Total Pembayaran</p>
                                <p class="text-3xl font-bold text-slate-900">Rp
                                    {{ number_format($total, 0, ',', '.') }}</p>
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Metode Pembayaran</label>
                                <div class="grid grid-cols-3 gap-3">
                                    <button type="button" wire:click="$set('paymentMethod', 'cash')"
                                        class="p-3 border-2 rounded-lg text-center transition-all {{ $paymentMethod === 'cash' ? 'border-slate-900 bg-slate-50' : 'border-slate-200 hover:border-slate-300' }}">
                                        <svg class="w-6 h-6 mx-auto mb-1 {{ $paymentMethod === 'cash' ? 'text-slate-900' : 'text-slate-400' }}"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                            </path>
                                        </svg>
                                        <span class="text-xs font-medium">Cash</span>
                                    </button>
                                    <button type="button" wire:click="$set('paymentMethod', 'transfer')"
                                        class="p-3 border-2 rounded-lg text-center transition-all {{ $paymentMethod === 'transfer' ? 'border-slate-900 bg-slate-50' : 'border-slate-200 hover:border-slate-300' }}">
                                        <svg class="w-6 h-6 mx-auto mb-1 {{ $paymentMethod === 'transfer' ? 'text-slate-900' : 'text-slate-400' }}"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                            </path>
                                        </svg>
                                        <span class="text-xs font-medium">Transfer</span>
                                    </button>
                                    <button type="button" wire:click="$set('paymentMethod', 'qris')"
                                        class="p-3 border-2 rounded-lg text-center transition-all {{ $paymentMethod === 'qris' ? 'border-slate-900 bg-slate-50' : 'border-slate-200 hover:border-slate-300' }}">
                                        <svg class="w-6 h-6 mx-auto mb-1 {{ $paymentMethod === 'qris' ? 'text-slate-900' : 'text-slate-400' }}"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                                            </path>
                                        </svg>
                                        <span class="text-xs font-medium">QRIS</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Amount Paid -->
                            <div>
                                <label for="paid" class="block text-sm font-medium text-slate-700 mb-2">
                                    Jumlah Dibayar
                                </label>
                                <input type="number" id="paid" wire:model.live="paid"
                                    class="block w-full px-4 py-3 text-lg font-semibold border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900 focus:border-transparent"
                                    placeholder="0">
                            </div>

                            <!-- Quick Amount Buttons -->
                            <div class="grid grid-cols-4 gap-2">
                                @foreach ([10000, 20000, 50000, 100000] as $amount)
                                    <button type="button" wire:click="$set('paid', {{ $total + $amount }})"
                                        class="px-3 py-2 text-sm border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                                        {{ number_format($amount, 0, ',', '.') }}
                                    </button>
                                @endforeach
                            </div>

                            <!-- Change -->
                            @if ($change > 0)
                                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                    <p class="text-sm text-green-700 mb-1">Kembalian</p>
                                    <p class="text-2xl font-bold text-green-900">Rp
                                        {{ number_format($change, 0, ',', '.') }}</p>
                                </div>
                            @endif

                            @if ($paid > 0 && $paid < $total)
                                <div class="bg-red-50 p-3 rounded-lg border border-red-200">
                                    <p class="text-sm text-red-700">Jumlah pembayaran kurang dari total</p>
                                </div>
                            @endif
                        </div>

                        <div class="bg-slate-50 px-6 py-4 flex justify-end space-x-3">
                            <button type="button" wire:click="closePaymentModal"
                                class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-100 transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-6 py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition-colors font-semibold {{ $paid < $total ? 'opacity-50 cursor-not-allowed' : '' }}"
                                {{ $paid < $total ? 'disabled' : '' }}>
                                Proses Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Success Modal -->
    @if ($showSuccessModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-900 bg-opacity-50"></div>

                <div
                    class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                    <div class="bg-white px-6 py-8 text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-2">Pembayaran Berhasil!</h3>
                        <p class="text-slate-500 mb-4">Transaksi telah berhasil diproses</p>
                        <div class="bg-slate-50 p-4 rounded-lg mb-6">
                            <p class="text-sm text-slate-500 mb-1">No. Invoice</p>
                            <p class="text-xl font-bold text-slate-900">{{ $lastInvoice }}</p>
                        </div>
                        <button wire:click="closeSuccessModal"
                            class="w-full bg-slate-900 text-white py-3 rounded-lg hover:bg-slate-800 transition-colors font-semibold">
                            Selesai
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
