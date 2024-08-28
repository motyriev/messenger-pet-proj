<?php

namespace Tests\Unit\Services;

use App\Repositories\Contracts\ChatRepositoryInterface;
use App\Services\ChatService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery;
use Motyriev\MyDTOLibrary\UserPairDTO;
use Tests\TestCase;

class ChatServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_get_or_create_with_new_pairs(): void
    {
        $userPairs = [
            new UserPairDTO(1, 2),
            new UserPairDTO(1, 3),
        ];

        $existingChats = [
            ['id' => 1, 'user_1' => 1, 'user_2' => 2],
        ];

        $repository = Mockery::mock(ChatRepositoryInterface::class);
        $repository->shouldReceive('findExistingChats')
            ->twice()
            ->andReturn($existingChats);

        $repository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($newPairs) {
                return is_array($newPairs) && count($newPairs) === 1 && $newPairs[1] instanceof UserPairDTO;
            }));

        $repository->shouldReceive('getLastMessage')
            ->once()
            ->with(1)
            ->andReturn('Last message');

        $this->app->instance(ChatRepositoryInterface::class, $repository);

        $service = new ChatService($repository);
        $result = $service->getOrCreate($userPairs);

        $this->assertCount(1, $result);
        $this->assertEquals('Last message', $result[0]['lastMessage'] ?? null);
    }

    public function test_get_all_by_user_id(): void
    {
        $userId = 1;
        $chats = [
            ['id' => 1, 'user_1' => 1, 'user_2' => 2],
            ['id' => 2, 'user_1' => 1, 'user_2' => 3],
        ];

        $repository = Mockery::mock(ChatRepositoryInterface::class);
        $repository->shouldReceive('getAllByUserId')
            ->once()
            ->with($userId)
            ->andReturn($chats);

        $this->app->instance(ChatRepositoryInterface::class, $repository);

        $service = new ChatService($repository);
        $result = $service->getAllByUserId($userId);

        $this->assertCount(2, $result);
        $this->assertEquals(1, $result[0]['id']);
    }
}

