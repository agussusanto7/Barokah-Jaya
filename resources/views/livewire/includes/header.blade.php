<header class="sticky top-0 z-40 flex items-center justify-between h-16 px-6 bg-white border-b border-slate-200">
    <div class="flex items-center space-x-4">
        <button id="sidebar-toggle" onclick="toggleSidebar()" class="lg:hidden text-slate-500 hover:text-slate-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        <div>
            <h1 class="text-xl font-bold text-slate-900">{{ $title }}</h1>
            <p class="text-sm text-slate-500">{{ $subtitle }}</p>
        </div>
    </div>
    <div class="flex items-center space-x-4">
        <span class="text-sm text-slate-500">{{ now()->format('d M Y, H:i') }}</span>
    </div>
</header>
