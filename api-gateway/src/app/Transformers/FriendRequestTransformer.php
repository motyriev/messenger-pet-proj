<?php

declare(strict_types=1);

namespace App\Transformers;

use App\Transformers\Contracts\Transformable;
use Illuminate\Support\Facades\Redis;
use JetBrains\PhpStorm\ArrayShape;

class FriendRequestTransformer implements Transformable
{
    #[ArrayShape(['id' => "int", 'requesterId' => "int", 'email' => "string"])]
    public static function transform(array $friendRequest): array
    {
        return [
            'id'          => (int)$friendRequest['id'],
            'requesterId' => (int)$friendRequest['requesterId'],
            'email'       => (string)(Redis::hget("user:{$friendRequest['requesterId']}", 'email') ?? ''),
        ];
    }


    public static function transformMany(array $friendRequests): array
    {
        return array_map([self::class, 'transform'], $friendRequests);
    }
}