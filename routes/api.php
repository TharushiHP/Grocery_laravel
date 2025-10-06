<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;

// Health check endpoint for Railway
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'service' => 'Laravel Grocery Cart',
        'database' => 'connected'
    ]);
});

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

// Simple API endpoint for testing purposes only
Route::get('/status', function () {
    return response()->json([
        'status' => 'online',
        'message' => 'Grocery Laravel Application',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0'
    ]);
});

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'database' => 'connected',
        'timestamp' => now()->toISOString()
    ]);
});

/*
|--------------------------------------------------------------------------
| Authentication Routes (Public)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Product Routes (Public)
|--------------------------------------------------------------------------
*/
Route::prefix('products')->group(function () {
    // Public product routes (no authentication required)
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/categories', [ProductController::class, 'categories']);
    Route::get('/category/{category}', [ProductController::class, 'getByCategory']);
    Route::get('/search', [ProductController::class, 'search']);
    Route::get('/featured', [ProductController::class, 'featured']);
    Route::get('/low-stock', [ProductController::class, 'lowStock']);
    Route::get('/{id}', [ProductController::class, 'show']);
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Require Authentication)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    
    // User Authentication Routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
    });
    
    // User Management Routes (Optional - for admin features)
    Route::get('/users', [AuthController::class, 'getAllUsers']);
    
    // Admin Product Management Routes (Optional)
    Route::prefix('admin/products')->group(function () {
        Route::post('/', [ProductController::class, 'store']);
        Route::put('/{id}', [ProductController::class, 'update']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);
    });
    
    // Cart-related routes can be added here if needed
    // For now, cart is handled locally in Flutter app
});

/*
|--------------------------------------------------------------------------
| Fallback Route
|--------------------------------------------------------------------------
*/
Route::fallback(function() {
    return response()->json([
        'success' => false,
        'message' => 'Endpoint not found. Please check the API documentation.',
        'available_endpoints' => [
            'GET /api/status' => 'API status check',
            'GET /api/health' => 'Health check',
            'POST /api/auth/register' => 'User registration',
            'POST /api/auth/login' => 'User login',
            'POST /api/auth/logout' => 'User logout (requires auth)',
            'GET /api/auth/user' => 'Get authenticated user (requires auth)',
            'PUT /api/auth/profile' => 'Update user profile (requires auth)',
            'GET /api/products' => 'Get all products',
            'GET /api/products/categories' => 'Get all categories',
            'GET /api/products/category/{category}' => 'Get products by category',
            'GET /api/products/search' => 'Search products',
            'GET /api/products/featured' => 'Get featured products',
            'GET /api/products/{id}' => 'Get specific product',
        ]
    ], 404);
});