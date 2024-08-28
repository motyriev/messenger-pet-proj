<?php

use Illuminate\Support\Facades\Route;

Route::post('users/{userId}/chats/initialize', [\App\Http\Controllers\ChatController::class, 'getOrCreate']);
Route::get('users/{userId}/chats', [\App\Http\Controllers\ChatController::class, 'getAllByUserId']);
Route::get('chats/{chatId}/messages', [\App\Http\Controllers\MessageController::class, 'index']);


