<?php

use App\Http\Controllers\Admin\CanteenController as AdminCanteenController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\CanteenController as UserCanteenController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\MenuController as UserMenuController;
use App\Http\Controllers\User\OrderController as UserOrderController;
use App\Http\Controllers\User\PaymentCallbackController;
use App\Http\Controllers\User\ReviewController;
use App\Http\Controllers\Vendor\CanteenController as VendorCanteenController;
use App\Http\Controllers\Vendor\DashboardController as VendorDashboardController;
use App\Http\Controllers\Vendor\MenuController as VendorMenuController;
use App\Http\Controllers\Vendor\OrderController as VendorOrderController;
use App\Http\Controllers\Vendor\ReportController;
use Illuminate\Support\Facades\Route;

// ---------------------------------------------------------------------------
// Auth Routes
// ---------------------------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    // Lupa password
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request.submit')->middleware('throttle:3,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // First login - ganti password wajib
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('password.change.form');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('password.change');

    // Profile & Settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// ---------------------------------------------------------------------------
// Public / Guest Routes (No Login Required)
// ---------------------------------------------------------------------------
// Beranda
Route::get('/', [HomeController::class, 'index'])->name('home');

// Tentang Kami
Route::get('/about', [HomeController::class, 'about'])->name('about');

// Browse kantin & menu
Route::get('/browse', [UserCanteenController::class, 'index'])->name('canteen.index');
Route::get('/canteen/{id}', [UserCanteenController::class, 'show'])->name('canteen.show');
Route::get('/canteen/{canteenId}/menu/{id}', [UserMenuController::class, 'show'])->name('menu.show');

// ---------------------------------------------------------------------------
// User / Mahasiswa Routes (Auth & Role Required)
// ---------------------------------------------------------------------------
Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    // Keranjang belanja
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store')->middleware('throttle:30,1');
    Route::put('/cart/{menuId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{menuId}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('/cart/clear-out-of-stock', [CartController::class, 'clearOutOfStock'])->name('cart.clear-out-of-stock');

    // Checkout
    Route::middleware('online.hours')->group(function () {
        Route::post('/checkout/prepare', [CheckoutController::class, 'prepare'])->name('checkout.prepare');
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store')->middleware('throttle:10,1');
    });
    Route::post('/checkout/retry/{paymentCode}', [CheckoutController::class, 'retry'])->name('checkout.retry');

    // Riwayat pesanan
    Route::get('/history', [UserOrderController::class, 'index'])->name('order.index');
    Route::get('/history/{id}', [UserOrderController::class, 'show'])->name('order.show');
    Route::delete('/history/{id}', [UserOrderController::class, 'destroy'])->name('order.destroy');
    Route::delete('/history/group/{paymentCode}', [UserOrderController::class, 'cancelGroup'])->name('order.cancel-group');
    Route::post('/history/{id}/review', [ReviewController::class, 'store'])->name('order.review')->middleware('throttle:10,1');
    Route::post('/history/{id}/reorder', [CartController::class, 'reorder'])->name('order.reorder');

    // API endpoint untuk polling status pembayaran (dipanggil oleh JavaScript di frontend)
    Route::get('/api/order/{id}/payment-status', [UserOrderController::class, 'paymentStatus'])->name('order.payment-status');
});

// ---------------------------------------------------------------------------
// Midtrans Webhook - TANPA auth middleware, TANPA CSRF (dikecualikan di bootstrap/app.php)
// ---------------------------------------------------------------------------
Route::post('/payment/notification', [PaymentCallbackController::class, 'handle'])->name('payment.notification');

// ---------------------------------------------------------------------------
// Vendor Routes
// ---------------------------------------------------------------------------
Route::middleware(['auth', 'role:vendor'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/dashboard', [VendorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/canteen/edit', [VendorCanteenController::class, 'edit'])->name('canteen.edit');
    Route::put('/canteen', [VendorCanteenController::class, 'update'])->name('canteen.update');
    Route::patch('/canteen/toggle', [VendorDashboardController::class, 'toggleStatus'])->name('canteen.toggle');
    Route::patch('/canteen/target', [VendorDashboardController::class, 'updateTarget'])->name('canteen.target');

    // Transaksi masuk
    Route::get('/order', [VendorOrderController::class, 'index'])->name('order.index');
    Route::get('/order/scan/{code}', [VendorOrderController::class, 'scan'])->name('order.scan');
    Route::get('/order/{id}', [VendorOrderController::class, 'show'])->name('order.show');
    Route::put('/order/{id}', [VendorOrderController::class, 'update'])->name('order.update');
    Route::delete('/order/{id}', [VendorOrderController::class, 'destroy'])->name('order.destroy');

    // Manajemen menu (resource)
    Route::resource('menu', VendorMenuController::class);

    // Laporan Penjualan
    Route::get('/report', [ReportController::class, 'index'])->name('report.index');
});

// ---------------------------------------------------------------------------
// Admin Routes
// ---------------------------------------------------------------------------
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Manajemen kantin (resource)
    Route::delete('/canteen/bulk-destroy', [AdminCanteenController::class, 'bulkDestroy'])->name('canteen.bulkDestroy');
    Route::resource('canteen', AdminCanteenController::class);

    // Manajemen pengguna
    Route::delete('/users/bulk-destroy', [AdminUserController::class, 'bulkDestroy'])->name('users.bulkDestroy');
    Route::patch('/users/bulk-toggle', [AdminUserController::class, 'bulkToggle'])->name('users.bulkToggle');
    Route::get('/users/import', [AdminUserController::class, 'importForm'])->name('users.import.form');
    Route::post('/users/import', [AdminUserController::class, 'import'])->name('users.import');
    Route::get('/users/import/template', [AdminUserController::class, 'downloadTemplate'])->name('users.import.template');
    Route::patch('/users/{id}/toggle', [AdminUserController::class, 'toggle'])->name('users.toggle');
    Route::resource('users', AdminUserController::class)->except(['show']);
});
