<?php
return [
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
    'webhook' => [
        'secret' => env('STRIPE_WEBHOOK_SECRET'),
        'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
    ],
    'plans' => [
        'essential' => [
            'name' => 'Abonnement Essentiel',
            'price_id' => 'price_1RQmvWFTR22qbY6TH0ol6tMv', // ID du prix pour l'abonnement Essentiel à 49€
            'amount' => 49,
        ],
        'pro' => [
            'name' => 'Abonnement Pro',
            'price_id' => 'price_1RQn2jFTR22qbY6TuoVwl5zd', // ID du prix pour l'abonnement Pro à 99€
            'amount' => 99,
        ],
    ],
];