<?php

declare(strict_types=1);

namespace App\Services;

use App\Factories\MessageDTOsFactory;
use App\Helpers\ArrayHelper;
use App\Jobs\MessageNotify;
use App\Repositories\Contracts\MessageRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Motyriev\MyDTOLibrary\MessageDTO;
use Motyriev\MyDTOLibrary\MessageStoreDTO;

class MessageService
{
    public function __construct(private MessageRepositoryInterface $repository)
    {
    }

    public function getAllByChatId(int $chatId): array
    {
        Log::info('Fetching all messages by chatId', ['chatId' => $chatId]);

        $messages = $this->repository->getAllByChatId($chatId);
        return MessageDTOsFactory::createFromArray($messages);
    }

    public function create(MessageStoreDTO $message): void
    {
        $message = $this->repository->create($message);
        Log::info('Message created successfully', ['msg' => $message]);

        $ccMessage = ArrayHelper::convertKeysToCamelCase($message);
        $ccMessage['userEmail'] = Redis::hget("user:{$message['user_id']}", 'email') ?? (string)$message['user_id'];

        $messageDTO = MessageDTO::fromArray($ccMessage);

        Redis::hmSet('chat:' . $messageDTO->chatId . ':last_message', ['body' => $messageDTO->body]);
        Log::debug('Last message updated in Redis', [
            'chatId'            => $messageDTO->chatId,
            'last_message_body' => $messageDTO->body,
        ]);

        MessageNotify::dispatch($messageDTO)->onQueue('message_notify_queue');
        Log::info('Message notification dispatched', [
            'queue'      => 'message_notify_queue',
            'messageDTO' => $messageDTO,
        ]);
    }
}