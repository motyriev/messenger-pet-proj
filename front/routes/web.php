<?php

use App\Http\Controllers\AppController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AppController::class, 'index'])->name('home');
Route::get('{any}', [AppController::class, 'index'])->name('home');

