<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Http\Requests\ChatInitializeRequest;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Motyriev\MyDTOLibrary\UserPairDTO;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(private ChatService $service)
    {
    }

    public function getOrCreate(ChatInitializeRequest $request): JsonResponse
    {
        try {
            $userPairs = array_map(
                fn($pair) => UserPairDTO::fromArray($pair),
                $request->validated('userPairs')
            );
            $chats = $this->service->getOrCreate($userPairs);

            return Response::json(['data' => $chats, 'status' => Status::Success->value], SymfonyResponse::HTTP_OK);
        } catch (\Throwable $t) {
            Log::error('Error in ' . __METHOD__ . ': ' . $t->getMessage(), [
                'trace'     => $t->getTraceAsString(),
                'userPairs' => $request->validated('userPairs'),
            ]);
            return Response::json(['data' => [], 'status' => Status::Failed->value, 'error' => $t->getMessage()],
                SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getAllByUserId(Request $request, $userId): JsonResponse
    {
        try {
            $chats = $this->service->getAllByUserId($userId);
            return Response::json(['data' => $chats, 'status' => Status::Success->value], SymfonyResponse::HTTP_OK);
        } catch (\Throwable $t) {
            Log::error('Error in ' . __METHOD__ . ': ' . $t->getMessage(), [
                'trace'  => $t->getTraceAsString(),
                'userId' => $userId,
            ]);
            return Response::json(['data' => [], 'status' => Status::Failed->value, 'error' => $t->getMessage()],
                SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
