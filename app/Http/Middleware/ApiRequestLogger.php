<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class ApiRequestLogger
{
    public function handle($request, Closure $next)
    {
        if ($request->is('api/*')) {
            // Log::info('GDM RIDER API Request', [
            //     'method' => $request->method(),
            //     'url' => $request->fullUrl(),
            //     'headers' => $request->headers->all(),
            //     'body' => $request->all(),
            //     'ip' => $request->ip(),
            // ]);
        }

        return $next($request);
    }
}
