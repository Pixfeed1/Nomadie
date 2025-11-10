<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Si l'utilisateur est un vendor, rediriger vers son dashboard
        if ($user->role === 'vendor' || $user->isVendor()) {
            return redirect()->route('vendor.dashboard.index');
        }

        // Si l'utilisateur est un admin, rediriger vers le dashboard admin
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard.index');
        }

        // Si c'est un client, laisser passer
        if ($user->role === 'customer' || $user->role === null) {
            return $next($request);
        }

        // Par défaut, rediriger vers la page d'accueil
        return redirect()->route('home');
    }
}