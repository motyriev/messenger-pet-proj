<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AddFriendRequest;
use App\Http\Requests\ManageFriendRequest;
use App\Jobs\AddFriendRequest as AddFriendRequestJob;
use App\Jobs\ManageFriendRequest as ManageFriendRequestJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Motyriev\MyDTOLibrary\AddFriendRequestDTO;
use Motyriev\MyDTOLibrary\ManageFriendRequestDTO;
use Symfony\Component\HttpFoundation\Response;

class FriendController extends Controller
{
    public function addFriend(AddFriendRequest $request): JsonResponse
    {
        try {
            $dto = AddFriendRequestDTO::fromArray($request->validated());
            AddFriendRequestJob::dispatch($dto)->onQueue('add_friend_request_queue');
            return response()->json([], Response::HTTP_ACCEPTED);
        } catch (\Throwable $e) {
            Log::error('Error adding friend request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Failed to add friend'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function manageFriend(ManageFriendRequest $request): JsonResponse
    {
        try {
            $dto = ManageFriendRequestDTO::fromArray($request->validated());
            ManageFriendRequestJob::dispatch($dto)->onQueue('manage_friend_request_queue');
            return response()->json([], Response::HTTP_ACCEPTED);
        } catch (\Throwable $e) {
            Log::error('Error managing friend request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Failed to manage friend request'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}