<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SessionPersistenceMiddleware
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
        // Force l'enregistrement de la session avant le traitement
        if ($request->hasSession()) {
            $request->session()->save();
        }
        
        Log::info('SessionPersistence - Before processing', [
            'session_id' => session()->getId(),
            'path' => $request->path(),
            'has_vendor_data' => session()->has('vendor_data')
        ]);
        
        $response = $next($request);
        
        // Force l'enregistrement de la session après le traitement
        if ($request->hasSession()) {
            $request->session()->save();
        }
        
        Log::info('SessionPersistence - After processing', [
            'session_id' => session()->getId(),
            'response_status' => $response->getStatusCode(),
            'has_vendor_data' => session()->has('vendor_data')
        ]);
        
        return $response;
    }
}