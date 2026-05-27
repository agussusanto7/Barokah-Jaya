<div class="min-h-screen bg-slate-50">
    @include('livewire.includes.sidebar')

    <!-- Main Content -->
    <div class="lg:pl-64">
        @include('livewire.includes.header', ['title' => 'Produk', 'subtitle' => 'Kelola semua produk'])

        <!-- Content -->
        <main class="p-6">
            <!-- Flash Messages -->
            @if (session()->has('message'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-green-800 font-medium text-sm">{{ session('message') }}</p>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-lg border border-slate-200 card-shadow">
                <!-- Header -->
                <div class="p-6 border-b border-slate-200">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Daftar Produk</h2>
                            <p class="text-sm text-slate-500 mt-1">Kelola semua produk warung</p>
                        </div>
                        <div
                            class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-3">
                            <select wire:model.live="categoryFilter"
                                class="px-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900 focus:border-transparent bg-white">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="relative">
                                <input type="text" wire:model.live.debounce.300ms="search"
                                    placeholder="Cari produk..."
                                    class="pl-10 pr-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900 focus:border-transparent w-64">
                                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <button wire:click="openModal"
                                class="bg-slate-900 text-white px-4 py-2.5 rounded-lg hover:bg-slate-800 transition-colors flex items-center space-x-2 whitespace-nowrap font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>Tambah Produk</span>
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
                                    Produk
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                    SKU
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                    Kategori
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                    Harga
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                    Stok
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @forelse($products as $product)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-3">
                                            @if ($product->image)
                                                <img src="{{ Storage::url($product->image) }}"
                                                    alt="{{ $product->name }}"
                                                    class="w-12 h-12 rounded-lg object-cover border border-slate-200">
                                            @else
                                                <div
                                                    class="w-12 h-12 bg-slate-100 rounded-lg flex items-center justify-center border border-slate-200">
                                                    <svg class="w-6 h-6 text-slate-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-slate-900">{{ $product->name }}
                                                </div>
                                                <div class="text-sm text-slate-500">
                                                    {{ Str::limit($product->description, 30) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-slate-900 font-mono">{{ $product->sku }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 border border-slate-200">
                                            {{ $product->category->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-slate-900">Rp
                                            {{ number_format($product->price, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->stock < 10 ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-slate-100 text-slate-800 border border-slate-200' }}">
                                            {{ $product->stock }} pcs
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <button wire:click="edit({{ $product->id }})"
                                            class="inline-flex items-center px-3 py-1.5 border border-slate-300 text-xs font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 transition-colors">
                                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                            Edit
                                        </button>
                                        <button wire:click="delete({{ $product->id }})"
                                            wire:confirm="Apakah Anda yakin ingin menghapus produk ini?"
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
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                            </path>
                                        </svg>
                                        <p class="text-slate-500 font-medium">Tidak ada produk ditemukan</p>
                                        <p class="text-slate-400 text-sm mt-1">Mulai dengan menambahkan produk baru</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-slate-200">
                    {{ $products->links() }}
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
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <form wire:submit.prevent="save">
                        <!-- Header -->
                        <div class="bg-white px-6 py-4 border-b border-slate-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-slate-900">
                                    {{ $isEdit ? 'Edit Produk' : 'Tambah Produk' }}
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
                        <div class="bg-white px-6 py-4 space-y-4 max-h-[calc(100vh-250px)] overflow-y-auto">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Nama Produk -->
                                <div class="md:col-span-2">
                                    <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                                        Nama Produk <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="name" wire:model="name"
                                        class="block w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900 focus:border-transparent @error('name') border-red-500 @enderror"
                                        placeholder="Masukkan nama produk">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- SKU -->
                                <div>
                                    <label for="sku" class="block text-sm font-medium text-slate-700 mb-2">
                                        SKU <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="sku" wire:model="sku"
                                        class="block w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900 focus:border-transparent @error('sku') border-red-500 @enderror"
                                        placeholder="PRD001">
                                    @error('sku')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Kategori -->
                                <div>
                                    <label for="category_id" class="block text-sm font-medium text-slate-700 mb-2">
                                        Kategori <span class="text-red-500">*</span>
                                    </label>
                                    <select id="category_id" wire:model="category_id"
                                        class="block w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900 focus:border-transparent @error('category_id') border-red-500 @enderror bg-white">
                                        <option value="">Pilih Kategori</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Harga -->
                                <div>
                                    <label for="price" class="block text-sm font-medium text-slate-700 mb-2">
                                        Harga <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" id="price" wire:model="price"
                                        class="block w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900 focus:border-transparent @error('price') border-red-500 @enderror"
                                        placeholder="15000">
                                    @error('price')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Stok -->
                                <div>
                                    <label for="stock" class="block text-sm font-medium text-slate-700 mb-2">
                                        Stok <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" id="stock" wire:model="stock"
                                        class="block w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900 focus:border-transparent @error('stock') border-red-500 @enderror"
                                        placeholder="50">
                                    @error('stock')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Deskripsi -->
                                <div class="md:col-span-2">
                                    <label for="description" class="block text-sm font-medium text-slate-700 mb-2">
                                        Deskripsi
                                    </label>
                                    <textarea id="description" wire:model="description" rows="3"
                                        class="block w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-900 focus:border-transparent"
                                        placeholder="Masukkan deskripsi produk (opsional)"></textarea>
                                </div>

                                <!-- Image Upload -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700 mb-2">
                                        Gambar Produk
                                    </label>
                                    <div class="flex items-center space-x-4">
                                        @if ($image)
                                            <img src="{{ $image->temporaryUrl() }}"
                                                class="w-24 h-24 rounded-lg object-cover border border-slate-200">
                                        @elseif($oldImage)
                                            <img src="{{ Storage::url($oldImage) }}"
                                                class="w-24 h-24 rounded-lg object-cover border border-slate-200">
                                        @endif
                                        <div class="flex-1">
                                            <input type="file" wire:model="image" accept="image/*"
                                                class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                                            @error('image')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                            <p class="mt-1 text-xs text-slate-500">PNG, JPG maksimal 2MB</p>
                                        </div>
                                    </div>
                                    <div wire:loading wire:target="image" class="mt-2 text-sm text-slate-500">
                                        Uploading...
                                    </div>
                                </div>
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
