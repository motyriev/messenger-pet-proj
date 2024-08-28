<?php

declare(strict_types=1);

namespace App\Factories;

use App\Helpers\ArrayHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Motyriev\MyDTOLibrary\MessageDTO;

class MessageDTOsFactory implements MessageDTOsFactoryInterface
{
    /**
     * @throws \ReflectionException
     */
    public static function createFromArray(array $messages): array
    {
        try {
            $messageDTOs = [];
            foreach ($messages as $message) {
                $ccMessage = ArrayHelper::convertKeysToCamelCase($message);
                $ccMessage['userEmail'] = Redis::hget("user:{$ccMessage['userId']}", 'email') ?? (string)$ccMessage['userId'];
                $messageDTOs[] = MessageDTO::fromArray($ccMessage);
            }
            return $messageDTOs;
        } catch (\Throwable $t) {
            Log::error('Error creating MessageDTOs from array', [
                'exception' => $t,
                'messages'  => $messages,
            ]);
            return [];
        }
    }
}