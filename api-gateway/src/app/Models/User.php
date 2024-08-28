<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        $client = new Client([
            'timeout' => 10.0,
        ]);

        try {
            $response = $client->get(config('app.urls.chat_api') . "users/{$this->id}/chats");

            $chats = json_decode($response->getBody()->getContents(), true)['data'];
            $ids = array_column($chats, 'id');
            Log::info(__METHOD__, ['chatIds' => $ids]);

            return ['chatIds' => $ids];
        } catch (RequestException $e) {
            Log::warning("Exception occurred in " . __METHOD__, ['message' => $e->getMessage()]);
            return ['chatIds' => []];
        }
    }
}
