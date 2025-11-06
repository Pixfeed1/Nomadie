<?php
// public/debug_payment_flow.php

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
    $logger->info($message, $data);
}

function getRequestInfo() {
    return [
        'url' => $_SERVER['REQUEST_URI'] ?? 'Non définie',
        'method' => $_SERVER['REQUEST_METHOD'] ?? 'Non défini',
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'Non définie',
        'session_id' => session_id(),
        'has_session_cookie' => isset($_COOKIE[session_name()]),
        'session_cookie_value' => $_COOKIE[session_name()] ?? 'Non définie',
        'all_cookies' => $_COOKIE,
    ];
}

// ================ TRAITEMENT DU FLUX ================

// Simuler un utilisateur parcourant le processus
$action = $_GET['action'] ?? 'start';
$token = $_GET['token'] ?? ($_POST['token'] ?? null);

echo "<h1>Débogage du flux d'inscription et paiement</h1>";
echo "<h2>Action: $action</h2>";

// Afficher les informations sur la requête
logMessage("Informations sur la requête", getRequestInfo());

// Vérifier la configuration de session
logMessage("Configuration de session Laravel", [
    'driver' => $config->get('session.driver'),
    'lifetime' => $config->get('session.lifetime'),
    'domain' => $config->get('session.domain'),
    'secure' => $config->get('session.secure'),
    'same_site' => $config->get('session.same_site'),
    'cookie' => $config->get('session.cookie'),
]);

