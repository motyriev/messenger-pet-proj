<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Motyriev\MyDTOLibrary\MessageStoreDTO;

interface MessageRepositoryInterface
{
    public function getAllByChatId(int $chatId): array;

    public function create(MessageStoreDTO $message): array;
}