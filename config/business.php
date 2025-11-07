<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Politique de Remboursement
    |--------------------------------------------------------------------------
    |
    | Configuration de la politique de remboursement basée sur le nombre
    | de jours avant le début du voyage.
    |
    */
    'refund_policy' => [
        // Délai en jours => pourcentage de remboursement
        30 => 1.0,    // 100% de remboursement si annulation 30+ jours avant
        14 => 0.5,    // 50% de remboursement si annulation 14-29 jours avant
        7  => 0.25,   // 25% de remboursement si annulation 7-13 jours avant
        0  => 0.0,    // Pas de remboursement si annulation < 7 jours avant
    ],

    /*
    |--------------------------------------------------------------------------
    | Limites de Validation
    |--------------------------------------------------------------------------
    */
    'validation' => [
        'max_travelers' => 100,
        'max_trips_per_vendor' => 9999,
        'max_destinations_per_vendor' => 50,
        'max_languages_per_trip' => 5,
        'max_reason_length' => 500,
        'max_notes_length' => 500,
        'max_rejection_reason_length' => 500,
    ],

    /*
    |--------------------------------------------------------------------------
    | Ratings
    |--------------------------------------------------------------------------
    */
    'rating' => [
        'min' => 1,
        'max' => 5,
        'default' => 4.7, // Note par défaut si aucun avis
    ],
];
