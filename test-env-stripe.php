<?php
require 'vendor/autoload.php';

// Récupérer depuis la variable d'environnement système
$key = getenv('STRIPE_API_KEY');
if (!$key) {
    die("Clé non trouvée dans l'environnement\n");
}

\Stripe\Stripe::setApiKey($key);

try {
    $account = \Stripe\Account::retrieve();
    echo "✅ Connexion réussie: " . $account->email . "\n";
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
