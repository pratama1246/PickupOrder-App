<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('user.index');
});

// Temporary routes to preview user pages during development
Route::view('/user', 'user.index');
Route::view('/riwayat', 'user.riwayat');
Route::view('/riwayat/detail', 'user.order-detail');
Route::view('/pesan', 'user.pesanan');
Route::view('/kantin', 'user.kantin');
Route::view('/keranjang', 'user.keranjang');
Route::view('/menu-detail', 'user.menu-detail');

Route::view('/admin/dashboard', 'admin.dashboard');
Route::view('/admin/kantin', 'admin.kantin');
Route::view('/admin/kantin/tambah', 'admin.kantin-tambah');
Route::view('/admin/pengguna', 'admin.pengguna');

// Vendor routes
Route::view('/vendor/dashboard', 'vendor.dashboard');
Route::view('/vendor/order', 'vendor.order');
Route::view('/vendor/order/detail', 'vendor.order-detail');
Route::view('/vendor/menu', 'vendor.menu');
