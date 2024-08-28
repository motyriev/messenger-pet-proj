<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\Status;
use App\Services\ChatService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

class ChatControllerTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    public function test_get_or_create_success()
    {
        $mockService = Mockery::mock(ChatService::class);
        $this->app->instance(ChatService::class, $mockService);

        $mockService->shouldReceive('getOrCreate')
            ->once()
            ->andReturn(['chat1', 'chat2']);

        $requestData = [
            'userPairs' => [
                ['userId1' => 1, 'userId2' => 2],
                ['userId1' => 3, 'userId2' => 4]
            ]
        ];

        $response = $this->postJson('/api/users/1/chats/initialize', $requestData);

        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJson([
            'data' => ['chat1', 'chat2'],
            'status' => Status::Success->value,
        ]);
    }

    public function test_get_all_by_user_id_success()
    {
        $mockService = Mockery::mock(ChatService::class);
        $this->app->instance(ChatService::class, $mockService);

        $mockService->shouldReceive('getAllByUserId')
            ->once()
            ->with(1)
            ->andReturn(['chat1', 'chat2']);
        $response = $this->getJson('/api/users/1/chats');

        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJson([
            'data' => ['chat1', 'chat2'],
            'status' => Status::Success->value,
        ]);
    }
}
