<x-layouts.app title="Checkout Berhasil" subtitle="Terima kasih telah berbelanja">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="bg-white rounded-xl border border-slate-200 p-8 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-green-600 text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mb-2">Checkout Berhasil</h1>
            <p class="text-slate-600 mb-6">Pesanan Anda sudah kami terima.</p>

            <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 text-left mb-6">
                <p class="text-sm text-slate-500">No. Invoice</p>
                <p class="text-lg font-semibold text-slate-900">{{ $transaction->invoice_number }}</p>
                <div class="mt-3 flex items-center justify-between text-sm">
                    <span class="text-slate-500">Total</span>
                    <span class="font-medium text-slate-900">
                        Rp {{ number_format($transaction->total, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <a href="{{ route('catalog') }}"
                class="inline-flex items-center px-4 py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition-colors">
                Kembali ke Katalog
            </a>
        </div>
    </div>
</x-layouts.app>
