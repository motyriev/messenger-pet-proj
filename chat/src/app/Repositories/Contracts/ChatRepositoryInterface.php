<?php

namespace App\Repositories\Contracts;

interface ChatRepositoryInterface
{
    public function findExistingChats(array $userPairs): array;

    public function create(array $userPairs): void;

    public function getAllByUserId(int $userId): array;

    public function getLastMessage(int $chatId): string;
}