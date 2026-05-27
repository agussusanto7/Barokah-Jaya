<div class="min-h-screen bg-slate-50">
    <!-- Sidebar -->
    @include('livewire.includes.sidebar')

    <!-- Main Content -->
    <div class="lg:pl-64">
        <!-- Top Bar -->
        <header class="sticky top-0 z-40 flex items-center justify-between h-16 px-6 bg-white border-b border-slate-200">
            <div class="flex items-center space-x-4">
                <button id="sidebar-toggle" onclick="toggleSidebar()"
                    class="lg:hidden text-slate-600 hover:text-slate-800 p-2 rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <div>
                    <h1 class="text-lg font-semibold text-slate-900">Dashboard</h1>
                    <p class="text-sm text-slate-500">Halo, {{ auth()->user()->name }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <span class="text-sm text-slate-600 bg-slate-100 px-3 py-1.5 rounded-lg">
                    {{ now()->format('d M Y, H:i') }}
                </span>
            </div>
        </header>

        <!-- Dashboard Content -->
        <main class="p-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                <!-- Card 1 -->
                <div class="stats-card bg-white rounded-lg p-5 card-shadow">
                    <div class="flex items-center space-x-4">
                        <div
                            class="flex items-center justify-center w-12 h-12 bg-black-50 rounded-lg border border-black-100">
                            <svg class="w-6 h-6 text-black-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-slate-900 mb-1">{{ $totalProducts }}</h3>
                            <p class="text-sm text-slate-600">Total Produk</p>
                        </div>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="stats-card bg-white rounded-lg p-5 card-shadow">
                    <div class="flex items-center space-x-4">
                        <div
                            class="flex items-center justify-center w-12 h-12 bg-black-50 rounded-lg border border-black-100">
                            <svg class="w-6 h-6 text-black-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-slate-900 mb-1">{{ $totalCategories }}</h3>
                            <p class="text-sm text-slate-600">Total Kategori</p>
                        </div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="stats-card bg-white rounded-lg p-5 card-shadow">
                    <div class="flex items-center space-x-4">
                        <div
                            class="flex items-center justify-center w-12 h-12 bg-black-50 rounded-lg border border-black-100">
                            <svg class="w-6 h-6 text-black-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-slate-900 mb-1">{{ $todayTransactions }}</h3>
                            <p class="text-sm text-slate-600">Transaksi Hari Ini</p>
                        </div>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="stats-card bg-white rounded-lg p-5 card-shadow">
                    <div class="flex items-center space-x-4">
                        <div
                            class="flex items-center justify-center w-12 h-12 bg-black-50 rounded-lg border border-black-100">
                            <svg class="w-6 h-6 text-black-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-slate-900 mb-1">Rp
                                {{ number_format($todayRevenue, 0, ',', '.') }}</h3>
                            <p class="text-sm text-slate-600">Pendapatan Hari Ini</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alert Low Stock -->
            @if ($lowStockProducts > 0)
                <div class="mb-6 bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                        <div>
                            <h4 class="font-semibold text-amber-900">Stok Rendah</h4>
                            <p class="text-sm text-amber-700">{{ $lowStockProducts }} produk stok kurang dari 10</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Transactions -->
                <div class="bg-white rounded-lg border border-slate-200 card-shadow">
                    <div class="px-5 py-4 border-b border-slate-200">
                        <h2 class="font-semibold text-slate-900">Transaksi Terbaru</h2>
                    </div>
                    <div class="p-5">
                        @if ($recentTransactions->count() > 0)
                            <div class="space-y-4">
                                @foreach ($recentTransactions as $transaction)
                                    <div
                                        class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-200">
                                        <div class="flex-1">
                                            <p class="font-medium text-slate-900 text-sm">
                                                {{ $transaction->invoice_number }}</p>
                                            <p class="text-xs text-slate-500 mt-1">{{ $transaction->user->name }} •
                                                {{ $transaction->created_at->format('H:i') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-slate-900">Rp
                                                {{ number_format($transaction->total, 0, ',', '.') }}</p>
                                            <span
                                                class="inline-block text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded mt-1">
                                                {{ $transaction->payment_method }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                                <p class="text-slate-500 text-sm">Belum ada transaksi</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Top Products -->
                <div class="bg-white rounded-lg border border-slate-200 card-shadow">
                    <div class="px-5 py-4 border-b border-slate-200">
                        <h2 class="font-semibold text-slate-900">Produk Terlaris</h2>
                    </div>
                    <div class="p-5">
                        @if ($topProducts->count() > 0)
                            <div class="space-y-3">
                                @foreach ($topProducts as $index => $product)
                                    <div class="flex items-center space-x-3 p-3 rounded-lg border border-slate-200">
                                        <div
                                            class="flex items-center justify-center w-8 h-8 bg-slate-100 rounded text-sm font-medium text-slate-700">
                                            {{ $index + 1 }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-slate-900 text-sm truncate">
                                                {{ $product->name }}</p>
                                            <p class="text-xs text-slate-500 truncate">{{ $product->category->name }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-slate-900">{{ $product->total_sold }}</p>
                                            <p class="text-xs text-slate-500">terjual</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <p class="text-slate-500 text-sm">Belum ada data penjualan</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
