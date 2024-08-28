<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function a_user_can_register()
    {
        $response = $this->postJson('/api/auth/register', [
            'email'    => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function a_user_cannot_register_with_invalid_data()
    {
        $response = $this->postJson('/api/auth/register', [
            'email' => 'invalid-email',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    /** @test */
    public function a_user_can_login()
    {
        $user = User::factory()->create([
            'email'    => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'accessToken', 'tokenType', 'expiresIn', 'user'
        ]);
    }

    /** @test */
    public function a_user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email'    => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJson(['message' => 'Invalid credentials!']);
    }

    /** @test */
    public function a_user_can_logout()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/auth/logout');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(['message' => 'Successfully logged out']);
    }

    /** @test */
    public function a_user_can_refresh_token()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/auth/refresh');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'accessToken', 'tokenType', 'expiresIn', 'user'
        ]);
    }
}

