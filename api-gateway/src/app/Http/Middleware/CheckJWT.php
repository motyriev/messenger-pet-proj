<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class CheckJWT
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['message' => 'User not found'], Response::HTTP_UNAUTHORIZED);
            }
        } catch (TokenExpiredException $e) {
            return response()->json([
                'message' => 'Token Expired!',
            ], 401);

        } catch (TokenInvalidException|JWTException $e) {
            return response()->json([
                'message' => 'Not Authorized!',
            ], 401);
        } catch (\Throwable $t){
            Log::error("Exception occurred in " . __METHOD__, [
                'message' => $t->getMessage(),
                'trace'   => $t->getTraceAsString()
            ]);
        }

        return $next($request);
    }
}