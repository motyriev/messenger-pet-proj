<?php

namespace Tests\Feature;

use App\Jobs\ManageFriendRequest as ManageFriendRequestJob;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ManageFriendRequestTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    /** @test */
    public function it_manages_a_friend_request_successfully()
    {
        Queue::fake();

        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $headers = [
            'Authorization' => 'Bearer ' . $token,
        ];

        $data = [
            'requestId' => 1,
            'status'    => 'accepted',
        ];

        $response = $this->patchJson('/api/users/1/friend-requests', $data, $headers);

        $response->assertStatus(202);

        Queue::assertPushed(ManageFriendRequestJob::class, function ($job) use ($data) {
            return $job->dto->requestId === $data['requestId']
                && $job->dto->status === $data['status'];
        });
    }

    /** @test */
    public function it_handles_error_when_managing_a_friend_request()
    {
        Queue::fake();

        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $headers = [
            'Authorization' => 'Bearer ' . $token,
        ];

        $data = [
            'requestId' => '1',
            'status'    => 'ffffffff',
        ];

        $response = $this->patchJson('/api/users/1/friend-requests', $data, $headers);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['status']);
        Queue::assertNotPushed(ManageFriendRequestJob::class);
    }
}
