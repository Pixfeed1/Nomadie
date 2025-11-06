<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Affiche le formulaire pour créer un nouvel avis
     */
    public function create($tripId)
    {
        $trip = Trip::findOrFail($tripId);
        
        // Vérifier si l'utilisateur a déjà laissé un avis pour ce voyage
        $existingReview = Review::where('user_id', auth()->id())
            ->where('trip_id', $tripId)
            ->first();
            
        if ($existingReview) {
            return redirect()->route('trips.show', $tripId)
                ->with('warning', 'Vous avez déjà laissé un avis pour ce voyage.');
        }
        
        return view('trips.reviews.create', compact('trip'));
    }
    
    /**
     * Enregistre un nouvel avis
     */
    public function store(Request $request, $tripId)
    {
        $trip = Trip::findOrFail($tripId);
        
        // Valider les données du formulaire
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string|min:10|max:1000',
            'travel_date' => 'required|date|before_or_equal:today',
        ]);
        
        // Créer le nouvel avis
        $review = new Review();
        $review->user_id = auth()->id();
        $review->trip_id = $tripId;
        $review->rating = $validated['rating'];
        $review->content = $validated['content'];
        $review->travel_date = $validated['travel_date'];
        $review->user_name = auth()->user()->name;
        $review->save();
        
        // Mettre à jour la note moyenne du voyage
        $this->updateTripRating($tripId);
        
        return redirect()->route('trips.show', $tripId)
            ->with('success', 'Votre avis a été ajouté avec succès. Merci de partager votre expérience !');
    }
    
    /**
     * Met à jour la note moyenne du voyage
     */
    private function updateTripRating($tripId)
    {
        $trip = Trip::findOrFail($tripId);
        
        // Calculer la nouvelle moyenne des notes
        $avgRating = Review::where('trip_id', $tripId)->avg('rating');
        $reviewsCount = Review::where('trip_id', $tripId)->count();
        
        // Mettre à jour le voyage
        $trip->rating = round($avgRating, 1);
        $trip->reviews_count = $reviewsCount;
        $trip->save();
    }
}