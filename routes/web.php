<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController; // ✅ frontend catalog
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AccountController;

// 2FA controllers
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\TwoFactorAuthController; // for your Jetstream-style routes
use App\Http\Controllers\Auth\GoogleController;

// Admin controllers for the panel
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\OrderAdminController;

// mongodb search controller
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Models\SearchLog;

// mongodb search suggestions route
Route::get('/search/suggestions', [SearchController::class, 'suggestions'])
    ->name('search.suggestions');

Route::middleware(['auth']) // adjust as you like
    ->group(function () {
        Route::get('/admin/analytics', [AnalyticsController::class, 'index'])
            ->name('admin.analytics');
    });

// --- Mongo test route ---
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

        $newCount = SearchLog::count();

        return [
            'before' => $count,
            'after'  => $newCount,
        ];
    } catch (\Throwable $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getFile() . ':' . $e->getLine(),
        ], 500);
    }
});

// --- Mongo DB name debug route ---
Route::get('/mongo-db-name', function () {
    return config('database.connections.mongodb.database');
});


// ---------- Public ----------
Route::get('/', [HomeController::class, 'index'])->name('home'); // ✅ only ONE home route
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

// ---------- Google OAuth (PUBLIC – no auth middleware!) ----------
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');


// ---------- Auth-required area ----------
Route::middleware(['auth'])->group(function () {

    // --- Two-Factor Authentication (Jetstream-style – kept as you requested) ---
    Route::get('/account/security', [AccountController::class, 'security'])
        ->name('account.security');

    Route::post('/user/two-factor-authentication', [TwoFactorAuthController::class, 'enable'])
        ->middleware(['auth', 'password.confirm'])
        ->name('two-factor.enable');

    Route::delete('/user/two-factor-authentication', [TwoFactorAuthController::class, 'disable'])
        ->middleware(['auth', 'password.confirm'])
        ->name('two-factor.disable');

    Route::post('/user/two-factor-recovery-codes', [TwoFactorAuthController::class, 'regenerateRecoveryCodes'])
        ->middleware(['auth', 'password.confirm'])
        ->name('two-factor.recovery');

    // --- Two-Factor Authentication (Custom OTP flow we built) ---
    Route::get('/two-factor', [TwoFactorController::class, 'show'])
        ->name('twofactor.show');

    Route::post('/two-factor/send', [TwoFactorController::class, 'send'])
        ->name('twofactor.send');

    Route::post('/two-factor/verify', [TwoFactorController::class, 'verify'])
        ->name('twofactor.verify');

    Route::post('/two-factor/resend', function () {
        return back()->with('status', 'A new verification code has been sent to your email.');
    })->name('two-factor.resend');

    // --- Cart ---
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/item/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/item/{item}', [CartController::class, 'remove'])->name('cart.remove');

    // --- Checkout (protected by custom 2FA) ---
    Route::get('/checkout', [OrderController::class, 'create'])
        ->name('checkout.create')
        ->middleware('twofactor');

    Route::post('/checkout', [OrderController::class, 'store'])
        ->name('checkout.store')
        ->middleware('twofactor');

    // --- Dashboard → send admins to /admin, customers to /account ---
    Route::get('/dashboard', function () {
        return auth()->user()->can('admin')
            ? redirect()->route('admin.dashboard')
            : redirect()->route('account.index');
    })->middleware(['verified'])->name('dashboard');

    // --- Customer panel (protected by custom 2FA) ---
    Route::get('/account', [AccountController::class, 'index'])
        ->name('account.index')
        ->middleware('twofactor');

    // --- Cancel order (PATCH so we can update status) ---
    Route::patch('/account/orders/{order}/cancel', [AccountController::class, 'cancel'])
        ->name('account.orders.cancel');
});


// ---------- Admin (auth + can:admin) ----------
Route::middleware(['auth', 'can:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard (simple view)
        Route::view('/', 'admin.dashboard')->name('dashboard');

        // Products CRUD
        Route::get('/products', [ProductAdminController::class, 'index'])->name('products.index');
        Route::get('/products/create', [ProductAdminController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductAdminController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductAdminController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductAdminController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductAdminController::class, 'destroy'])->name('products.destroy');

        // Orders management
        Route::get('/orders', [OrderAdminController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderAdminController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [OrderAdminController::class, 'updateStatus'])->name('orders.status');
        Route::patch('/orders/{order}/cancel', [OrderAdminController::class, 'cancel'])->name('orders.cancel');

        Route::get('/dashboard', function () {
            return auth()->user()->can('admin')
                ? redirect()->route('admin.dashboard')
                : redirect()->route('account.index');
        })->middleware(['auth','verified'])->name('dashboard');
    });
