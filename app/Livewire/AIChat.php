<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\GeminiAIService;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AIChat extends Component
{
    public $isOpen = false;
    public $message = '';
    public $messages = [];
    public $isLoading = false;
    public $isTyping = false;

    protected $listeners = [
        'toggleChat',
        'add-bot-message-from-js' => 'addBotMessageFromJs'

    ];

    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;
        if ($this->isOpen && empty($this->messages)) {
            $this->addBotMessage('Halo! Saya asisten AI Toko Barokah Jaya. Ada yang bisa saya bantu mengenai penjualan atau produk?');
        }
    }

    public function sendMessage()
    {
        if (trim($this->message) === '') {
            return;
        }

        // Simpan pesan user dan langsung tampilkan
        $userMessage = $this->message;
        $this->addUserMessage($userMessage);
        $this->message = '';
        $this->isLoading = true;
        $this->isTyping = true;

        Log::info('📤 [AIChat] Sending message', ['message' => $userMessage]);

        // Dispatch event untuk scroll ke bawah setelah pesan user ditambahkan
        $this->dispatch('scroll-to-bottom');

        // Gunakan queue atau async untuk memproses AI response
        $this->processAIResponse($userMessage);
    }

    private function processAIResponse($userMessage)
    {
        try {
            $geminiService = app(GeminiAIService::class);
            $context = $this->prepareContext($userMessage);

            Log::info('🤖 [AIChat] Processing AI response');

            // Dapatkan response dari AI
            $response = $geminiService->generateResponse($userMessage, $context);

            Log::info('📥 [AIChat] AI Response received', [
                'response_length' => strlen($response)
            ]);

            // Tambahkan pesan bot dengan animasi mengetik
            $this->dispatch('start-typing-animation', message: $response);

        } catch (\Exception $e) {
            Log::error('💥 [AIChat] Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->addBotMessage('Maaf, terjadi kesalahan: ' . $e->getMessage());
            $this->isLoading = false;
            $this->isTyping = false;
        }
    }

    // Method untuk menerima pesan dari JavaScript (typing animation selesai)
    public function addBotMessageFromJs($message)
    {
        // Handle both string and object input
        if (is_array($message) && isset($message['message'])) {
            $message = $message['message'];
        }

        $this->messages[] = [
            'type' => 'bot',
            'content' => $message,
            'time' => now()->format('H:i'),
        ];

        $this->isTyping = false;
        $this->isLoading = false;
        $this->dispatch('scroll-to-bottom');
    }

    public function addBotMessage($message)
    {
        $this->messages[] = [
            'type' => 'bot',
            'content' => $message,
            'time' => now()->format('H:i'),
        ];

        $this->dispatch('scroll-to-bottom');
    }

    private function addUserMessage($message)
    {
        $this->messages[] = [
            'type' => 'user',
            'content' => $message,
            'time' => now()->format('H:i'),
        ];
    }

    // ... (method-method helper lainnya tetap sama: prepareContext, isSalesRelated, dll.)
    private function prepareContext($message)
    {
        $context = '';
        $message = strtolower($message);

        try {
            if ($this->isSalesRelated($message)) {
                $context = $this->getSalesContext();
            } elseif ($this->isProductRelated($message)) {
                $context = $this->getProductContext();
            } elseif ($this->isTransactionRelated($message)) {
                $context = $this->getTransactionContext();
            }
        } catch (\Exception $e) {
            Log::error('Error preparing context', ['error' => $e->getMessage()]);
        }

        return $context;
    }

    private function isSalesRelated($message)
    {
        $keywords = ['penjualan', 'jual', 'laporan', 'pendapatan', 'revenue', 'omzet', 'terjual', 'transaksi', 'hasil', 'kinerja', 'produk terjual', 'produk yang terjual', 'barang terjual', 'apa saja yang terjual', 'daftar terjual'];
        return $this->containsKeywords($message, $keywords);
    }

    private function isProductRelated($message)
    {
        $keywords = ['produk', 'barang', 'stok', 'inventory', 'kategori', 'item', 'terlaris', 'apa produk', 'produk terjual', 'produk yang terjual', 'barang terjual'];
        return $this->containsKeywords($message, $keywords);
    }

    private function isTransactionRelated($message)
    {
        $keywords = [
            'transaksi',
            'pembelian',
            'invoice',
            'struk',
            'pembayaran',
            'beli',
            'detail transaksi',
            'transaksi hari',
            'transaksi terbaru',
            'invoice nomor',
            'transaksi terakhir',
            'pembelian hari ini',
            'transaksi bulan',
            'metode pembayaran',
            'cash',
            'transfer',
            'qris',
            'kembalian'
        ];
        return $this->containsKeywords($message, $keywords);
    }

    private function containsKeywords($message, $keywords)
    {
        foreach ($keywords as $keyword) {
            if (str_contains($message, strtolower($keyword))) {
                return true;
            }
        }
        return false;
    }

    private function getSalesContext()
    {
        try {
            // Statistik umum
            $todayTransactions = Transaction::whereDate('created_at', today())->count();
            $todayRevenue = Transaction::whereDate('created_at', today())->sum('total');
            $yesterdayRevenue = Transaction::whereDate('created_at', today()->subDay())->sum('total');
            $weekRevenue = Transaction::where('created_at', '>=', now()->startOfWeek())->sum('total');
            $monthRevenue = Transaction::whereMonth('created_at', now()->month)->sum('total');

            $lowStockProducts = Product::where('stock', '<', 10)->get();
            $outOfStockProducts = Product::where('stock', '=', 0)->get();
            $totalProducts = Product::count();
            $totalCategories = Category::count();

            // Produk terlaris dengan detail
            $topProducts = Product::select(
                'products.*',
                DB::raw('COALESCE(SUM(transaction_details.quantity), 0) as total_sold'),
                DB::raw('COALESCE(SUM(transaction_details.subtotal), 0) as total_revenue')
            )
                ->leftJoin('transaction_details', 'products.id', '=', 'transaction_details.product_id')
                ->groupBy('products.id')
                ->orderBy('total_sold', 'DESC')
                ->take(10)
                ->get();

            // Metode pembayaran populer
            $paymentMethods = Transaction::select(
                'payment_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as total')
            )
                ->whereDate('created_at', today())
                ->groupBy('payment_method')
                ->get();

            $context = "=== LAPORAN PENJUALAN LENGKAP ===\n\n";

            $context .= "PERFORMA PENJUALAN:\n";
            $context .= "- Hari ini: {$todayTransactions} transaksi, Rp " . number_format($todayRevenue, 0, ',', '.') . "\n";
            $context .= "- Kemarin: Rp " . number_format($yesterdayRevenue, 0, ',', '.') . "\n";
            $context .= "- Minggu ini: Rp " . number_format($weekRevenue, 0, ',', '.') . "\n";
            $context .= "- Bulan ini: Rp " . number_format($monthRevenue, 0, ',', '.') . "\n";

            if ($yesterdayRevenue > 0) {
                $growth = (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100;
                $context .= "- Pertumbuhan vs kemarin: " . number_format($growth, 2) . "%\n";
            }
            $context .= "\n";

            $context .= "STATUS INVENTORI:\n";
            $context .= "- Total produk: {$totalProducts}\n";
            $context .= "- Total kategori: {$totalCategories}\n";
            $context .= "- Produk stok rendah (<10): {$lowStockProducts->count()}\n";
            $context .= "- Produk habis: {$outOfStockProducts->count()}\n\n";

            if ($lowStockProducts->count() > 0) {
                $context .= "PRODUK STOK RENDAH:\n";
                foreach ($lowStockProducts as $product) {
                    $context .= "- {$product->name}: {$product->stock} unit tersisa\n";
                }
                $context .= "\n";
            }

            if ($topProducts->count() > 0) {
                $context .= "TOP 10 PRODUK TERLARIS:\n";
                foreach ($topProducts as $index => $product) {
                    $context .= ($index + 1) . ". {$product->name}:\n";
                    $context .= "   - Terjual: {$product->total_sold} unit\n";
                    $context .= "   - Revenue: Rp " . number_format($product->total_revenue, 0, ',', '.') . "\n";
                    $context .= "   - Stok tersisa: {$product->stock} unit\n";
                }
                $context .= "\n";
            }

            if ($paymentMethods->count() > 0) {
                $context .= "METODE PEMBAYARAN HARI INI:\n";
                foreach ($paymentMethods as $method) {
                    $context .= "- {$method->payment_method}: {$method->count} transaksi, ";
                    $context .= "Rp " . number_format($method->total, 0, ',', '.') . "\n";
                }
            }

            return $context;
        } catch (\Exception $e) {
            Log::error('Error getting sales context', ['error' => $e->getMessage()]);
            return "Data penjualan tidak dapat diakses saat ini.";
        }
    }

    // ... (method getProductContext dan getTransactionContext tetap sama)
    private function getProductContext()
    {
        try {
            $products = Product::with('category')->get();
            $lowStockProducts = Product::where('stock', '<', 10)->get();
            $outOfStockProducts = Product::where('stock', '=', 0)->get();
            $categories = Category::withCount('products')->get();

            // Produk dengan nilai stok tertinggi
            $highValueProducts = Product::select('*', DB::raw('price * stock as stock_value'))
                ->orderBy('stock_value', 'DESC')
                ->take(5)
                ->get();

            $context = "=== DATA PRODUK LENGKAP ===\n\n";

            $context .= "RINGKASAN INVENTORI:\n";
            $context .= "- Total produk: " . $products->count() . "\n";
            $context .= "- Total kategori: " . $categories->count() . "\n";
            $context .= "- Produk stok rendah: " . $lowStockProducts->count() . "\n";
            $context .= "- Produk habis: " . $outOfStockProducts->count() . "\n";
            $context .= "- Nilai total inventori: Rp " . number_format($products->sum(function ($p) {
                return $p->price * $p->stock;
            }), 0, ',', '.') . "\n\n";

            $context .= "KATEGORI PRODUK:\n";
            foreach ($categories as $category) {
                $categoryProducts = $products->where('category_id', $category->id);
                $categoryValue = $categoryProducts->sum(function ($p) {
                    return $p->price * $p->stock;
                });
                $context .= "- {$category->name}: {$category->products_count} produk, ";
                $context .= "Nilai: Rp " . number_format($categoryValue, 0, ',', '.') . "\n";
            }
            $context .= "\n";

            if ($highValueProducts->count() > 0) {
                $context .= "PRODUK NILAI STOK TERTINGGI:\n";
                foreach ($highValueProducts as $index => $product) {
                    $stockValue = $product->price * $product->stock;
                    $context .= ($index + 1) . ". {$product->name}:\n";
                    $context .= "   - Harga: Rp " . number_format($product->price, 0, ',', '.') . "\n";
                    $context .= "   - Stok: {$product->stock} unit\n";
                    $context .= "   - Nilai total: Rp " . number_format($stockValue, 0, ',', '.') . "\n";
                }
                $context .= "\n";
            }

            if ($lowStockProducts->count() > 0) {
                $context .= "⚠️ PERINGATAN STOK RENDAH:\n";
                foreach ($lowStockProducts as $product) {
                    $context .= "- {$product->name} ({$product->category->name}): {$product->stock} unit\n";
                }
                $context .= "\n";
            }

            if ($outOfStockProducts->count() > 0) {
                $context .= "🚫 PRODUK HABIS:\n";
                foreach ($outOfStockProducts as $product) {
                    $context .= "- {$product->name} ({$product->category->name})\n";
                }
            }

            return $context;
        } catch (\Exception $e) {
            Log::error('Error getting product context', ['error' => $e->getMessage()]);
            return "Data produk tidak dapat diakses saat ini.";
        }
    }

    private function getTransactionContext()
    {
        try {
            // Data statistik umum
            $todayTransactions = Transaction::whereDate('created_at', today())->count();
            $todayRevenue = Transaction::whereDate('created_at', today())->sum('total');
            $monthTransactions = Transaction::whereMonth('created_at', now()->month)->count();
            $monthRevenue = Transaction::whereMonth('created_at', now()->month)->sum('total');
            $totalRevenue = Transaction::sum('total');

            // Transaksi terbaru dengan detail lengkap
            $recentTransactions = Transaction::with(['user', 'details.product'])
                ->latest()
                ->take(10)
                ->get();

            // Transaksi hari ini dengan detail
            $todayTransactionsDetailed = Transaction::with(['user', 'details.product'])
                ->whereDate('created_at', today())
                ->get();

            $context = "=== DATA TRANSAKSI LENGKAP ===\n\n";

            $context .= "RINGKASAN STATISTIK:\n";
            $context .= "- Transaksi hari ini: {$todayTransactions} transaksi\n";
            $context .= "- Pendapatan hari ini: Rp " . number_format($todayRevenue, 0, ',', '.') . "\n";
            $context .= "- Transaksi bulan ini: {$monthTransactions} transaksi\n";
            $context .= "- Pendapatan bulan ini: Rp " . number_format($monthRevenue, 0, ',', '.') . "\n";
            $context .= "- Total pendapatan keseluruhan: Rp " . number_format($totalRevenue, 0, ',', '.') . "\n\n";

            // Detail transaksi hari ini
            if ($todayTransactionsDetailed->count() > 0) {
                $context .= "DETAIL TRANSAKSI HARI INI:\n";
                foreach ($todayTransactionsDetailed as $index => $transaction) {
                    $context .= "\nTransaksi #" . ($index + 1) . ":\n";
                    $context .= "  - Invoice: {$transaction->invoice_number}\n";
                    $context .= "  - Waktu: {$transaction->created_at->format('H:i:s')}\n";
                    $context .= "  - Kasir: {$transaction->user->name}\n";
                    $context .= "  - Metode Bayar: {$transaction->payment_method}\n";
                    $context .= "  - Subtotal: Rp " . number_format($transaction->subtotal, 0, ',', '.') . "\n";

                    if ($transaction->discount > 0) {
                        $context .= "  - Diskon: Rp " . number_format($transaction->discount, 0, ',', '.') . "\n";
                    }
                    if ($transaction->tax > 0) {
                        $context .= "  - Pajak: Rp " . number_format($transaction->tax, 0, ',', '.') . "\n";
                    }

                    $context .= "  - Total: Rp " . number_format($transaction->total, 0, ',', '.') . "\n";
                    $context .= "  - Dibayar: Rp " . number_format($transaction->paid, 0, ',', '.') . "\n";
                    $context .= "  - Kembalian: Rp " . number_format($transaction->change, 0, ',', '.') . "\n";

                    // Detail produk yang dibeli
                    $context .= "  - Produk yang dibeli:\n";
                    foreach ($transaction->details as $detail) {
                        $context .= "    * {$detail->product->name}: {$detail->quantity} x Rp " .
                            number_format($detail->price, 0, ',', '.') .
                            " = Rp " . number_format($detail->subtotal, 0, ',', '.') . "\n";
                    }
                }
                $context .= "\n";
            }

            // 10 Transaksi terbaru untuk referensi
            $context .= "10 TRANSAKSI TERBARU:\n";
            foreach ($recentTransactions as $index => $transaction) {
                $itemCount = $transaction->details->sum('quantity');
                $context .= ($index + 1) . ". {$transaction->invoice_number} - ";
                $context .= "Rp " . number_format($transaction->total, 0, ',', '.') . " ";
                $context .= "({$itemCount} item, {$transaction->payment_method}) - ";
                $context .= "{$transaction->created_at->format('d/m/Y H:i')}\n";
            }

            return $context;
        } catch (\Exception $e) {
            Log::error('Error getting transaction context', ['error' => $e->getMessage()]);
            return "Data transaksi tidak dapat diakses saat ini.";
        }
    }

    public function render()
    {
        return view('livewire.ai-chat');
    }
}
