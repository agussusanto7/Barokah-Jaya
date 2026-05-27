<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeminiAIController;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Catalog\CatalogPublic;
use App\Livewire\Customer\Cart;
use App\Http\Controllers\CheckoutController;
use App\Livewire\Dashboard;
use App\Livewire\Products\ProductIndex;
use App\Livewire\Categories\CategoryIndex;
use App\Livewire\Transactions\TransactionIndex;
use App\Livewire\Pos\PosIndex;

// Public routes - untuk customer
Route::get('/', CatalogPublic::class)->name('catalog');
Route::get('/cart', Cart::class)->name('cart.simple');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('customer.checkout');
Route::post('/checkout', [CheckoutController::class, 'process'])->name('customer.checkout.process');
Route::get('/checkout/success/{transactionId}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/register', Register::class)->name('register');

// Guest routes - untuk login admin/karyawan
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// Test route untuk debug - hapus setelah berhasil
Route::get('/test-gemini-debug', function () {
    $apiKey = env('GEMINI_API_KEY');

    echo "<h2>Debug Gemini API</h2>";
    echo "API Key: " . (empty($apiKey) ? 'MISSING' : 'EXISTS') . "<br>";
    echo "Key (first 10): " . substr($apiKey, 0, 10) . "...<br><br>";

    try {
        $response = Http::timeout(30)
            ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => 'Halo, balas dengan "TEST BERHASIL" dalam bahasa Indonesia']
                        ]
                    ]
                ]
            ]);

        echo "Status: " . $response->status() . "<br>";

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                echo "<strong>SUCCESS:</strong> " . $data['candidates'][0]['content']['parts'][0]['text'] . "<br>";
            } else {
                echo "<strong>ERROR - No text in response:</strong><pre>";
                print_r($data);
                echo "</pre>";
            }
        } else {
            echo "<strong>ERROR:</strong> " . $response->body() . "<br>";
        }

    } catch (\Exception $e) {
        echo "<strong>EXCEPTION:</strong> " . $e->getMessage() . "<br>";
    }

    echo "<br><h3>Test dengan Service Class</h3>";
    try {
        $service = app(App\Services\GeminiAIService::class);
        $result = $service->generateResponse('Test dari service class');
        echo "<strong>Service Result:</strong> " . $result . "<br>";
    } catch (\Exception $e) {
        echo "<strong>Service Exception:</strong> " . $e->getMessage() . "<br>";
    }
});

// Test route - hapus setelah berhasil
Route::get('/test-gemini', function () {
    $service = app(App\Services\GeminiAIService::class);
    $response = $service->generateResponse('Halo, apa kabar?, apakah ada penjualan hari ini');
    dd($response);
});

// Auth routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});

// Admin & kasir routes
Route::middleware(['auth', 'role:admin,kasir'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/products', ProductIndex::class)->name('products.index');
    Route::get('/categories', CategoryIndex::class)->name('categories.index');
    Route::get('/transactions', TransactionIndex::class)->name('transactions.index');
    Route::get('/pos', PosIndex::class)->name('pos.index');

    // AI Chat endpoint
    Route::post('/gemini/chat', [GeminiAIController::class, 'chat'])->name('gemini.chat');
});
