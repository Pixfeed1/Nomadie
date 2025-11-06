<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class VendorRegistrationMiddleware
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
        Log::info('VendorRegistrationMiddleware - Start', [
            'path' => $request->path(),
            'method' => $request->method(),
            'session_id' => session()->getId(),
            'has_token' => $request->has('token'),
            'token' => $request->input('token'),
            'has_vendor_data_session' => session()->has('vendor_data')
        ]);

        // 1. Vérifier si un token est présent dans l'URL ou la session
        $token = $request->input('token') ?? session('vendor_token');
        
        if ($token) {
            // 2. Récupérer les données depuis le cache si disponibles
            $vendorData = Cache::get('vendor_registration_' . $token);
            
            if ($vendorData) {
                // 3. Synchroniser avec la session pour double protection
                session(['vendor_data' => $vendorData, 'vendor_token' => $token]);
                session()->save();
                
                Log::info('VendorRegistrationMiddleware - Data restored from cache', [
                    'token' => $token,
                    'has_vendor_data_now' => session()->has('vendor_data')
                ]);
            }
        }
        
        // 4. Continuer le traitement de la requête
        $response = $next($request);
        
        // 5. Si des données ont été modifiées en session, les synchroniser avec le cache
        if (session()->has('vendor_data') && session()->has('vendor_token')) {
            $updatedToken = session('vendor_token');
            $updatedData = session('vendor_data');
            
            // Stocker les données mises à jour dans le cache
            Cache::put('vendor_registration_' . $updatedToken, $updatedData, now()->addHour());
            
            Log::info('VendorRegistrationMiddleware - Data saved to cache', [
                'token' => $updatedToken,
                'has_vendor_data' => session()->has('vendor_data')
            ]);
        }
        
        return $response;
    }
}