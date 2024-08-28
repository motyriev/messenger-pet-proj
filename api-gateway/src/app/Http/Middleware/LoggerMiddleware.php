<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LoggerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $traceId = $request->header('X-Trace-Id') ?? Str::uuid()->toString();

        Log::withContext(['traceId' => $traceId, 'service' => config('app.name')]);

        $user = Auth::user();
        if ($user) {
            Log::withContext(['userId' => $user->id]);
        }

        Log::withContext([
            'method'      => $request->method(),
            'requestBody' => $request->except('password'),
            'url'         => $request->url()
        ]);

        $request->headers->set('X-Trace-Id', $traceId);

        return $next($request);
    }
}
