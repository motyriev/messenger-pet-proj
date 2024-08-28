<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;
use App\Jobs\MessageStore;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class MessageStoreTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_stores_a_message_successfully()
    {
        Queue::fake();

        $user = User::factory()->create();

        $token = JWTAuth::claims(['chatIds' => [1]])->fromUser($user);

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'X-Trace-Id'    => '12345abc'
        ];

        $data = [
            'userId' => $user->id,
            'chatId' => 1,
            'body'   => 'This is a test message'
        ];

        $response = $this->postJson('api/chats/1/message', $data, $headers);

        $response->assertStatus(202);

        Queue::assertPushed(MessageStore::class, function ($job) use ($data, $headers) {
            return $job->dto->traceId === $headers['X-Trace-Id'] &&
                $job->dto->userId === $data['userId'] &&
                $job->dto->chatId === $data['chatId'] &&
                $job->dto->body === $data['body'];
        });
    }
}
