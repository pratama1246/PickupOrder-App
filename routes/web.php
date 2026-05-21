<?php

use App\Http\Controllers\Admin\CanteenController as AdminCanteenController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\User\CanteenController as UserCanteenController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\MenuController as UserMenuController;
use App\Http\Controllers\User\OrderController as UserOrderController;
use App\Http\Controllers\Vendor\DashboardController as VendorDashboardController;
use App\Http\Controllers\Vendor\MenuController as VendorMenuController;
use App\Http\Controllers\Vendor\OrderController as VendorOrderController;
use Illuminate\Support\Facades\Route;

// ---------------------------------------------------------------------------
// Auth Routes
// ---------------------------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Lupa password
    Route::get('/lupa-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/lupa-password', [AuthController::class, 'forgotPassword'])->name('password.request.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // First login - ganti password wajib
    Route::get('/ganti-password', [AuthController::class, 'showChangePassword'])->name('password.change.form');
    Route::post('/ganti-password', [AuthController::class, 'changePassword'])->name('password.change');
});

// ---------------------------------------------------------------------------
// User / Mahasiswa Routes
// ---------------------------------------------------------------------------
Route::middleware('auth')->group(function () {
    // Beranda
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Browse kantin & menu
    Route::get('/pesan', [UserCanteenController::class, 'index'])->name('canteen.index');
    Route::get('/kantin/{id}', [UserCanteenController::class, 'show'])->name('canteen.show');
    Route::get('/kantin/{canteenId}/menu/{id}', [UserMenuController::class, 'show'])->name('menu.show');

    // Keranjang belanja
    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang', [CartController::class, 'store'])->name('cart.store');
    Route::put('/keranjang/{menuId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/keranjang/{menuId}', [CartController::class, 'destroy'])->name('cart.destroy');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Riwayat & antrian pesanan
    Route::get('/riwayat', [UserOrderController::class, 'index'])->name('order.index');
    Route::get('/riwayat/{id}', [UserOrderController::class, 'show'])->name('order.show');
    Route::get('/pesanan/antrian/{id}', [UserOrderController::class, 'queue'])->name('order.queue');
});

// ---------------------------------------------------------------------------
// Vendor Routes
// ---------------------------------------------------------------------------
Route::middleware(['auth'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/dashboard', [VendorDashboardController::class, 'index'])->name('dashboard');

    // Transaksi masuk
    Route::get('/order', [VendorOrderController::class, 'index'])->name('order.index');
    Route::get('/order/{id}', [VendorOrderController::class, 'show'])->name('order.show');
    Route::put('/order/{id}', [VendorOrderController::class, 'update'])->name('order.update');
    Route::delete('/order/{id}', [VendorOrderController::class, 'destroy'])->name('order.destroy');

    // Manajemen menu (resource)
    Route::resource('menu', VendorMenuController::class);
});

// ---------------------------------------------------------------------------
// Admin Routes
// ---------------------------------------------------------------------------
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Manajemen kantin (resource)
    Route::delete('/kantin/bulk-destroy', [AdminCanteenController::class, 'bulkDestroy'])->name('kantin.bulkDestroy');
    Route::resource('kantin', AdminCanteenController::class);

    // Manajemen pengguna
    Route::patch('/pengguna/{id}/toggle', [AdminUserController::class, 'toggle'])->name('pengguna.toggle');
    Route::resource('pengguna', AdminUserController::class)->except(['show']);
});
