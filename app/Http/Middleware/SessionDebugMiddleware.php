<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SessionDebugMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Log l'ID de session au début de la requête
        Log::info('SessionDebug - Request Start', [
            'session_id' => session()->getId(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'has_session_cookie' => $request->hasCookie(config('session.cookie')),
            'session_cookie_value' => $request->cookie(config('session.cookie')),
            'session_keys' => array_keys(session()->all()),
            'has_vendor_data' => session()->has('vendor_data')
        ]);

        // Continuer le traitement de la requête
        $response = $next($request);

        // Log l'ID de session à la fin de la requête
        Log::info('SessionDebug - Request End', [
            'session_id' => session()->getId(),
            'response_status' => $response->getStatusCode(),
            'session_keys' => array_keys(session()->all()),
            'has_vendor_data' => session()->has('vendor_data')
        ]);

        return $response;
    }
}