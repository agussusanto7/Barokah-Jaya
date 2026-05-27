<x-layouts.app title="Checkout" subtitle="Selesaikan pembelian Anda">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-red-800 font-medium text-sm">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @php
            $cart = session('cart', []);
            $total = 0;
            foreach ($cart as $item) {
                $total += $item['price'] * $item['qty'];
            }
        @endphp

        @if (empty($cart))
            <div class="bg-white rounded-xl border border-slate-200 p-10 text-center">
                <p class="text-slate-600 mb-4">Keranjang Anda masih kosong.</p>
                <a href="{{ route('catalog') }}"
                    class="inline-flex items-center px-4 py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition-colors">
                    Kembali ke Katalog
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl border border-slate-200 p-6">
                        <h2 class="text-lg font-semibold text-slate-900 mb-4">Data Pengiriman</h2>
                        <form action="{{ route('customer.checkout.process') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="phone" class="block text-sm font-medium text-slate-700 mb-2">No. HP</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                                    class="block w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900 focus:border-transparent @error('phone') border-red-500 @enderror"
                                    placeholder="081234567890">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="address" class="block text-sm font-medium text-slate-700 mb-2">Alamat</label>
                                <textarea id="address" name="address" rows="4"
                                    class="block w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900 focus:border-transparent @error('address') border-red-500 @enderror"
                                    placeholder="Alamat lengkap pengiriman">{{ old('address') }}</textarea>
                                @error('address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Metode Pembayaran</label>
                                <div class="space-y-2">
                                    @foreach (['transfer' => 'Transfer Bank', 'cash' => 'Cash', 'qris' => 'QRIS'] as $value => $label)
                                        <label class="flex items-center space-x-2 text-sm text-slate-700">
                                            <input type="radio" name="payment_method" value="{{ $value }}"
                                                {{ old('payment_method', 'transfer') === $value ? 'checked' : '' }}>
                                            <span>{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('payment_method')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="pt-2">
                                <button type="submit"
                                    class="w-full bg-slate-900 text-white py-3 rounded-lg hover:bg-slate-800 transition-colors font-semibold">
                                    Proses Checkout
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 p-6 h-fit">
                    <h2 class="text-lg font-semibold text-slate-900 mb-4">Ringkasan Pesanan</h2>
                    <div class="space-y-3">
                        @foreach ($cart as $item)
                            <div class="flex items-center justify-between text-sm">
                                <div class="text-slate-700">
                                    {{ $item['name'] }} <span class="text-slate-400">x{{ $item['qty'] }}</span>
                                </div>
                                <div class="font-medium text-slate-900">
                                    Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-200 flex items-center justify-between">
                        <span class="text-sm text-slate-500">Total</span>
                        <span class="text-lg font-bold text-slate-900">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
