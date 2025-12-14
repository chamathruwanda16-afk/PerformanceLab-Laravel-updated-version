<?php

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\AuthTokenController;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

// ---------- Public product list ----------
Route::get('/products', fn () =>
    Product::with('category')->paginate(20)
);

// ---------- API LOGIN (issue Sanctum token) ----------
// Option A: use your existing AuthTokenController
Route::post('/login', [AuthTokenController::class, 'issueToken']);

// (Optional) keep old /token route if you still want it
Route::post('/token', [AuthTokenController::class, 'issueToken']);

// ---------- Protected API routes (Sanctum) ----------
Route::middleware('auth:sanctum')->group(function () {

    // Current authenticated user (for testing)
    Route::get('/me', fn (Request $request) => $request->user());

    // Create order (POST) – uses OrderApiController@store
    Route::post('/orders', [OrderApiController::class, 'store']);

    // List orders (GET) – uses OrderApiController@index (if you created it)
    Route::get('/orders', [OrderApiController::class, 'index']);
});
