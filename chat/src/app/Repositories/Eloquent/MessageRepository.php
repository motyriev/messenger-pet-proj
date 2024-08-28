<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Message;
use App\Repositories\Contracts\MessageRepositoryInterface;
use Motyriev\MyDTOLibrary\MessageStoreDTO;

class MessageRepository extends EloquentModelRepository implements MessageRepositoryInterface
{
    protected $eloquent = Message::class;

    /**
     * @throws \Exception
     */
    public function create(MessageStoreDTO $message): array
    {
        $message = $this->model()->create([
            'user_id' => $message->userId,
            'chat_id' => $message->chatId,
            'body'    => $message->body
        ]);

        return $message->toArray();
    }

    public function getAllByChatId(int $chatId): array
    {
        $messages = $this->model()->where('chat_id', $chatId)->get();
        return $messages->toArray();
    }
}