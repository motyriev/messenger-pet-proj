<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\GetMessagesRequest;
use App\Http\Requests\MessageStoreRequest;
use App\Jobs\MessageStore;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Motyriev\MyDTOLibrary\MessageStoreDTO;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends Controller
{
    public function getMessages(GetMessagesRequest $request): JsonResponse
    {
        try {
            $client = new Client([
                'timeout'  => 10.0,
                'base_uri' => config('app.urls.chat_api'),
            ]);

            $response = $client->get("chats/{$request->route('chatId')}/messages", [
                'query' => $request->validated(),
            ]);

            if ($response->getStatusCode() === 200) {
                return response()->json(
                    json_decode($response->getBody()->getContents(), true),
                    $response->getStatusCode()
                );
            }

            Log::error('Error fetching messages from external API', [
                'status' => $response->getStatusCode(),
                'body'   => $response->getBody()->getContents(),
            ]);

            return response()->json(['message' => 'Failed to fetch messages'], $response->getStatusCode());
        } catch (\Throwable $e) {
            Log::error('Error fetching messages', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Failed to fetch messages'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeMessage(MessageStoreRequest $request): JsonResponse
    {
        try {
            $dto = MessageStoreDTO::fromArray($request->validated());
            MessageStore::dispatch($dto)->onQueue('message_store_queue');
            return response()->json([], Response::HTTP_ACCEPTED);
        } catch (\Throwable $e) {
            Log::error('Error storing message', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Failed to store message'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}