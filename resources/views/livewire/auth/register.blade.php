<div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 border border-slate-200">
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-slate-900 mb-2">Daftar Toko Barokah Jaya</h1>
        <p class="text-slate-500 text-sm">Buat akun untuk mulai berbelanja</p>
    </div>

    <form wire:submit.prevent="register" class="space-y-5">
        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap</label>
            <input type="text" id="name" wire:model.defer="name" placeholder="John Doe"
                class="block w-full px-3 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900 focus:border-transparent">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email</label>
            <input type="email" id="email" wire:model.defer="email" placeholder="nama@email.com"
                class="block w-full px-3 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900 focus:border-transparent">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password</label>
            <input type="password" id="password" wire:model.defer="password" placeholder="Minimal 6 karakter"
                class="block w-full px-3 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900 focus:border-transparent">
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password Confirmation -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">Konfirmasi Password</label>
            <input type="password" id="password_confirmation" wire:model.defer="password_confirmation" placeholder="Ulangi password"
                class="block w-full px-3 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900 focus:border-transparent">
            @error('password_confirmation')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit -->
        <button type="submit" wire:loading.attr="disabled"
            class="w-full bg-slate-900 text-white py-3 px-4 rounded-lg hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2 transition-all duration-200 font-medium">
            <span wire:loading.remove>Daftar Sekarang</span>
            <span wire:loading>
                <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                </svg>
            </span>
        </button>
    </form>

    <!-- Login Link -->
    <div class="mt-6 text-center">
        <p class="text-sm text-slate-600">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-slate-900 hover:text-slate-700 font-medium">
                Login di sini
            </a>
        </p>
    </div>
</div>