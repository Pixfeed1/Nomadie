<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact');
    }
    
    public function store(Request $request)
    {
        // Validation du formulaire
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Envoyer l'email à l'adresse de contact configurée
        try {
            $contactEmail = config('mail.contact_address', config('mail.from.address'));
            Mail::to($contactEmail)->send(new ContactFormMail($validated));
        } catch (\Exception $e) {
            // Log l'erreur mais ne bloque pas l'utilisateur
            \Log::error('Erreur envoi email contact: ' . $e->getMessage());
        }

        // Redirection avec un message de succès
        return back()->with('success', 'Merci pour votre message ! Nous vous répondrons dans les plus brefs délais.');
    }
}
