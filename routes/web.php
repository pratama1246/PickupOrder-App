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
