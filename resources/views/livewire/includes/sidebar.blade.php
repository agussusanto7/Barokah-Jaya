<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0">
    <!-- Logo -->
    <div class="flex items-center justify-between h-16 px-6 border-b border-slate-200">
        <div class="flex items-center space-x-3">
            <div class="flex items-center justify-center w-10 h-10 bg-slate-900 rounded-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
            </div>
            <span class="text-lg font-bold text-slate-900">Toko Barokah Jaya</span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="p-4 space-y-1">
        <a href="{{ route('dashboard') }}" wire:navigate
            class="sidebar-link flex items-center space-x-3 px-4 py-3 {{ request()->routeIs('dashboard') ? 'text-slate-900 bg-slate-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                </path>
            </svg>
            <span class="font-medium">Dashboard</span>
        </a>

        <a href="{{ route('pos.index') }}" wire:navigate
            class="sidebar-link flex items-center space-x-3 px-4 py-3 {{ request()->routeIs('pos.*') ? 'text-slate-900 bg-slate-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                </path>
            </svg>
            <span class="font-medium">Kasir (POS)</span>
        </a>

        <a href="{{ route('products.index') }}" wire:navigate
            class="sidebar-link flex items-center space-x-3 px-4 py-3 {{ request()->routeIs('products.*') ? 'text-slate-900 bg-slate-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            <span class="font-medium">Produk</span>
        </a>

        <a href="{{ route('categories.index') }}" wire:navigate
            class="sidebar-link flex items-center space-x-3 px-4 py-3 {{ request()->routeIs('categories.*') ? 'text-slate-900 bg-slate-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                </path>
            </svg>
            <span class="font-medium">Kategori</span>
        </a>

        <a href="{{ route('transactions.index') }}" wire:navigate
            class="sidebar-link flex items-center space-x-3 px-4 py-3 {{ request()->routeIs('transactions.*') ? 'text-slate-900 bg-slate-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                </path>
            </svg>
            <span class="font-medium">Transaksi</span>
        </a>
    </nav>

    <!-- User Info & Logout -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-slate-200">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="flex items-center justify-center w-10 h-10 bg-slate-100 rounded-full">
                    <span class="text-sm font-semibold text-slate-700">{{ substr(auth()->user()->name, 0, 1) }}</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-900">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-500">{{ ucfirst(auth()->user()->role) }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="p-2 text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>
