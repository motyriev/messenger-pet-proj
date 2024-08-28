<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckChatAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $userChatIds = Auth::payload()->get('chatIds');

            if (!$request->route('chatId')
                || empty($userChatIds)
                || !is_array($userChatIds)
                || !in_array($request->route('chatId'), $userChatIds)) {
                return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
            }
        } catch (\Throwable $e) {
            Log::error("Exception occurred in " . __METHOD__, [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }


        return $next($request);
    }
}
