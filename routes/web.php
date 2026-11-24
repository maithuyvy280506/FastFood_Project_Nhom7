<?php

use App\Http\Controllers\AdminPageController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\PromotionController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// Food
Route::get('/foods', [FoodController::class, 'index']);
Route::get('/foods/{id}', [FoodController::class, 'showDetail']);

// Login
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::get('/logout', [AuthController::class, 'logout']);

Route::middleware('customer')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
    Route::post('/cart/promotion', [CartController::class, 'applyPromotion'])->name('cart.promotion.apply');
    Route::patch('/cart/items/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/items/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::get('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/cart/checkout', [CartController::class, 'placeOrder'])->name('cart.place-order');

    Route::get('/orders', [CustomerOrderController::class, 'index'])->name('customer.orders.index');
    Route::get('/orders/{order}', [CustomerOrderController::class, 'show'])->name('customer.orders.show');
    Route::get('/orders/{order}/status', [CustomerOrderController::class, 'status'])->name('customer.orders.status');
    Route::post('/orders/{order}/cancel', [CustomerOrderController::class, 'cancel'])->name('customer.orders.cancel');

    Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions.index');
    Route::post('/promotions/{promotion}/save', [PromotionController::class, 'store'])->name('promotions.save');
});

Route::middleware('admin')->group(function () {
    Route::get('/admin/dashboard', [AdminPageController::class, 'dashboard'])
        ->name('admin.dashboard');
    Route::get('/admin/dashboard/stats', [AdminPageController::class, 'dashboardStats'])
        ->name('admin.dashboard.stats');
    Route::get('/admin/customers', [AdminPageController::class, 'customers'])
        ->name('admin.customers.index');
    Route::get('/admin/orders', [AdminPageController::class, 'orders'])
        ->name('admin.orders.index');
    Route::get('/admin/orders/{order}', [AdminPageController::class, 'showOrder'])
        ->name('admin.orders.show');
    Route::patch('/admin/orders/{order}/status', [AdminPageController::class, 'updateOrderStatus'])
        ->name('admin.orders.update-status');
    Route::get('/admin/users', [AdminUserController::class, 'index'])
        ->name('admin.users.index');
    Route::get('/admin/users/create', [AdminUserController::class, 'create'])
        ->name('admin.users.create');
    Route::get('/admin/users/{id}/edit', [AdminUserController::class, 'edit'])
        ->name('admin.users.edit');
    Route::post('/admin/users', [AdminUserController::class, 'store'])
        ->name('admin.users.store');
    Route::post('/admin/users/{id}/toggle-status', [AdminUserController::class, 'toggleStatus'])
        ->name('admin.users.toggle-status');
});
