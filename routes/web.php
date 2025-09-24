<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ProductCatalog;
use App\Livewire\AdminProductManager;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AdminController;

Route::get('/', ProductCatalog::class)->name('home');

Route::get('/products', ProductCatalog::class)->name('products');

// Checkout routes
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('process.checkout');
});

// Admin Authentication Routes (accessible without being logged in)
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'login']);
});

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


