<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Article;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminWriterController extends Controller
{
    /**
     * Display list of writers by status
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $query = User::whereNotNull('writer_type');

        if ($status === 'all') {
            // Tous les rédacteurs
            $writers = $query->with(['articles' => function($q) {
                $q->latest()->limit(1);
            }])->orderBy('created_at', 'desc')->paginate(20);
        } else {
            // Filtrer par statut
            $writers = $query->where('writer_status', $status === 'pending' ? User::WRITER_STATUS_PENDING : $status)
                ->with(['articles' => function($q) {
                    $q->latest()->limit(1);
                }])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }

        $stats = [
            'pending' => User::whereNotNull('writer_type')->where('writer_status', User::WRITER_STATUS_PENDING)->count(),
            'validated' => User::whereNotNull('writer_type')->where('writer_status', User::WRITER_STATUS_VALIDATED)->count(),
            'rejected' => User::whereNotNull('writer_type')->where('writer_status', User::WRITER_STATUS_REJECTED)->count(),
            'suspended' => User::whereNotNull('writer_type')->where('writer_status', User::WRITER_STATUS_SUSPENDED)->count(),
            'total' => User::whereNotNull('writer_type')->count(),
        ];

        return view('admin.writers.index', compact('writers', 'status', 'stats'));
    }

    /**
     * Show writer details for validation
     */
    public function show($id)
    {
        $writer = User::with(['articles.latestSeoAnalysis', 'badges', 'bookings.trip'])
            ->findOrFail($id);

        // Si community writer, charger son article test
        $testArticle = null;
        if ($writer->isCommunityWriter()) {
            $testArticle = $writer->articles()
                ->with('latestSeoAnalysis')
                ->oldest()
                ->first();
        }

        // Si client-contributor, charger la réservation vérifiée
        $verifiedBooking = null;
        if ($writer->isClientContributor() && $writer->verified_booking_id) {
            $verifiedBooking = $writer->bookings()
                ->with('trip')
                ->where('id', $writer->verified_booking_id)
                ->first();
        }

        return view('admin.writers.show', compact('writer', 'testArticle', 'verifiedBooking'));
    }

    /**
     * Validate a writer
     */
    public function validate(Request $request, $id)
    {
        $writer = User::findOrFail($id);

        if (!$writer->isWriter()) {
            return redirect()->back()
                ->with('error', 'Cet utilisateur n\'est pas un rédacteur.');
        }

        // Valider le rédacteur
        $writer->validateWriter();

        // Attribution automatique de badges selon le type
        if ($writer->isClientContributor()) {
            // Badge "Voyageur Vérifié"
            $badge = Badge::where('code', 'voyageur_verifie')->first();
            if ($badge) {
                $writer->unlockBadge($badge);
            }
        }

        if ($writer->isPartner()) {
            // Badge "Partenaire Certifié"
            $badge = Badge::where('code', 'partenaire_certifie')->first();
            if ($badge) {
                $writer->unlockBadge($badge);
            }
        }

        if ($writer->isTeamMember()) {
            // Badge "Team Nomadie"
            $badge = Badge::where('code', 'team_nomadie')->first();
            if ($badge) {
                $writer->unlockBadge($badge);
            }
        }

        // TODO: Envoyer notification email au rédacteur

        Log::info('Writer validated by admin', [
            'admin_id' => auth()->id(),
            'writer_id' => $writer->id,
            'writer_type' => $writer->writer_type,
        ]);

        return redirect()->back()
            ->with('success', "Le rédacteur {$writer->name} a été validé avec succès !");
    }

    /**
     * Reject a writer
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:10|max:500'
        ], [
            'reason.required' => 'Vous devez fournir une raison pour le refus.',
            'reason.min' => 'La raison doit contenir au moins 10 caractères.',
        ]);

        $writer = User::findOrFail($id);

        if (!$writer->isWriter()) {
            return redirect()->back()
                ->with('error', 'Cet utilisateur n\'est pas un rédacteur.');
        }

        $writer->rejectWriter($request->reason);

        // TODO: Envoyer notification email au rédacteur avec la raison

        Log::info('Writer rejected by admin', [
            'admin_id' => auth()->id(),
            'writer_id' => $writer->id,
            'writer_type' => $writer->writer_type,
            'reason' => $request->reason,
        ]);

        return redirect()->back()
            ->with('success', "La candidature de {$writer->name} a été refusée.");
    }

    /**
     * Suspend a writer
     */
    public function suspend(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:10|max:500'
        ], [
            'reason.required' => 'Vous devez fournir une raison pour la suspension.',
            'reason.min' => 'La raison doit contenir au moins 10 caractères.',
        ]);

        $writer = User::findOrFail($id);

        if (!$writer->isWriter()) {
            return redirect()->back()
                ->with('error', 'Cet utilisateur n\'est pas un rédacteur.');
        }

        $writer->suspendWriter($request->reason);

        // TODO: Envoyer notification email au rédacteur

        Log::info('Writer suspended by admin', [
            'admin_id' => auth()->id(),
            'writer_id' => $writer->id,
            'writer_type' => $writer->writer_type,
            'reason' => $request->reason,
        ]);

        return redirect()->back()
            ->with('success', "Le compte rédacteur de {$writer->name} a été suspendu.");
    }

    /**
     * Restore a suspended/rejected writer
     */
    public function restore($id)
    {
        $writer = User::findOrFail($id);

        if (!$writer->isWriter()) {
            return redirect()->back()
                ->with('error', 'Cet utilisateur n\'est pas un rédacteur.');
        }

        $writer->update([
            'writer_status' => User::WRITER_STATUS_PENDING,
            'writer_notes' => null
        ]);

        Log::info('Writer restored by admin', [
            'admin_id' => auth()->id(),
            'writer_id' => $writer->id,
            'writer_type' => $writer->writer_type,
        ]);

        return redirect()->back()
            ->with('success', "Le compte de {$writer->name} a été restauré en statut 'En attente'.");
    }

    /**
     * Update writer notes
     */
    public function updateNotes(Request $request, $id)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000'
        ]);

        $writer = User::findOrFail($id);

        $writer->update([
            'writer_notes' => $request->notes
        ]);

        return redirect()->back()
            ->with('success', 'Notes mises à jour avec succès.');
    }
}
