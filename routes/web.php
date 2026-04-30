<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('user.index');
});

// Temporary route to preview user index during development
Route::view('/user', 'user.index');
