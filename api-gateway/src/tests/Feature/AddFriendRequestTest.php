<?php

namespace Tests\Feature;

use App\Jobs\AddFriendRequest as AddFriendRequestJob;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AddFriendRequestTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    /** @test */
    public function it_adds_a_friend_successfully()
    {
        Queue::fake();

        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'X-Trace-Id'    => '12345abc'
        ];

        $data = [
            'requesterId' => $user->id,
            'requestedId' => 2,
        ];

        $response = $this->postJson('api/users/1/friend-requests', $data, $headers);

        $response->assertStatus(202);
        Queue::assertPushed(AddFriendRequestJob::class, function ($job) use ($data, $headers) {
            return $job->dto->traceId === $headers['X-Trace-Id'] &&
                $job->dto->requesterId === $data['requesterId'] &&
                $job->dto->requestedId === $data['requestedId'];
        });
    }

    /** @test */
    public function it_handles_error_when_adding_a_friend()
    {
        Queue::fake();

        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $headers = [
            'Authorization' => 'Bearer ' . $token,
        ];

        $data = [
            'requesterId' => $user->id,
            'xyzasjkndl' => 2,
        ];

        $response = $this->postJson('api/users/1/friend-requests', $data, $headers);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'The requested id field is required.']);
        Queue::assertNotPushed(AddFriendRequestJob::class);
    }

}
