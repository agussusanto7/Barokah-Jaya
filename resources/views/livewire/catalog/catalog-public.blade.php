<div class="min-h-screen bg-slate-50">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-slate-900 to-slate-700 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold mb-4">Selamat Datang di Toko Barokah Jaya</h1>
                <p class="text-xl text-slate-200 mb-8">Temukan produk berkualitas dengan harga terbaik</p>

                <!-- Search Bar -->
                <div class="max-w-2xl mx-auto">
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live="search"
                            placeholder="Cari produk yang Anda inginkan..."
                            class="w-full px-6 py-4 rounded-full text-slate-900 text-lg focus:outline-none focus:ring-4 focus:ring-slate-300"
                        >
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                            <i class="fas fa-search text-slate-400"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation & Auth -->
    <div class="bg-white border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-8">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-store text-slate-900 text-xl"></i>
                        <span class="font-bold text-slate-900 text-lg">Toko Barokah Jaya</span>
                    </div>

                    <!-- Filter Categories -->
                    <div class="hidden md:flex items-center space-x-4">
                        <select wire:model.live="category_id" class="px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>

                        <select wire:model.live="sortBy" class="px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900">
                            <option value="name">Urutkan Nama</option>
                            <option value="price">Urutkan Harga</option>
                            <option value="created_at">Terbaru</option>
                        </select>

                        <button wire:click="sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'"
                                class="p-2 text-slate-600 hover:text-slate-900">
                            <i class="fas fa-sort-amount-{{ $sortDirection === 'asc' ? 'down' : 'up' }}"></i>
                        </button>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    @auth
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-slate-600">Halo, {{ auth()->user()->name }}</span>
                            <a href="{{ route('dashboard') }}" class="text-slate-600 hover:text-slate-900">
                                <i class="fas fa-user"></i>
                            </a>
                        </div>
                    @else
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('login') }}" class="text-slate-600 hover:text-slate-900 font-medium">Login</a>
                            <a href="{{ route('register') }}" class="bg-slate-900 text-white px-4 py-2 rounded-lg hover:bg-slate-800 transition-colors">Daftar</a>
                        </div>
                    @endauth

                    <!-- Cart Icon -->
                    <div class="relative">
                        <button class="text-slate-600 hover:text-slate-900 relative">
                            <i class="fas fa-shopping-cart text-xl"></i>
                            @if (count(session()->get('cart', [])) > 0)
                                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    {{ count(session()->get('cart', [])) }}
                                </span>
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if ($products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($products as $product)
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="aspect-w-1 aspect-h-1 w-full h-48 bg-slate-100">
                            @if ($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}"
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-image text-slate-400 text-3xl"></i>
                                </div>
                            @endif
                        </div>

                        <div class="p-4">
                            <div class="mb-2">
                                <span class="inline-block px-2 py-1 text-xs font-medium bg-slate-100 text-slate-700 rounded-full">
                                    {{ $product->category->name }}
                                </span>
                            </div>

                            <h3 class="font-semibold text-slate-900 mb-2">{{ $product->name }}</h3>
                            <p class="text-slate-600 text-sm mb-3 line-clamp-2">{{ $product->description }}</p>

                            <div class="flex items-center justify-between mb-3">
                                <span class="text-2xl font-bold text-slate-900">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                <span class="text-sm text-slate-500">Stok: {{ $product->stock }}</span>
                            </div>

                            <button wire:click="addToCart({{ $product->id }})"
                                    wire:loading.attr="disabled"
                                    class="w-full bg-slate-900 text-white py-2 px-4 rounded-lg hover:bg-slate-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <span wire:loading.remove>
                                    <i class="fas fa-cart-plus mr-2"></i> Tambah ke Keranjang
                                </span>
                                <span wire:loading>
                                    <i class="fas fa-spinner fa-spin mr-2"></i> Menambahkan...
                                </span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-search text-slate-400 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-slate-900 mb-2">Produk tidak ditemukan</h3>
                <p class="text-slate-600">Coba ubah kata kunci pencarian atau filter yang digunakan.</p>
            </div>
        @endif
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('success') }}
        </div>
    @endif
</div>

<!-- Cart Alert -->
<script>
    window.addEventListener('livewire:init', () => {
        Livewire.on('cart-added', (message) => {
            // Show success notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-20 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-pulse';
            notification.innerHTML = `
                <div class="flex items-center space-x-2">
                    <i class="fas fa-check-circle"></i>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        });

        Livewire.on('cart-error', (message) => {
            // Show error notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-20 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-pulse';
            notification.innerHTML = `
                <div class="flex items-center space-x-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        });
    });
</script>