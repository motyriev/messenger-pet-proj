<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Tymon\JWTAuth\Facades\JWTAuth;

Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    $payload = json_decode(Auth::payload(), true);
    if (empty($payload['chatIds']) || !in_array($chatId, $payload['chatIds'])) {
        return false;
    }

    return true;
});
