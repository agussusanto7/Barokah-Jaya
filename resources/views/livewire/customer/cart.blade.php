<div class="min-h-screen bg-slate-50">
    <div class="bg-white border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('catalog') }}" class="flex items-center space-x-2 text-slate-900">
                    <i class="fas fa-store text-xl"></i>
                    <span class="font-bold text-lg">Toko Barokah Jaya</span>
                </a>
                <div class="flex items-center space-x-6">
                    <div class="flex items-center space-x-3 text-sm text-slate-600">
                        <a href="{{ route('catalog') }}" class="hover:text-slate-900">Katalog</a>
                        <span>/</span>
                        <span class="text-slate-900 font-medium">Keranjang</span>
                    </div>
                    @auth
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-slate-600">Halo, {{ auth()->user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-sm text-slate-600 hover:text-slate-900 font-medium">
                                    Logout
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="flex items-center space-x-3 text-sm">
                            <a href="{{ route('login') }}" class="text-slate-600 hover:text-slate-900 font-medium">Login</a>
                            <a href="{{ route('register') }}" class="bg-slate-900 text-white px-3 py-1.5 rounded-lg hover:bg-slate-800 transition-colors">
                                Daftar
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

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

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-slate-900">Keranjang Belanja</h1>
            @if (!empty($cart))
                <button wire:click="clearCart"
                    class="text-sm text-red-600 hover:text-red-800 font-medium">
                    Kosongkan Keranjang
                </button>
            @endif
        </div>

        @if (empty($cart))
            <div class="bg-white rounded-xl border border-slate-200 p-10 text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-slate-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-slate-400 text-2xl"></i>
                </div>
                <p class="text-slate-600 mb-4">Keranjang Anda masih kosong.</p>
                <a href="{{ route('catalog') }}"
                    class="inline-flex items-center px-4 py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition-colors">
                    Kembali ke Katalog
                </a>
            </div>
        @else
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="divide-y divide-slate-200">
                    @foreach ($cart as $index => $item)
                        <div class="p-4 sm:p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex items-center gap-4">
                                @if (!empty($item['image']))
                                    <img src="{{ asset('storage/' . $item['image']) }}"
                                        alt="{{ $item['name'] }}"
                                        class="w-16 h-16 rounded-lg object-cover border border-slate-200">
                                @else
                                    <div class="w-16 h-16 rounded-lg bg-slate-100 flex items-center justify-center border border-slate-200">
                                        <i class="fas fa-image text-slate-400"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $item['name'] }}</p>
                                    <p class="text-sm text-slate-500">
                                        Rp {{ number_format($item['price'], 0, ',', '.') }}
                                    </p>
                                    @if (isset($item['stock']))
                                        <p class="text-xs text-slate-400">Stok: {{ $item['stock'] }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="flex items-center border border-slate-300 rounded-lg overflow-hidden">
                                    <button
                                        wire:click="updateQuantity({{ $index }}, {{ $item['qty'] - 1 }})"
                                        class="w-9 h-9 flex items-center justify-center bg-slate-100 hover:bg-slate-200 disabled:opacity-50"
                                        {{ $item['qty'] <= 1 ? 'disabled' : '' }}
                                    >
                                        <i class="fas fa-minus text-xs"></i>
                                    </button>
                                    <span class="w-10 text-center font-medium text-slate-900">{{ $item['qty'] }}</span>
                                    <button
                                        wire:click="updateQuantity({{ $index }}, {{ $item['qty'] + 1 }})"
                                        class="w-9 h-9 flex items-center justify-center bg-slate-100 hover:bg-slate-200 disabled:opacity-50"
                                        {{ isset($item['stock']) && $item['qty'] >= $item['stock'] ? 'disabled' : '' }}
                                    >
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </div>

                                <button wire:click="removeFromCart({{ $index }})"
                                    class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 bg-white rounded-xl border border-slate-200 p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-sm text-slate-500">Total</p>
                    <p class="text-2xl font-bold text-slate-900">
                        Rp {{ number_format($total, 0, ',', '.') }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('catalog') }}"
                        class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors">
                        Lanjut Belanja
                    </a>
                    <a href="{{ route('customer.checkout') }}"
                        class="px-4 py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition-colors font-medium">
                        {{ auth()->check() ? 'Checkout' : 'Login untuk Checkout' }}
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
