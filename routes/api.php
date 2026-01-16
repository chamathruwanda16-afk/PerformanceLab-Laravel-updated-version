<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\AuthTokenController;

/*
|--------------------------------------------------------------------------
| API Login 
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthTokenController::class, 'issueToken']);
Route::post('/token', [AuthTokenController::class, 'issueToken']); // optional

/*
|--------------------------------------------------------------------------
| Protected API Routes 
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    //  Products API 
    Route::get('/products', fn () =>
        Product::with('category')->paginate(20)
    );

    // Authenticated user info
    Route::get('/me', fn (Request $request) => $request->user());

    // Orders
    Route::post('/orders', [OrderApiController::class, 'store']);
    Route::get('/orders', [OrderApiController::class, 'index']);
});
