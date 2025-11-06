<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        // Vérifier si l'utilisateur existe
        $user = User::where('email', $request->email)->first();
        
        if ($user) {
            $user->update(['newsletter' => true]);
        } else {
            // Créer juste une entrée newsletter ou stocker dans une table dédiée
            // Pour l'instant on peut juste stocker dans la session
            session()->flash('success', 'Merci pour votre inscription à la newsletter !');
        }

        return back()->with('success', 'Inscription à la newsletter confirmée !');
    }
}