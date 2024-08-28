<?php

namespace Tests\Unit\Services;

use App\Services\DashboardService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class DashboardServiceTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_returns_dashboard_data_successfully()
    {
        $userId = 1;

        Http::fake([
            config('app.urls.friends_api'). "users/$userId/friend-requests" => Http::response([
                'friends' => [
                    ['friendId' => 2],
                ],
                'friendRequests' => [
                    ['requesterId' => 3],
                ],
            ], 200),
        ]);

        Http::fake([
            config('app.urls.chat_api') . 'users/1/chats/initialize' => Http::response([
                'data' => [
                    [
                        'id' => 10,
                        'user1' => 1,
                        'user2' => 2,
                        'lastMessage' => 'Hello',
                    ]
                ],
            ], 200),
        ]);

        $service = new DashboardService();
        $result = $service->getDashboard($userId);

        $this->assertArrayHasKey('friendRequests', $result);
        $this->assertArrayHasKey('users', $result);
        $this->assertArrayHasKey('friends', $result);
    }

    /** @test */
    public function it_returns_empty_dashboard_on_friend_request_failure()
    {
        $userId = 1;

        Http::fake([
            config('app.urls.friends_api'). "users/$userId/friend-requests" => Http::response([], 500),
        ]);

        $service = new DashboardService();
        $result = $service->getDashboard($userId);

        $this->assertEquals([
            'friendRequests' => [],
            'users'          => [],
            'friends'        => [],
        ], $result);
    }

    /** @test */
    public function it_logs_error_when_chat_request_fails()
    {
        $userId = 1;

        Http::fake([
            config('app.urls.friends_api'). "users/$userId/friend-requests" => Http::response([
                'friends' => [
                    ['friendId' => 2],
                ],
                'friendRequests' => [
                    ['requesterId' => 3],
                ],
            ], 200),
        ]);

        Http::fake([
            config('app.urls.chat_api') . 'chats' => Http::response([], 500),
        ]);

        Log::shouldReceive('error')->once();

        $service = new DashboardService();
        $result = $service->getDashboard($userId);

        $this->assertEquals([
            'friendRequests' => [],
            'users'          => [],
            'friends'        => [],
        ], $result);
    }
}
