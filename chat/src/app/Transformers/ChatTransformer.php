<?php

declare(strict_types=1);

namespace App\Transformers;

use App\Transformers\Contracts\Transformable;

class ChatTransformer implements Transformable
{
    public static function transform(array $chat): array
    {
        return [
            'id' => $chat['id'],
            'user1' => $chat['user_1'],
            'user2' => $chat['user_2'],
            'lastMessage' => $chat['lastMessage'] ?? ''
        ];
    }

    public static function transformMany(array $chats): array
    {
        return array_map([self::class, 'transform'], $chats);
    }
}