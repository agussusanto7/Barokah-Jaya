<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\PosController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/auth/register', [AuthController::class, 'register'])->name('api.auth.register');
Route::post('/auth/login', [AuthController::class, 'login'])->name('api.auth.login');

// Public product catalog
Route::get('/products', [ProductController::class, 'index'])->name('api.products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('api.products.show');

// Public categories
Route::get('/categories', [CategoryController::class, 'index'])->name('api.categories.index');

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {

    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
    Route::get('/auth/me', [AuthController::class, 'me'])->name('api.auth.me');

    // Products management
    Route::post('/products', [ProductController::class, 'store'])->name('api.products.store');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('api.products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('api.products.destroy');

    // Categories management
    Route::post('/categories', [CategoryController::class, 'store'])->name('api.categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('api.categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('api.categories.destroy');

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('api.transactions.index');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('api.transactions.store');
    Route::get('/transactions/{id}', [TransactionController::class, 'show'])->name('api.transactions.show');
    Route::put('/transactions/{id}', [TransactionController::class, 'update'])->name('api.transactions.update');
    Route::delete('/transactions/{id}', [TransactionController::class, 'destroy'])->name('api.transactions.destroy');
    Route::get('/transactions/daily', [TransactionController::class, 'dailyReport'])->name('api.transactions.daily');
    Route::get('/transactions/summary', [TransactionController::class, 'summary'])->name('api.transactions.summary');

    // POS specific routes
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('api.pos.checkout');
    Route::get('/pos/receipt/{id}', [PosController::class, 'receipt'])->name('api.pos.receipt');
    Route::get('/pos/daily-sales', [PosController::class, 'dailySales'])->name('api.pos.daily-sales');

    // AI Chat integration
    Route::post('/ai/chat', [\App\Http\Controllers\GeminiAIController::class, 'chatApi'])->name('api.ai.chat');

    // User management
    Route::get('/users', [UserController::class, 'index'])->name('api.users.index');
    Route::post('/users', [UserController::class, 'store'])->name('api.users.store');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('api.users.show');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('api.users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('api.users.destroy');
});

// Admin only routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // Admin-specific endpoints
    Route::get('/admin/dashboard/stats', [App\Http\Controllers\Api\DashboardController::class, 'stats'])->name('api.admin.dashboard.stats');
    Route::get('/admin/reports/sales', [App\Http\Controllers\Api\ReportController::class, 'salesReport'])->name('api.admin.reports.sales');
    Route::get('/admin/reports/inventory', [App\Http\Controllers\Api\ReportController::class, 'inventoryReport'])->name('api.admin.reports.inventory');
});