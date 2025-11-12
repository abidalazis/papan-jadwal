<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

// Homepage dengan dashboard undangan
Route::get('/', [HomeController::class, 'index'])->name('home');

// Route::get('/', function () {
//     return view('welcome');
// });
