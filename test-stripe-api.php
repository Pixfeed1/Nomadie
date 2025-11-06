<?php
require 'vendor/autoload.php';

// La clé directement dans le script
\Stripe\Stripe::setApiKey('sk_test_51RQll2FTR22qbY6T3t514x0k8gcSPnkheA001aGXJuwKca3gZmkk5AS9UeNjMH01bwc4ZSoNIhap4JD5bMoV0gDq06krs4o53w');

try {
    // Test 1: Récupérer le compte
    $account = \Stripe\Account::retrieve();
    echo "✅ Compte Stripe: " . $account->email . "\n\n";
    
    // Test 2: Vérifier le prix
    $price = \Stripe\Price::retrieve('price_1RQmvWFTR22qbY6TH0ol6tMv');
    echo "✅ Prix trouvé: " . $price->unit_amount/100 . " " . $price->currency . "\n";
    echo "Type: " . $price->type . "\n";
    echo "Recurring: " . ($price->recurring ? "Oui" : "Non") . "\n\n";
    
    // Test 3: Créer une session de paiement
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price' => 'price_1RQmvWFTR22qbY6TH0ol6tMv',
            'quantity' => 1,
        ]],
        'mode' => 'subscription',
        'success_url' => 'https://test2.jewelme.fr/success',
        'cancel_url' => 'https://test2.jewelme.fr/cancel',
    ]);
    
    echo "✅ Session créée: " . $session->id . "\n";
    echo "URL: " . $session->url . "\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
