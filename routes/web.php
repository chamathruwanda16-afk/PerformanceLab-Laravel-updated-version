<?php

use Illuminate\Support\Facades\Route;

// ✅ TEMP: Health check to confirm Laravel is responding on Railway
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'time' => now(),
    ]);
});

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController; 
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AccountController;

// 2FA controllers
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\TwoFactorAuthController;
use App\Http\Controllers\Auth\GoogleController;

// Admin controllers
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\AnalyticsController;

// MongoDB
use App\Http\Controllers\SearchController;
use App\Models\SearchLog;


/*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/
Route::get('/search/suggestions', [SearchController::class, 'suggestions'])
    ->name('search.suggestions');


/*
|--------------------------------------------------------------------------
| Admin Analytics (auth)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/analytics', [AnalyticsController::class, 'index'])
        ->name('admin.analytics');
});


/*
|--------------------------------------------------------------------------
| Mongo Test Routes (debug only)
|--------------------------------------------------------------------------
*/
Route::get('/mongo-test', function () {
    try {
        $count = SearchLog::count();

        SearchLog::create([
            'query'         => 'test search',
            'user_id'       => 1,
            'session_id'    => 'test-session',
            'results_count' => 0,
            'ip'            => request()->ip(),
            'created_at'    => now(),
        ]);

        return [
            'before' => $count,
            'after'  => SearchLog::count(),
        ];
    } catch (\Throwable $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getFile() . ':' . $e->getLine(),
        ], 500);
    }
});

Route::get('/mongo-db-name', fn () =>
    config('database.connections.mongodb.database')
);


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');


/*
|--------------------------------------------------------------------------
| Google OAuth (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Account security
    Route::get('/account/security', [AccountController::class, 'security'])
        ->name('account.security');

    // Jetstream 2FA
    Route::post('/user/two-factor-authentication', [TwoFactorAuthController::class, 'enable'])
        ->middleware('password.confirm')
        ->name('two-factor.enable');

    Route::delete('/user/two-factor-authentication', [TwoFactorAuthController::class, 'disable'])
        ->middleware('password.confirm')
        ->name('two-factor.disable');

    Route::post('/user/two-factor-recovery-codes', [TwoFactorAuthController::class, 'regenerateRecoveryCodes'])
        ->middleware('password.confirm')
        ->name('two-factor.recovery');

    // Custom OTP flow
    Route::get('/two-factor', [TwoFactorController::class, 'show'])->name('twofactor.show');
    Route::post('/two-factor/send', [TwoFactorController::class, 'send'])->name('twofactor.send');
    Route::post('/two-factor/verify', [TwoFactorController::class, 'verify'])->name('twofactor.verify');
    Route::post('/two-factor/resend', fn () =>
        back()->with('status', 'A new verification code has been sent.')
    )->name('two-factor.resend');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/item/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/item/{item}', [CartController::class, 'remove'])->name('cart.remove');

    // Checkout (2FA)
    Route::get('/checkout', [OrderController::class, 'create'])
        ->middleware('twofactor')
        ->name('checkout.create');

    Route::post('/checkout', [OrderController::class, 'store'])
        ->middleware('twofactor')
        ->name('checkout.store');

    // Dashboard redirect
    Route::get('/dashboard', function () {
        return auth()->user()->can('admin')
            ? redirect()->route('admin.dashboard')
            : redirect()->route('account.index');
    })->middleware('verified')->name('dashboard');

    // Customer panel
    Route::get('/account', [AccountController::class, 'index'])
        ->middleware('twofactor')
        ->name('account.index');

    Route::patch('/account/orders/{order}/cancel', [AccountController::class, 'cancel'])
        ->name('account.orders.cancel');
});


// admin panel/*
Route::middleware(['auth', 'can:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // ✅ SINGLE admin dashboard (NO DUPLICATE)
        Route::view('/', 'admin.dashboard')->name('dashboard');

        // Products
        Route::resource('products', ProductAdminController::class)->except(['show']);

        // Orders
        Route::get('/orders', [OrderAdminController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderAdminController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [OrderAdminController::class, 'updateStatus'])->name('orders.status');
        Route::patch('/orders/{order}/cancel', [OrderAdminController::class, 'cancel'])->name('orders.cancel');
    });
