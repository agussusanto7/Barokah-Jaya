<div class="min-h-screen bg-slate-50">
    @include('livewire.includes.sidebar')

    <!-- Main Content -->
    <div class="lg:pl-64">
        @include('livewire.includes.header', [
            'title' => 'Kategori',
            'subtitle' => 'Kelola kategori produk',
        ])

        <!-- Content -->
        <main class="p-6">
            <!-- Flash Messages -->
            @if (session()->has('message'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-green-800 font-medium">{{ session('message') }}</p>
                    </div>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-red-800 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-lg border border-slate-200 card-shadow">
                <!-- Header -->
                <div class="p-6 border-b border-slate-200">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Daftar Kategori</h2>
                            <p class="text-sm text-slate-500 mt-1">Kelola semua kategori produk</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="relative">
                                <input type="text" wire:model.live.debounce.300ms="search"
                                    placeholder="Cari kategori..."
                                    class="pl-10 pr-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900 focus:border-transparent w-64">
                                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <button wire:click="openModal"
                                class="bg-slate-900 text-white px-4 py-2.5 rounded-lg hover:bg-slate-800 transition-colors flex items-center space-x-2 font-medium">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>Tambah Kategori</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                    No
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                    Nama Kategori
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                    Deskripsi
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                    Jumlah Produk
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @forelse($categories as $index => $category)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                        {{ $categories->firstItem() + $index }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-slate-900">{{ $category->name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-slate-500">{{ $category->description ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 border border-slate-200">
                                            {{ $category->products_count }} Produk
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <button wire:click="edit({{ $category->id }})"
                                            class="inline-flex items-center px-3 py-1.5 border border-slate-300 text-xs font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 transition-colors">
                                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                            Edit
                                        </button>
                                        <button wire:click="delete({{ $category->id }})"
                                            wire:confirm="Apakah Anda yakin ingin menghapus kategori ini?"
                                            class="inline-flex items-center px-3 py-1.5 border border-red-300 text-xs font-medium rounded-lg text-red-700 bg-white hover:bg-red-50 transition-colors">
                                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                            </path>
                                        </svg>
                                        <p class="text-slate-500 font-medium">Tidak ada kategori ditemukan</p>
                                        <p class="text-slate-400 text-sm mt-1">Mulai dengan menambahkan kategori baru
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-slate-200">
                    {{ $categories->links() }}
                </div>
            </div>
        </main>
    </div>

    <!-- Modal -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 transition-opacity bg-slate-900 bg-opacity-50" wire:click="closeModal">
                </div>

                <!-- Modal panel -->
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="save">
                        <!-- Header -->
                        <div class="bg-white px-6 py-4 border-b border-slate-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-slate-900">
                                    {{ $isEdit ? 'Edit Kategori' : 'Tambah Kategori' }}
                                </h3>
                                <button type="button" wire:click="closeModal"
                                    class="text-slate-400 hover:text-slate-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Body -->
                        <div class="bg-white px-6 py-4 space-y-4">
                            <!-- Nama -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                                    Nama Kategori <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="name" wire:model="name"
                                    class="block w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900 focus:border-transparent @error('name') border-red-500 @enderror"
                                    placeholder="Masukkan nama kategori">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-slate-700 mb-2">
                                    Deskripsi
                                </label>
                                <textarea id="description" wire:model="description" rows="3"
                                    class="block w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900 focus:border-transparent"
                                    placeholder="Masukkan deskripsi kategori (opsional)"></textarea>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="bg-slate-50 px-6 py-4 flex justify-end space-x-3 border-t border-slate-200">
                            <button type="button" wire:click="closeModal"
                                class="px-4 py-2.5 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-100 transition-colors font-medium">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2.5 bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition-colors font-medium"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove>{{ $isEdit ? 'Update' : 'Simpan' }}</span>
                                <span wire:loading>
                                    <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
