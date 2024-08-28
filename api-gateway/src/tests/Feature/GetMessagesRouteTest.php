<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class GetMessagesRouteTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function an_authenticated_user_can_get_messages()
    {
        $user = User::first();

        $token = JWTAuth::claims(['chatIds' => [1]])->fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->get('api/chats/1/messages');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => Status::Success->value,
        ]);
    }

    /** @test */
    public function an_authenticated_user_cannot_get_messages()
    {
        $user = User::first();

        $customClaims = [
            'chatIds' => [1, 2, 3]
        ];

        $token = JWTAuth::claims($customClaims)->fromUser($user);


        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->get('api/chats/9/messages');

        $response->assertStatus(403);
    }

    /** @test */
    public function an_unauthenticated_user_cannot_get_messages()
    {
        $response = $this->get('api/chats/1/messages');

        $response->assertStatus(401);
    }
}
