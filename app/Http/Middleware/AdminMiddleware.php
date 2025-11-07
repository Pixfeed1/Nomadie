<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     * Vérifie que l'utilisateur connecté est un administrateur
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifier que l'utilisateur est connecté
        if (!Auth::check()) {
            Log::info('AdminMiddleware: User not authenticated', [
                'path' => $request->path(),
                'ip' => $request->ip()
            ]);

            return redirect()->route('login')
                ->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        $user = Auth::user();

        // Vérifier que l'utilisateur est un administrateur
        if (!$user->isAdmin()) {
            Log::warning('AdminMiddleware: User is not admin', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'path' => $request->path()
            ]);

            return redirect()->route('home')
                ->with('error', 'Vous n\'avez pas les droits d\'accès à cette section.');
        }

        Log::debug('AdminMiddleware: Access granted', [
            'user_id' => $user->id,
            'email' => $user->email,
            'path' => $request->path()
        ]);

        return $next($request);
    }
}
