<?php

use Illuminate\Support\Facades\Route;

// Atau bisa juga pakai closure
Route::get('/', function () {
    return redirect('/admin');
});