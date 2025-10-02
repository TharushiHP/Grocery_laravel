<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ProductCatalog;
use App\Livewire\AdminProductManager;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AdminController;

// Health check endpoint for Railway
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'service' => 'Laravel Grocery Cart',
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version(),
        'deployment_time' => 'October 2, 2025 - Database connected'
    ]);
});

// Database health check (separate endpoint)
Route::get('/health/database', function () {
    try {
        \DB::connection()->getPdo();
        $productCount = \App\Models\Product::count();
        $userCount = \App\Models\User::count();
        return response()->json([
            'database' => 'connected',
            'products' => $productCount,
            'users' => $userCount,
            'database_name' => \DB::connection()->getDatabaseName()
        ]);
    } catch (\Exception $e) {
        return response()->json(['database' => 'disconnected: ' . $e->getMessage()], 500);
    }
});

Route::get('/', ProductCatalog::class)->name('home');

Route::get('/products', ProductCatalog::class)->name('products');

// Checkout routes
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('process.checkout');
    Route::get('/order/success/{order}', [CheckoutController::class, 'success'])->name('order.success');
    
    // Orders routes
    Route::get('/orders', function () {
        return view('orders.index');
    })->name('orders.index');
});

// Admin Authentication Routes (accessible without being logged in)
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'login']);
});

// MongoDB Test Route
Route::get('/test-mongodb', [App\Http\Controllers\MongoDBTestController::class, 'testConnection'])
    ->name('test.mongodb');

// Protected Admin Routes (require admin authentication)
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
    
    // Product Management Routes
    Route::get('/products/{product}', [AdminController::class, 'showProduct'])->name('admin.products.show');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('admin.products.store');
    Route::put('/products/{product}', [AdminController::class, 'updateProduct'])->name('admin.products.update');
    Route::delete('/products/{product}', [AdminController::class, 'destroyProduct'])->name('admin.products.destroy');
});


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Keep the old admin route for backward compatibility if needed
    // Route::middleware('admin')->group(function () {
    //     Route::get('/admin/products', AdminProductManager::class)->name('admin.products');
    // });
});


