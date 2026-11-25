
<?php

use App\Http\Controllers\AdminPageController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\PromotionController;
use Illuminate\Support\Facades\Route;

// ==================== TRANG CHỦ ====================

Route::view('/', 'welcome');

// ==================== MÓN ĂN ====================

Route::get('/foods', [FoodController::class, 'index']);

Route::get('/foods/{id}', [FoodController::class, 'showDetail']);

// ==================== XÁC THỰC ====================

Route::get('/login', [AuthController::class, 'index'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::post('/register', [AuthController::class, 'register']);

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

Route::get('/logout', [AuthController::class, 'logout']);

// ==================== KHÁCH HÀNG ====================

Route::middleware('customer')->group(function () {
    Route::post('/customer/update-address', [AuthController::class, 'updateAddress'])
        ->name('customer.update-address');

    // -------------------- GIỎ HÀNG --------------------

    // Hiển thị giỏ hàng
    Route::get('/cart', [CartController::class, 'index'])
        ->name('cart.index');

    // Thêm món vào giỏ hàng
    Route::post('/cart/add', [CartController::class, 'add'])
        ->name('cart.add');

    // Đếm số lượng món trong giỏ hàng
    Route::get('/cart/count', [CartController::class, 'count'])
        ->name('cart.count');

    // Áp dụng voucher
    Route::post('/cart/promotion', [CartController::class, 'applyPromotion'])
        ->name('cart.promotion.apply');

    // Xóa tất cả món trong giỏ hàng
    Route::delete('/cart/clear', [CartController::class, 'clear'])
        ->name('cart.clear');

    // Cập nhật số lượng món
    Route::patch('/cart/items/{cart}', [CartController::class, 'update'])
        ->name('cart.update');

    // Xóa từng món trong giỏ hàng
    Route::delete('/cart/items/{cart}', [CartController::class, 'destroy'])
        ->name('cart.destroy');

    // -------------------- THANH TOÁN --------------------

    // Hiển thị trang thanh toán
    Route::get('/cart/checkout', [CartController::class, 'checkout'])
        ->name('cart.checkout');

    // Xác nhận đặt hàng
    Route::post('/cart/checkout', [CartController::class, 'placeOrder'])
        ->name('cart.place-order');

    // -------------------- ĐƠN HÀNG --------------------

    // Danh sách đơn hàng
    Route::get('/orders', [CustomerOrderController::class, 'index'])
        ->name('customer.orders.index');

    // Chi tiết đơn hàng
    Route::get('/orders/{order}', [CustomerOrderController::class, 'show'])
        ->name('customer.orders.show');

    // Theo dõi trạng thái đơn hàng
    Route::get('/orders/{order}/status', [CustomerOrderController::class, 'status'])
        ->name('customer.orders.status');

    // Hủy đơn hàng
    Route::post('/orders/{order}/cancel', [CustomerOrderController::class, 'cancel'])
        ->name('customer.orders.cancel');

    // -------------------- VOUCHER --------------------

    // Danh sách voucher
    Route::get('/promotions', [PromotionController::class, 'index'])
        ->name('promotions.index');

    // Lưu voucher
    Route::post('/promotions/{promotion}/save', [PromotionController::class, 'store'])
        ->name('promotions.save');
});

// ==================== QUẢN TRỊ VIÊN ====================

Route::middleware('admin')->group(function () {

    // -------------------- DASHBOARD --------------------

    Route::get('/admin/dashboard', [AdminPageController::class, 'dashboard'])
        ->name('admin.dashboard');

    Route::get('/admin/dashboard/stats', [AdminPageController::class, 'dashboardStats'])
        ->name('admin.dashboard.stats');

    // -------------------- KHÁCH HÀNG --------------------

    Route::get('/admin/customers', [AdminPageController::class, 'customers'])
        ->name('admin.customers.index');

    // -------------------- QUẢN LÝ ĐƠN HÀNG --------------------

    Route::get('/admin/orders', [AdminPageController::class, 'orders'])
        ->name('admin.orders.index');

    Route::get('/admin/orders/{order}', [AdminPageController::class, 'showOrder'])
        ->name('admin.orders.show');

    Route::patch('/admin/orders/{order}/status', [AdminPageController::class, 'updateOrderStatus'])
        ->name('admin.orders.update-status');

    // -------------------- QUẢN LÝ TÀI KHOẢN --------------------

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
