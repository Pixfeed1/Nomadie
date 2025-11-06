<?php
// public/test_stripe_flow.php

// Initialiser la session
session_start();

// Récupérer les configurations de l'application Laravel
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

// Accéder aux services Laravel
$cache = app('cache');
$config = app('config');
$logger = app('log');

// Activer l'affichage des erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ================ FONCTIONS UTILITAIRES ================

function logMessage($message, $data = []) {
    global $logger;
    echo "<div style='margin: 10px 0; padding: 10px; background: #f5f5f5; border-left: 4px solid #3498db;'>";
    echo "<b>$message</b>";
    if (!empty($data)) {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
    echo "</div>";
    $logger->info("TEST_STRIPE: " . $message, $data);
}

function getRequestInfo() {
    return [
        'url' => $_SERVER['REQUEST_URI'],
        'method' => $_SERVER['REQUEST_METHOD'],
        'cookie_name' => config('session.cookie'),
        'has_laravel_cookie' => isset($_COOKIE[config('session.cookie')]),
        'laravel_cookie_value' => $_COOKIE[config('session.cookie')] ?? 'Non définie',
        'php_session_id' => session_id(),
        'all_cookies' => $_COOKIE,
    ];
}

// ================ TRAITEMENT DU FLUX ================

$action = $_GET['action'] ?? 'start';
$token = $_GET['token'] ?? ($_POST['token'] ?? ($_SESSION['token'] ?? null));
$payment_successful = isset($_GET['success']) && $_GET['success'] == 'true';

echo "<html><head><title>Test du flux Stripe</title>";
echo "<style>
    body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; max-width: 1100px; margin: 0 auto; }
    h1 { border-bottom: 2px solid #3498db; padding-bottom: 10px; }
    .test-section { background: #f9f9f9; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
    .success { background-color: #d4edda; border-left: 4px solid #28a745; padding: 10px; }
    .warning { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 10px; }
    .error { background-color: #f8d7da; border-left: 4px solid #dc3545; padding: 10px; }
    .btn { padding: 10px 15px; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
    .btn-small { padding: 5px 10px; font-size: 0.9em; }
    table { width: 100%; border-collapse: collapse; }
    table th, table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    table th { background-color: #f2f2f2; }
    pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
</style>";
echo "</head><body>";

echo "<h1>Test du flux de paiement Stripe</h1>";
echo "<p>Cette page simule le flux complet d'une redirection Stripe, y compris la perte potentielle de cookies et de contexte.</p>";

// Afficher les informations de requête
echo "<div class='test-section'>";
echo "<h2>Informations de la requête</h2>";
$requestInfo = getRequestInfo();
echo "<table>";
foreach ($requestInfo as $key => $value) {
    echo "<tr><th>$key</th><td>";
    if (is_array($value)) {
        echo "<pre>" . print_r($value, true) . "</pre>";
    } else {
        echo htmlspecialchars($value);
    }
    echo "</td></tr>";
}
echo "</table>";
echo "</div>";

// Traitement selon l'action
switch ($action) {
    case 'start':
        echo "<div class='test-section'>";
        echo "<h2>Démarrer le test</h2>";
        
        // Réinitialiser les données
        $_SESSION = [];
        
        // Générer un nouveau token
        $token = md5(uniqid() . time());
        $_SESSION['token'] = $token;
        
        // Stocker des données de test
        $testData = [
            'company_name' => 'Test Company',
            'email' => 'test@example.com',
            'subscription' => 'essential',
            'timestamp' => time()
        ];
        $_SESSION['vendor_data'] = $testData;
        
        logMessage("Session initialisée", [
            'token' => $token,
            'vendor_data' => $testData,
            'session_id' => session_id()
        ]);
        
        // Stocker dans le cache Laravel
        $cache->put('vendor_registration_' . $token, $testData, now()->addHour());
        
        logMessage("Données stockées dans le cache", [
            'token' => $token,
            'cache_key' => 'vendor_registration_' . $token
        ]);
        
        echo "<p>Nous avons créé un nouveau test avec:</p>";
        echo "<ul>";
        echo "<li>Token: <strong>$token</strong></li>";
        echo "<li>ID de session PHP: <strong>" . session_id() . "</strong></li>";
        echo "<li>Cookie de session Laravel: <strong>" . ($requestInfo['has_laravel_cookie'] ? $requestInfo['laravel_cookie_value'] : 'Non défini') . "</strong></li>";
        echo "</ul>";
        
        echo "<p>Choisissez une méthode de redirection pour simuler le paiement Stripe:</p>";
        
        // Option 1: Redirection simple
        echo "<a href='?action=redirect_stripe_simple&token=$token' class='btn'>1. Redirection simple (simulation basique)</a><br><br>";
        
        // Option 2: Redirection avec nouvelle fenêtre (simule mieux Stripe)
        echo "<button onclick='window.open(\"?action=stripe_simulator&token=$token\", \"_blank\");' class='btn'>2. Redirection via nouvelle fenêtre (simule mieux Stripe)</button><br><br>";
        
        // Option 3: Redirection qui supprime les cookies actuels
        echo "<a href='?action=redirect_stripe_clear_cookies&token=$token' class='btn'>3. Redirection avec suppression de cookies (test extrême)</a>";
        
        echo "</div>";
        break;
        
    case 'redirect_stripe_simple':
        echo "<div class='test-section'>";
        echo "<h2>Redirection vers Stripe (simulation)</h2>";
        
        logMessage("Redirection simple vers Stripe", [
            'token' => $token,
            'session_id' => session_id(),
            'session_token' => $_SESSION['token'] ?? 'Non défini',
            'has_vendor_data' => isset($_SESSION['vendor_data']),
            'session_data' => $_SESSION
        ]);
        
        // Simuler la redirection Stripe avec retour
        echo "<p>Simulation de la page de paiement Stripe...</p>";
        
        // Options de paiement
        echo "<div style='margin: 20px; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>";
        echo "<h3>Simulateur de paiement Stripe</h3>";
        echo "<p>Choisissez une option:</p>";
        echo "<a href='?action=payment_return&token=$token&success=true' class='btn' style='background: #5cb85c;'>Paiement réussi</a> &nbsp; ";
        echo "<a href='?action=payment_return&token=$token&success=false' class='btn' style='background: #d9534f;'>Paiement annulé</a>";
        echo "</div>";
        
        echo "</div>";
        break;
        
    case 'redirect_stripe_clear_cookies':
        echo "<div class='test-section'>";
        echo "<h2>Redirection vers Stripe (avec suppression des cookies)</h2>";
        
        // Sauvegarder le token pour le restaurer après
        $savedToken = $token;
        
        // Stocker l'état initial des cookies
        $initialCookies = $_COOKIE;
        
        logMessage("Redirection avec suppression des cookies", [
            'token' => $token,
            'session_id' => session_id(),
            'session_token' => $_SESSION['token'] ?? 'Non défini',
            'has_vendor_data' => isset($_SESSION['vendor_data']),
            'initial_cookies' => $initialCookies
        ]);
        
        // Script pour supprimer tous les cookies puis rediriger
        echo "<script>
            // Fonction pour supprimer tous les cookies
            function deleteAllCookies() {
                const cookies = document.cookie.split(';');
                
                for (let i = 0; i < cookies.length; i++) {
                    const cookie = cookies[i];
                    const eqPos = cookie.indexOf('=');
                    const name = eqPos > -1 ? cookie.substr(0, eqPos).trim() : cookie.trim();
                    document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/';
                    document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;domain=' + window.location.hostname;
                }
                
                console.log('Tous les cookies ont été supprimés');
            }
            
            // Afficher message
            document.write('<p>Suppression de tous les cookies pour simuler un scénario extrême...</p>');
            
            // Supprimer les cookies et rediriger
            setTimeout(function() {
                deleteAllCookies();
                window.location.href = '?action=payment_return&token=$savedToken&success=true&cleared_cookies=true';
            }, 2000);
        </script>";
        
        echo "<p>Suppression de tous les cookies et redirection vers la page de retour...</p>";
        echo "</div>";
        break;
        
    case 'stripe_simulator':
        echo "<div class='test-section'>";
        echo "<h2>Simulateur de paiement Stripe</h2>";
        
        logMessage("Ouverture du simulateur Stripe", [
            'token' => $token,
            'session_id' => session_id()
        ]);
        
        echo "<div style='text-align: center; padding: 40px; background: #f6f9fc; border-radius: 10px;'>";
        echo "<h3>Stripe Checkout</h3>";
        echo "<p>Cette fenêtre simule la page de paiement Stripe.</p>";
        echo "<p>Quand l'utilisateur complète le paiement chez Stripe, il est redirigé vers votre site avec une URL de retour.</p>";
        
        echo "<div style='margin: 30px 0;'>";
        echo "<a href='javascript:void(0)' onclick='simulatePayment(true)' class='btn' style='background: #5cb85c;'>Simuler un paiement réussi</a>";
        echo "<br><br>";
        echo "<a href='javascript:void(0)' onclick='simulatePayment(false)' class='btn' style='background: #d9534f;'>Simuler un paiement annulé</a>";
        echo "</div>";
        
        echo "<script>
            function simulatePayment(success) {
                // Fermer cette fenêtre et ouvrir la fenêtre de retour dans la fenêtre principale
                window.opener.location.href = '?action=payment_return&token=$token&success=' + (success ? 'true' : 'false') + '&new_window=true';
                window.close();
            }
        </script>";
        echo "</div>";
        break;
        
    case 'payment_return':
        echo "<div class='test-section'>";
        echo "<h2>Retour de paiement " . ($payment_successful ? "réussi" : "annulé") . "</h2>";
        
        $clearedCookies = isset($_GET['cleared_cookies']) && $_GET['cleared_cookies'] === 'true';
        $fromNewWindow = isset($_GET['new_window']) && $_GET['new_window'] === 'true';
        
        logMessage("Retour du paiement Stripe", [
            'token' => $token,
            'payment_successful' => $payment_successful,
            'cleared_cookies' => $clearedCookies,
            'from_new_window' => $fromNewWindow,
            'session_id' => session_id(),
            'has_session_token' => isset($_SESSION['token']),
            'session_token' => $_SESSION['token'] ?? 'Non défini',
            'has_vendor_data_in_session' => isset($_SESSION['vendor_data']),
            'all_session_keys' => array_keys($_SESSION)
        ]);
        
        // Vérifier les données en session
        $sessionHasVendorData = isset($_SESSION['vendor_data']);
        
        // Vérifier les données en cache
        $cacheHasVendorData = false;
        $vendorDataFromCache = null;
        
        if ($token) {
            $vendorDataFromCache = $cache->get('vendor_registration_' . $token);
            $cacheHasVendorData = !empty($vendorDataFromCache);
            
            logMessage("Vérification des données du cache", [
                'token' => $token,
                'cache_has_data' => $cacheHasVendorData,
                'cache_data' => $vendorDataFromCache
            ]);
        } else {
            logMessage("Aucun token disponible pour vérifier le cache", [
                'url_token' => $_GET['token'] ?? 'Non défini',
                'session_token' => $_SESSION['token'] ?? 'Non défini',
                'post_token' => $_POST['token'] ?? 'Non défini'
            ]);
        }
        
        // Afficher les résultats
        echo "<h3>État des données après le retour de Stripe</h3>";
        
        echo "<table>";
        echo "<tr><th>Test</th><th>Résultat</th><th>Détails</th></tr>";
        
        // Vérifier la persistance du token
        echo "<tr>";
        echo "<td>Token préservé dans l'URL</td>";
        echo "<td>" . (isset($_GET['token']) ? "<span style='color:green'>✓ OUI</span>" : "<span style='color:red'>✗ NON</span>") . "</td>";
        echo "<td>Token dans l'URL: " . (isset($_GET['token']) ? $_GET['token'] : "Non trouvé") . "</td>";
        echo "</tr>";
        
        // Vérifier la session PHP
        echo "<tr>";
        echo "<td>Session PHP maintenue</td>";
        echo "<td>" . (isset($_SESSION['token']) ? "<span style='color:green'>✓ OUI</span>" : "<span style='color:red'>✗ NON</span>") . "</td>";
        echo "<td>Session ID: " . session_id() . "</td>";
        echo "</tr>";
        
        // Vérifier les données vendeur en session
        echo "<tr>";
        echo "<td>Données vendeur en session</td>";
        echo "<td>" . ($sessionHasVendorData ? "<span style='color:green'>✓ OUI</span>" : "<span style='color:red'>✗ NON</span>") . "</td>";
        echo "<td>" . ($sessionHasVendorData ? "Données trouvées pour: " . htmlspecialchars($_SESSION['vendor_data']['company_name']) : "Aucune donnée trouvée") . "</td>";
        echo "</tr>";
        
        // Vérifier les données vendeur en cache
        echo "<tr>";
        echo "<td>Données vendeur en cache</td>";
        echo "<td>" . ($cacheHasVendorData ? "<span style='color:green'>✓ OUI</span>" : "<span style='color:red'>✗ NON</span>") . "</td>";
        echo "<td>" . ($cacheHasVendorData ? "Données trouvées pour: " . htmlspecialchars($vendorDataFromCache['company_name']) : "Aucune donnée trouvée" . ($token ? "" : " (pas de token)")) . "</td>";
        echo "</tr>";
        
        echo "</table>";
        
        // Verdict
        echo "<h3>Verdict</h3>";
        
        if (!isset($_GET['token'])) {
            echo "<div class='error'>";
            echo "<strong>PROBLÈME IDENTIFIÉ !</strong> Le token n'est pas présent dans l'URL de retour.";
            echo "</div>";
            echo "<p>C'est probablement la source principale de votre problème. Lorsque Stripe redirige vers votre site, le token n'est pas inclus dans l'URL.</p>";
        } elseif (!$sessionHasVendorData && $cacheHasVendorData) {
            echo "<div class='warning'>";
            echo "<strong>PROBLÈME IDENTIFIÉ !</strong> La session a été perdue, mais le cache fonctionne correctement.";
            echo "</div>";
            echo "<p>C'est un scénario courant avec les redirections Stripe. L'approche par token dans l'URL + cache est la bonne solution.</p>";
        } elseif (!$sessionHasVendorData && !$cacheHasVendorData && $token) {
            echo "<div class='error'>";
            echo "<strong>PROBLÈME GRAVE !</strong> Ni la session ni le cache ne fonctionnent correctement, malgré la présence du token.";
            echo "</div>";
            echo "<p>Vérifiez la configuration de votre cache et la durée de vie des données mises en cache.</p>";
        } elseif ($sessionHasVendorData && $cacheHasVendorData) {
            echo "<div class='success'>";
            echo "<strong>TOUT FONCTIONNE !</strong> Les données sont préservées à la fois en session et dans le cache.";
            echo "</div>";
            echo "<p>Dans ce test, tout fonctionne correctement, ce qui suggère que le problème dans votre application réelle est lié à la façon dont les URLs sont générées pour Stripe.</p>";
        }
        
        // Solution recommandée
        echo "<h3>Solution recommandée</h3>";
        
        if (!isset($_GET['token'])) {
            echo "<div class='test-section'>";
            echo "<h4>Problème: Le token n'est pas inclus dans l'URL de retour</h4>";
            echo "<pre style='background:#f8f9fa; padding:15px;'>";
            echo "// Dans PaymentController.php, méthode initiatePayment\n\n";
            echo "// 1. Assurez-vous que le token est bien défini\n";
            echo "\$token = \$request->input('token') ?? session('vendor_token');\n\n";
            echo "// 2. Construisez manuellement les URLs avec le token\n";
            echo "\$successUrl = route('vendor.payment.success');\n";
            echo "\$cancelUrl = route('vendor.payment.cancel');\n\n";
            echo "// 3. Ajoutez le token aux URLs AVANT d'ajouter les paramètres Stripe\n";
            echo "// IMPORTANT: utilisez urlencode() pour éviter les problèmes avec les caractères spéciaux\n";
            echo "\$successUrl = \$successUrl . '?token=' . urlencode(\$token);\n";
            echo "\$cancelUrl = \$cancelUrl . '?token=' . urlencode(\$token);\n\n";
            echo "// 4. Ajoutez les paramètres Stripe APRÈS le token\n";
            echo "\$successUrl .= '&session_id={CHECKOUT_SESSION_ID}';\n\n";
            echo "// 5. Assurez-vous que ces URLs sont utilisées telles quelles par Stripe\n";
            echo "\$session = \$this->stripeService->createCheckoutSession(\n";
            echo "    \$vendor,\n";
            echo "    \$plan,\n";
            echo "    \$planConfig['price_id'],\n";
            echo "    \$successUrl,\n";
            echo "    \$cancelUrl\n";
            echo ");\n";
            echo "</pre>";
            echo "</div>";
        } elseif (!$sessionHasVendorData && $cacheHasVendorData) {
            echo "<div class='test-section'>";
            echo "<h4>Problème: La session est perdue mais le cache fonctionne</h4>";
            echo "<p>Utilisez le pattern suivant pour récupérer les données après le retour de Stripe:</p>";
            echo "<pre style='background:#f8f9fa; padding:15px;'>";
            echo "// Dans PaymentController.php, méthode paymentSuccess\n\n";
            echo "// 1. Récupérer le token de l'URL\n";
            echo "\$token = \$request->query('token');\n\n";
            echo "// 2. Récupérer les données du cache\n";
            echo "if (\$token) {\n";
            echo "    \$vendorData = Cache::get('vendor_registration_' . \$token);\n";
            echo "    \n";
            echo "    if (\$vendorData) {\n";
            echo "        // 3. Restaurer en session si nécessaire\n";
            echo "        session(['vendor_data' => \$vendorData, 'vendor_token' => \$token]);\n";
            echo "        \n";
            echo "        // 4. Continuer le traitement...\n";
            echo "    }\n";
            echo "}\n";
            echo "</pre>";
            echo "</div>";
        }
        
        // Lien pour réexécuter le test
        echo "<p><a href='?action=start' class='btn'>Redémarrer le test</a></p>";
        echo "</div>";
        break;
        
    default:
        echo "<div class='error'>Action inconnue: $action</div>";
        echo "<p><a href='?action=start' class='btn'>Retour à l'accueil</a></p>";
}

echo "</body></html>";