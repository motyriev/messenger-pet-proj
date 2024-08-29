<?php

declare(strict_types=1);

namespace App\Transformers;

use App\Transformers\Contracts\Transformable;
use Illuminate\Support\Facades\Redis;
use JetBrains\PhpStorm\ArrayShape;

class FriendTransformer implements Transformable
{
    public static function transform(array $friend): array
    {
        return [
            'id'          => (int)$friend['friendId'],
            'email'       => (string)Redis::hget("user:{$friend['friendId']}", 'email') ?? '',
            'chatId'      => (int)$friend['chatId'],
            'lastMessage' => (string)$friend['lastMessage'],
        ];
    }

    public static function transformMany(array $friends): array
    {
        return array_map([self::class, 'transform'], $friends);
    }
}