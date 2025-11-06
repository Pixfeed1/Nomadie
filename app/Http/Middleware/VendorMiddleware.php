<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VendorMiddleware
{
    /**
     * Handle an incoming request.
     * Vérifie que l'utilisateur connecté est un vendeur actif
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifier que l'utilisateur est connecté
        if (!Auth::check()) {
            Log::info('VendorMiddleware: User not authenticated', [
                'path' => $request->path(),
                'ip' => $request->ip()
            ]);
            
            return redirect()->route('login')
                ->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        $user = Auth::user();
        
        // Vérifier que l'utilisateur a un compte vendeur
        if (!$user->vendor) {
            Log::warning('VendorMiddleware: User has no vendor account', [
                'user_id' => $user->id,
                'email' => $user->email,
                'path' => $request->path()
            ]);
            
            return redirect()->route('vendor.register')
                ->with('error', 'Vous devez d\'abord vous inscrire en tant qu\'organisateur de voyages.');
        }

        $vendor = $user->vendor;

        // Vérifier le statut du vendeur selon votre système admin
        switch ($vendor->status) {
            case 'pending':
                Log::info('VendorMiddleware: Vendor account pending approval', [
                    'vendor_id' => $vendor->id,
                    'email' => $vendor->email
                ]);
                
                return redirect()->route('vendor.pending')
                    ->with('warning', 'Votre compte organisateur est en attente de validation par notre équipe.');

            case 'rejected':
                Log::warning('VendorMiddleware: Vendor account rejected', [
                    'vendor_id' => $vendor->id,
                    'email' => $vendor->email
                ]);
                
                return redirect()->route('home')
                    ->with('error', 'Votre demande d\'inscription a été rejetée. Contactez notre support pour plus d\'informations.');

            case 'suspended':
                Log::warning('VendorMiddleware: Vendor account suspended', [
                    'vendor_id' => $vendor->id,
                    'email' => $vendor->email
                ]);
                
                return redirect()->route('vendor.suspended')
                    ->with('error', 'Votre compte organisateur a été suspendu. Contactez notre support.');

            case 'active':
                // Tout va bien, continuer
                break;

            default:
                Log::error('VendorMiddleware: Unknown vendor status', [
                    'vendor_id' => $vendor->id,
                    'status' => $vendor->status,
                    'email' => $vendor->email
                ]);
                
                return redirect()->route('home')
                    ->with('error', 'Statut de compte invalide. Contactez notre support.');
        }

        // Vérifier que l'email du vendeur est vérifié (si vous utilisez cette fonctionnalité)
        if (!$vendor->email_verified_at) {
            Log::info('VendorMiddleware: Vendor email not verified', [
                'vendor_id' => $vendor->id,
                'email' => $vendor->email
            ]);
            
            return redirect()->route('vendor.verify-email')
                ->with('warning', 'Veuillez vérifier votre adresse email avant d\'accéder à votre espace organisateur.');
        }

        // Ajouter le vendeur dans la requête pour un accès facile dans les controllers
        $request->merge(['current_vendor' => $vendor]);

        Log::debug('VendorMiddleware: Access granted', [
            'vendor_id' => $vendor->id,
            'company_name' => $vendor->company_name,
            'subscription_plan' => $vendor->subscription_plan,
            'path' => $request->path()
        ]);

        return $next($request);
    }
}