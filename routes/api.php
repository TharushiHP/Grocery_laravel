<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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