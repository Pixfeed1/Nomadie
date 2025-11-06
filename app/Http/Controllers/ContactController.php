<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        
        // Ici, vous pourriez envoyer un email avec les données du formulaire
        // Mail::to('votre-email@example.com')->send(new ContactFormMail($validated));
        
        // Redirection avec un message de succès
        return back()->with('success', 'Merci pour votre message ! Nous vous répondrons dans les plus brefs délais.');
    }
}