switch ($action) {
    case 'start':
        // Point de départ
        logMessage("Début du processus de test");
        
        // Réinitialiser les données de test
        $_SESSION['vendor_data'] = null;
        $_SESSION['vendor_token'] = null;
        
        echo "<form method='post' action='?action=step1'>";
        echo "<input type='hidden' name='token' value=''>";
        echo "<button type='submit' style='padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 4px;'>Commencer le test</button>";
        echo "</form>";
        break;
        
    case 'step1':
        // Étape 1: Simuler le stockage initial des données
        logMessage("Étape 1: Création des données de test");
        
        // Générer un token si pas déjà présent
        if (empty($token)) {
            $token = md5(uniqid() . time());
        }
        
        // Données de test
        $testData = [
            'company_name' => 'Test Company',
            'email' => 'test@example.com',
            'subscription' => 'essential',
            'timestamp' => time()
        ];
        
        // Stocker dans la session
        $_SESSION['vendor_data'] = $testData;
        $_SESSION['vendor_token'] = $token;
        
        logMessage("Données stockées en session", [
            'token' => $token,
            'session_data' => $_SESSION,
            'has_vendor_data' => isset($_SESSION['vendor_data']),
            'session_id' => session_id()
        ]);
        
        // Stocker dans le cache
        $cache->put('vendor_registration_' . $token, $testData, now()->addHour());
        
        logMessage("Données stockées dans le cache", [
            'token' => $token,
            'cache_key' => 'vendor_registration_' . $token,
            'cache_has_data' => $cache->has('vendor_registration_' . $token)
        ]);
        
        echo "<p>Données créées et stockées en session et dans le cache.</p>";
        echo "<form method='post' action='?action=redirect_simulation'>";
        echo "<input type='hidden' name='token' value='$token'>";
        echo "<button type='submit' style='padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 4px;'>Simuler la redirection vers la page de paiement</button>";
        echo "</form>";
        break;
        
    case 'redirect_simulation':
        // Simuler une redirection vers Stripe puis retour
        logMessage("Simulation de redirection vers paiement", [
            'token' => $token,
            'session_id_before' => session_id(),
            'session_has_token' => isset($_SESSION['vendor_token']),
            'session_token' => $_SESSION['vendor_token'] ?? 'Non défini',
            'cache_has_data' => $token ? $cache->has('vendor_registration_' . $token) : false
        ]);
        
        // URL de retour avec token
        $returnUrl = "debug_payment_flow.php?action=payment_return&token=" . urlencode($token);
        
        echo "<p>Normalement, c'est ici que l'utilisateur serait redirigé vers Stripe.</p>";
        echo "<p>Nous allons simuler ce processus avec une simple redirection interne.</p>";
        
        // Mettre en session pour pouvoir vérifier après la redirection
        $_SESSION['pre_redirect_data'] = [
            'token' => $token,
            'session_id' => session_id(),
            'timestamp' => time()
        ];
        
        echo "<p>Redirection vers: <a href='$returnUrl'>$returnUrl</a></p>";
        echo "<script>
            // Simulation d'une redirection après 3 secondes
            setTimeout(function() {
                window.location.href = '$returnUrl';
            }, 3000);
        </script>";
        break;
        
    case 'payment_return':
        // Simuler le retour du paiement
        logMessage("Retour de paiement simulé", [
            'token' => $token,
            'session_id' => session_id(),
            'pre_redirect_data' => $_SESSION['pre_redirect_data'] ?? 'Non définie',
            'session_token_persisted' => $_SESSION['vendor_token'] ?? 'Non défini',
            'session_changed' => isset($_SESSION['pre_redirect_data']) && 
                                ($_SESSION['pre_redirect_data']['session_id'] !== session_id())
        ]);
        
        // Vérifier si les données sont encore disponibles
        $sessionHasData = isset($_SESSION['vendor_data']);
        $cacheHasData = $token ? $cache->has('vendor_registration_' . $token) : false;
        
        if ($cacheHasData) {
            $vendorData = $cache->get('vendor_registration_' . $token);
            logMessage("Données trouvées dans le cache", [
                'token' => $token,
                'vendor_data' => $vendorData
            ]);
        } else {
            logMessage("DONNÉES NON TROUVÉES DANS LE CACHE", [
                'token' => $token,
                'cache_key' => 'vendor_registration_' . $token
            ]);
        }
        
        if ($sessionHasData) {
            logMessage("Données trouvées en session", [
                'vendor_data' => $_SESSION['vendor_data']
            ]);
        } else {
            logMessage("DONNÉES NON TROUVÉES EN SESSION", [
                'available_keys' => array_keys($_SESSION)
            ]);
        }
        
        // Afficher les résultats du test
        echo "<h3>Résultat du test de flux</h3>";
        
        if ($sessionHasData && $cacheHasData) {
            echo "<div style='padding: 15px; background: #d4edda; border-left: 4px solid #28a745;'>";
            echo "<p><strong>✅ Succès!</strong> Les données ont été préservées à la fois en session et dans le cache.</p>";
            echo "</div>";
        } elseif ($cacheHasData) {
            echo "<div style='padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107;'>";
            echo "<p><strong>⚠️ Partial Success!</strong> Les données sont préservées dans le cache mais PAS en session.</p>";
            echo "<p>C'est pourquoi il faut utiliser l'approche par token dans l'URL et le cache.</p>";
            echo "</div>";
        } elseif ($sessionHasData) {
            echo "<div style='padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107;'>";
            echo "<p><strong>⚠️ Partial Success!</strong> Les données sont préservées en session mais PAS dans le cache.</p>";
            echo "</div>";
        } else {
            echo "<div style='padding: 15px; background: #f8d7da; border-left: 4px solid #dc3545;'>";
            echo "<p><strong>❌ Échec!</strong> Les données ont été perdues dans les DEUX stockages.</p>";
            echo "</div>";
            
            if ($token) {
                echo "<p>Le token est bien passé dans l'URL, mais les données associées n'ont pas été retrouvées.</p>";
                echo "<p>Vérifiez les temps d'expiration du cache et la configuration du driver de cache.</p>";
            } else {
                echo "<p>Le token n'a pas été transmis dans l'URL de retour. C'est probablement le problème principal.</p>";
            }
        }
        
        // Instructions pour la suite
        echo "<h3>Comment résoudre le problème?</h3>";
        echo "<p>Selon les résultats ci-dessus:</p>";
        echo "<ul>";
        
        if (!$sessionHasData) {
            echo "<li>La session ne persiste pas après la redirection, cela indique un problème de cookie ou de configuration de session.</li>";
        }
        
        if (!$cacheHasData && $token) {
            echo "<li>Les données du cache sont perdues ou expirées, vérifiez la configuration du driver de cache.</li>";
        }
        
        if (!$token) {
            echo "<li>Le token n'est pas transmis dans l'URL lors des redirections, c'est probablement le problème principal.</li>";
        }
        
        echo "</ul>";
        
        // Solutions concrètes
        echo "<h3>Solutions concrètes</h3>";
        echo "<p>Basé sur les résultats, voici les solutions à appliquer:</p>";
        
        if (!$sessionHasData) {
            echo "<h4>1. Problème de session:</h4>";
            echo "<pre style='background: #f5f5f5; padding: 10px; border-left: 4px solid #3498db'>";
            echo "// Dans .env\n";
            echo "SESSION_DRIVER=file\n";
            echo "SESSION_DOMAIN=test2.jewelme.fr.test\n";
            echo "SESSION_SECURE_COOKIE=true\n";
            echo "SESSION_SAME_SITE=lax\n\n";
            
            echo "// Dans PaymentController.php, méthode initiatePayment()\n";
            echo "// Assurez-vous d'inclure le token dans les URLs de redirection Stripe\n";
            echo "\$successUrl = route('vendor.payment.success') . '?token=' . urlencode(\$token);\n";
            echo "\$cancelUrl = route('vendor.payment.cancel') . '?token=' . urlencode(\$token);\n";
            echo "</pre>";
        }
        
        if (!$cacheHasData && $token) {
            echo "<h4>2. Problème de cache:</h4>";
            echo "<pre style='background: #f5f5f5; padding: 10px; border-left: 4px solid #3498db'>";
            echo "// Dans .env\n";
            echo "CACHE_DRIVER=file   # Utilisez file au lieu de redis ou database pour simplifier\n\n";
            
            echo "// Assurez-vous que le cache fonctionne\n";
            echo "Cache::put('test_key', 'test_value', now()->addMinutes(60));\n";
            echo "var_dump(Cache::get('test_key'));\n";
            echo "</pre>";
        }
        
        if (!$token) {
            echo "<h4>3. Problème de transmission du token:</h4>";
            echo "<pre style='background: #f5f5f5; padding: 10px; border-left: 4px solid #3498db'>";
            echo "// Dans register.blade.php (formulaire)\n";
            echo "&lt;input type=\"hidden\" name=\"token\" value=\"{{ session('vendor_token') ?? request()->query('token') }}\"&gt;\n\n";
            
            echo "// Dans VendorRegistrationController.php, méthode store()\n";
            echo "\$token = \$request->input('token') ?? md5(uniqid() . time());\n";
            echo "Cache::put('vendor_registration_' . \$token, \$validated, now()->addHour());\n";
            echo "session(['vendor_token' => \$token]);\n";
            echo "\$paymentUrl = route('vendor.payment.show') . '?token=' . urlencode(\$token);\n";
            echo "return redirect()->to(\$paymentUrl);\n";
            echo "</pre>";
        }
        
        // Option pour recommencer le test
        echo "<p><a href='?action=start' style='padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; display: inline-block;'>Recommencer le test</a></p>";
        break;
        
    default:
        echo "<p>Action non reconnue: $action</p>";
        echo "<p><a href='?action=start'>Retour au début</a></p>";
}

// Afficher les informations finales
echo "<hr>";
echo "<h3>Informations de session finales</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>ID de session: " . session_id() . "</h3>";
echo "<h3>Cookie de session: " . (isset($_COOKIE[session_name()]) ? $_COOKIE[session_name()] : 'Non défini') . "</h3>";