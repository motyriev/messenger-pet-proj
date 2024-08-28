<?php

namespace App\Jobs\Middleware;

use Illuminate\Support\Facades\Log;

class LoggerMiddleware
{
    public function __construct(private string $traceId)
    {
    }

    public function handle($job, $next)
    {
        Log::withContext([
            'service' => config('app.name'),
            'traceId' => $this->traceId
        ]);

        $next($job);
    }
}