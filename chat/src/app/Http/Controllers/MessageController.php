<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Http\Requests\GetMessagesRequest;
use App\Services\MessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class MessageController extends Controller
{
    public function __construct(private MessageService $messageService)
    {
    }

    public function index(GetMessagesRequest $request): JsonResponse
    {
        try {
            $messageDTOs = $this->messageService->getAllByChatId((int)$request->route('chatId'));
            return Response::json(['data' => $messageDTOs, 'status' => Status::Success->value],
                SymfonyResponse::HTTP_OK);
        } catch (\Throwable $t) {
            Log::error('Error fetching messages', [
                'exception' => $t,
                'chatId'    => $request->input('chatId'),
            ]);

            return Response::json([
                'status' => Status::Failed->value,
                'error'  => 'Failed to fetch messages'
            ], SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
