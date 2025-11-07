<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WriterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Vérifie que l'utilisateur est un rédacteur validé ou membre de l'équipe.
     *
     * Les 4 types de rédacteurs :
     * - community : Doit être validé après article test
     * - client_contributor : Doit être validé (voyage vérifié)
     * - partner : Doit être validé (offre commerciale vérifiée)
     * - team : Accès immédiat (pas de validation requise)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifier que l'utilisateur est connecté
        if (!Auth::check()) {
            Log::info('WriterMiddleware: User not authenticated', [
                'path' => $request->path(),
                'ip' => $request->ip()
            ]);

            return redirect()->route('login')
                ->with('error', 'Vous devez être connecté pour accéder à l\'espace rédacteur.');
        }

        $user = Auth::user();

        // Vérifier que l'utilisateur a un type de rédacteur défini
        if (!$user->isWriter()) {
            Log::info('WriterMiddleware: User is not a writer', [
                'user_id' => $user->id,
                'email' => $user->email,
                'path' => $request->path()
            ]);

            return redirect()->route('writer.register')
                ->with('info', 'Vous devez d\'abord vous inscrire en tant que rédacteur.');
        }

        // Les membres de l'équipe ont toujours accès
        if ($user->isTeamMember()) {
            Log::debug('WriterMiddleware: Team member access granted', [
                'user_id' => $user->id,
                'email' => $user->email,
                'path' => $request->path()
            ]);

            return $next($request);
        }

        // Vérifier le statut du rédacteur
        switch ($user->writer_status) {
            case $user::WRITER_STATUS_PENDING:
                Log::info('WriterMiddleware: Writer pending validation', [
                    'user_id' => $user->id,
                    'writer_type' => $user->writer_type,
                    'email' => $user->email
                ]);

                // Permettre l'accès au dashboard pour soumettre l'article test
                if ($request->is('writer/dashboard') || $request->is('writer/articles/*')) {
                    return $next($request);
                }

                return redirect()->route('writer.pending')
                    ->with('warning', 'Votre compte rédacteur est en attente de validation.');

            case $user::WRITER_STATUS_REJECTED:
                Log::warning('WriterMiddleware: Writer account rejected', [
                    'user_id' => $user->id,
                    'writer_type' => $user->writer_type,
                    'email' => $user->email,
                    'reason' => $user->writer_notes
                ]);

                return redirect()->route('home')
                    ->with('error', 'Votre candidature en tant que rédacteur a été refusée. Raison : ' . ($user->writer_notes ?? 'Non spécifiée'));

            case $user::WRITER_STATUS_SUSPENDED:
                Log::warning('WriterMiddleware: Writer account suspended', [
                    'user_id' => $user->id,
                    'writer_type' => $user->writer_type,
                    'email' => $user->email,
                    'reason' => $user->writer_notes
                ]);

                return redirect()->route('home')
                    ->with('error', 'Votre compte rédacteur a été suspendu. Raison : ' . ($user->writer_notes ?? 'Non spécifiée'));

            case $user::WRITER_STATUS_VALIDATED:
                // Tout va bien, continuer
                break;

            default:
                Log::error('WriterMiddleware: Unknown writer status', [
                    'user_id' => $user->id,
                    'writer_status' => $user->writer_status,
                    'writer_type' => $user->writer_type,
                    'email' => $user->email
                ]);

                return redirect()->route('home')
                    ->with('error', 'Statut de rédacteur invalide. Contactez notre support.');
        }

        Log::debug('WriterMiddleware: Access granted', [
            'user_id' => $user->id,
            'writer_type' => $user->writer_type,
            'writer_status' => $user->writer_status,
            'path' => $request->path()
        ]);

        return $next($request);
    }
}
