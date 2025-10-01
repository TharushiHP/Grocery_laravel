<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ProductCatalog;
use App\Livewire\AdminProductManager;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AdminController;

// Health check endpoint for Railway
Route::get('/health', function () {
    try {
        // Test database connection
        \DB::connection()->getPdo();
        $dbStatus = 'connected';
    } catch (\Exception $e) {
        $dbStatus = 'disconnected: ' . $e->getMessage();
    }
    
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'service' => 'Laravel Grocery Cart',
        'database' => $dbStatus,
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version()
    ]);
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

// Demo Route to Show NoSQL and Sanctum Implementation
Route::get('/demo-implementations', function () {
    try {
        $results = [];
        
        // 1. Test NoSQL (DocumentStore)
        $store = app(\App\Services\DocumentStore::class);
        
        $demoDoc = [
            'demo_type' => 'academic_showcase',
            'features' => ['NoSQL', 'Document Storage', 'JSON Collections'],
            'timestamp' => now()->toISOString(),
            'message' => 'MongoDB-like functionality working!'
        ];
        
        $stored = $store->store('academic_demo', $demoDoc);
        $results['nosql'] = [
            'status' => 'SUCCESS',
            'implementation' => 'Custom DocumentStore Service (MongoDB-like)',
            'document_id' => $stored['_id'],
            'storage_location' => 'storage/app/documents/academic_demo/',
            'features' => ['Flexible Schema', 'JSON Documents', 'Collections', 'CRUD Operations']
        ];
        
        // 2. Test Sanctum API
        $user = \App\Models\User::first();
        if ($user) {
            $token = $user->createToken('demo-showcase')->plainTextToken;
            $results['sanctum'] = [
                'status' => 'SUCCESS',
                'implementation' => 'Laravel Sanctum API Authentication',
                'token_generated' => substr($token, 0, 30) . '...',
                'user' => $user->name,
                'features' => ['Token Authentication', 'API Security', 'Protected Routes', 'Bearer Tokens']
            ];
        } else {
            $results['sanctum'] = [
                'status' => 'INFO',
                'message' => 'Create a user first to test Sanctum tokens'
            ];
        }
        
        // 3. Show API endpoints
        $results['api_endpoints'] = [
            'nosql_demo' => url('/api/v1/nosql-test'),
            'external_api_demo' => url('/api/v1/external-api-demo'),
            'sanctum_register' => url('/api/v1/register'),
            'sanctum_login' => url('/api/v1/login'),
            'protected_profile' => url('/api/v1/profile')
        ];
        
        return view('demo-implementations', compact('results'));
        
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
})->name('demo.implementations');

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


