<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\MessageController;
use App\Http\Middleware\CheckChatAccess;
use App\Http\Middleware\WebsocketAuth;
use App\Http\Middleware\CheckJWT;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

Broadcast::routes(['middleware' => [WebsocketAuth::class]]);
 
Route::middleware([CheckJWT::class])->group(function () {
    Route::get('/users/{userId}/dashboard', [UserController::class, 'getDashboard']);

    Route::post('/users/{userId}/friend-requests', [FriendController::class, 'addFriend'])
        ->name('users.friends.add');

    Route::patch('/users/{userId}/friend-requests', [FriendController::class, 'manageFriend'])
        ->name('users.friends.manage');

    Route::middleware([CheckChatAccess::class])->group(function () {
        Route::get('/chats/{chatId}/messages', [MessageController::class, 'getMessages']);

        Route::post('/chats/{chatId}/message', [MessageController::class, 'storeMessage']);
    });
});

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
    Route::post('register', [AuthController::class, 'register']);
});
