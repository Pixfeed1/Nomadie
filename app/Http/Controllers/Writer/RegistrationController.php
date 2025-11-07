<?php

namespace App\Http\Controllers\Writer;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegistrationController extends Controller
{
    /**
     * Show writer registration form
     */
    public function showForm()
    {
        $user = Auth::user();

        // Si déjà rédacteur, rediriger selon le statut
        if ($user->isWriter()) {
            if ($user->canWriteArticles()) {
                return redirect()->route('writer.dashboard')
                    ->with('info', 'Vous êtes déjà un rédacteur validé.');
            }

            return redirect()->route('writer.pending');
        }

        // Vérifier si l'utilisateur a des réservations confirmées (pour client-contributor)
        $hasBookings = Booking::where('user_id', $user->id)
            ->whereIn('status', ['confirmed', 'completed'])
            ->exists();

        return view('writer.register', compact('hasBookings'));
    }

    /**
     * Process writer registration
     */
    public function register(Request $request)
    {
        $user = Auth::user();

        // Vérifier si déjà rédacteur
        if ($user->isWriter()) {
            return redirect()->route('writer.pending')
                ->with('info', 'Vous avez déjà soumis une candidature.');
        }

        // Validation selon le type de rédacteur
        $rules = [
            'writer_type' => 'required|in:community,client_contributor,partner',
            'motivation' => 'required|string|min:100|max:1000',
        ];

        // Règles spécifiques selon le type
        if ($request->writer_type === 'client_contributor') {
            $rules['verified_booking_id'] = 'required|exists:bookings,id';
        }

        if ($request->writer_type === 'partner') {
            $rules['partner_offer_url'] = 'required|url|max:255';
            $rules['company_name'] = 'required|string|max:255';
        }

        $validator = Validator::make($request->all(), $rules, [
            'writer_type.required' => 'Vous devez choisir un type de rédacteur.',
            'motivation.required' => 'Veuillez expliquer votre motivation (minimum 100 caractères).',
            'motivation.min' => 'Votre motivation doit contenir au moins 100 caractères.',
            'verified_booking_id.required' => 'Vous devez sélectionner une réservation vérifiée.',
            'verified_booking_id.exists' => 'Cette réservation n\'existe pas.',
            'partner_offer_url.required' => 'L\'URL de votre offre commerciale est obligatoire.',
            'partner_offer_url.url' => 'L\'URL doit être valide.',
            'company_name.required' => 'Le nom de votre entreprise est obligatoire.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifications spécifiques
        if ($request->writer_type === 'client_contributor') {
            // Vérifier que la réservation appartient bien à l'utilisateur
            $booking = Booking::where('id', $request->verified_booking_id)
                ->where('user_id', $user->id)
                ->whereIn('status', ['confirmed', 'completed'])
                ->first();

            if (!$booking) {
                return redirect()->back()
                    ->withErrors(['verified_booking_id' => 'Cette réservation ne vous appartient pas ou n\'est pas confirmée.'])
                    ->withInput();
            }
        }

        // Créer le profil rédacteur
        $writerData = [
            'writer_type' => $request->writer_type,
            'writer_status' => User::WRITER_STATUS_PENDING,
            'writer_notes' => 'Motivation : ' . $request->motivation,
        ];

        // Données spécifiques selon le type
        if ($request->writer_type === 'client_contributor') {
            $writerData['verified_booking_id'] = $request->verified_booking_id;
        }

        if ($request->writer_type === 'partner') {
            $writerData['partner_offer_url'] = $request->partner_offer_url;
            $writerData['writer_notes'] .= "\n\nEntreprise : " . $request->company_name;
        }

        $user->update($writerData);

        Log::info('Writer registration submitted', [
            'user_id' => $user->id,
            'email' => $user->email,
            'writer_type' => $request->writer_type,
        ]);

        // Message personnalisé selon le type
        $message = match($request->writer_type) {
            'community' => 'Votre candidature a été soumise ! Vous pouvez maintenant écrire votre article test. Il sera examiné par notre équipe.',
            'client_contributor' => 'Votre candidature a été soumise ! Notre équipe va vérifier votre réservation et vous contactera sous 48h.',
            'partner' => 'Votre candidature partenaire a été soumise ! Notre équipe va vérifier votre offre commerciale et vous contactera sous 48h.',
            default => 'Votre candidature a été soumise avec succès !',
        };

        return redirect()->route('writer.pending')
            ->with('success', $message);
    }

    /**
     * Show pending validation page
     */
    public function pending()
    {
        $user = Auth::user();

        // Si pas de writer_type, rediriger vers inscription
        if (!$user->isWriter()) {
            return redirect()->route('writer.register')
                ->with('info', 'Veuillez d\'abord vous inscrire en tant que rédacteur.');
        }

        // Si déjà validé, rediriger vers dashboard
        if ($user->isValidatedWriter() || $user->isTeamMember()) {
            return redirect()->route('writer.dashboard')
                ->with('success', 'Votre compte rédacteur est validé !');
        }

        // Si rejeté
        if ($user->isRejectedWriter()) {
            return view('writer.rejected', [
                'reason' => $user->writer_notes
            ]);
        }

        // Si suspendu
        if ($user->isSuspendedWriter()) {
            return view('writer.suspended', [
                'reason' => $user->writer_notes
            ]);
        }

        // Statut pending
        $canSubmitTestArticle = $user->isCommunityWriter() && $user->articles()->count() === 0;
        $hasSubmittedTestArticle = $user->isCommunityWriter() && $user->articles()->count() > 0;

        return view('writer.pending', compact('user', 'canSubmitTestArticle', 'hasSubmittedTestArticle'));
    }
}
