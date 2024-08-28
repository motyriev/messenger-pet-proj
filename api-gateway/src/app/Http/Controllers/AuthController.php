<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'email' => $request->email,
            'name' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Log::info('User registered', ['userId' => $user->id, 'email' => $user->email]);

        return response()->json(new UserResource($user), Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (!$token = Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials!'], Response::HTTP_UNAUTHORIZED);
        }

        Log::info('User logged in', ['userId' => Auth::id(), 'email' => $request->email]);

        return $this->respondWithToken($token);
    }

    public function logout(): JsonResponse
    {
        $userId = Auth::id();

        Auth::logout();

        Log::info('User logged out', ['userId' => $userId]);

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh(): JsonResponse
    {
        $user = Auth::user();

        // payload data update
        $newToken = Auth::login($user);

        Log::info('Token refreshed', [
            'userId' => $user->id,
            'userChatIds' => Auth::payload()->get('chatIds'),
        ]);

        return $this->respondWithToken($newToken);
    }

    protected function respondWithToken($token): JsonResponse
    {
        return response()->json([
            'accessToken' => $token,
            'tokenType' => 'bearer',
            'expiresIn' => Auth::factory()->getTTL() * 60,
            'user' => UserTransformer::transform(Auth::user())
        ]);
    }
}

