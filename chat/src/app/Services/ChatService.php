<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ChatRepositoryInterface;
use App\Transformers\ChatTransformer;
use Illuminate\Support\Facades\Log;
use Motyriev\MyDTOLibrary\UserPairDTO;

class ChatService
{
    public function __construct(private ChatRepositoryInterface $repository)
    {
    }

    /**
     * @throws \Exception
     */
    public function getOrCreate(array $userPairs): array
    {
        Log::info(__METHOD__, ['userPairs' => $userPairs]);

        $existingChats = $this->repository->findExistingChats($userPairs);
        $existingPairs = $this->extractPairs($existingChats);

        Log::info('Existing chats retrieved', ['existingChats' => $existingChats, 'existingPairs' => $existingPairs]);

        $newPairs = $this->filterNewPairs($userPairs, $existingPairs);

        if (!empty($newPairs)) {
            $this->repository->create($newPairs);
            $existingChats = $this->repository->findExistingChats($userPairs);
        }

        foreach ($existingChats as &$chat) {
            $chat['lastMessage'] = $this->repository->getLastMessage($chat['id']);
        }

        return ChatTransformer::transformMany($existingChats);
    }

    public function getAllByUserId(int $userId): array
    {
        Log::info(__METHOD__, ['userId' => $userId]);

        $chats = $this->repository->getAllByUserId($userId);

        Log::info('Chats retrieved by userId', ['userId' => $userId, 'chats' => $chats]);

        $transformedChats = ChatTransformer::transformMany($chats);

        Log::info('Chats transformed', ['transformedChats' => $transformedChats]);

        return $transformedChats;
    }

    protected function extractPairs(array $chats): array
    {
        return array_map(fn($chat) => new UserPairDTO($chat['user_1'], $chat['user_2']), $chats);
    }

    protected function filterNewPairs(array $userPairs, array $existingPairs): array
    {
        return array_filter($userPairs, fn(UserPairDTO $pair) => !$this->pairExists($pair, $existingPairs));
    }

    protected function pairExists(UserPairDTO $pair, array $existingPairs): bool
    {
        foreach ($existingPairs as $existingPair) {
            if (
                ($pair->userId1 === $existingPair->userId1 && $pair->userId2 === $existingPair->userId2) ||
                ($pair->userId1 === $existingPair->userId2 && $pair->userId2 === $existingPair->userId1)
            ) {
                return true;
            }
        }
        return false;
    }
}


