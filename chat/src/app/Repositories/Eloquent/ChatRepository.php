<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Chat;
use App\Repositories\Contracts\ChatRepositoryInterface;
use Illuminate\Support\Facades\Redis;
use Motyriev\MyDTOLibrary\UserPairDTO;

class ChatRepository extends EloquentModelRepository implements ChatRepositoryInterface
{
    protected $eloquent = Chat::class;

    public function findExistingChats(array $userPairs): array
    {
        $result = $this->model()->where(function ($query) use ($userPairs) {
            foreach ($userPairs as $pair) {
                $query->orWhere(fn($q) => $q->where('user_1', $pair->userId1)->where('user_2', $pair->userId2))
                    ->orWhere(fn($q) => $q->where('user_1', $pair->userId2)->where('user_2', $pair->userId1));
            }
        })->get();

        return $result->toArray();
    }

    public function create(array $userPairs): void
    {
        $newChats = array_map(fn(UserPairDTO $pair) => ['user_1' => $pair->userId1, 'user_2' => $pair->userId2],
            $userPairs);
        $this->model()->insert($newChats);
    }

    public function getAllByUserId(int $userId): array
    {
        $result = $this->model()->where(fn($query) => $query->where('user_1', $userId)->orWhere('user_2', $userId)
        )->get();
        return $result->toArray();
    }

    public function getLastMessage(int $chatId): string
    {
        $lastMessage = Redis::hget("chat:{$chatId}:last_message", 'body');
        if ($lastMessage) {
            return $lastMessage;
        }

        $chat = $this->model()->find($chatId);
        if ($chat && $chat->lastMessage) {
            return $chat->lastMessage->body;
        }

        return '';
    }
}