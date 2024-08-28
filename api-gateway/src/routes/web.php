<?php

use Illuminate\Support\Facades\Route;

Route::get('/documentation', function () {
    return view('l5-swagger::index');
});