#!/usr/bin/env php
<?php

/**
 * Script de test automatisé pour Nomad SEO
 *
 * Usage: php tests/test_nomad_seo.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Article;
use App\Models\User;
use App\Services\Seo\SeoAnalyzer;
use Illuminate\Support\Str;

echo "\n";
echo "===========================================\n";
echo "  Test Automatisé - Nomad SEO v2.0\n";
echo "===========================================\n\n";

// Couleurs pour le terminal
function green($text) { return "\033[32m{$text}\033[0m"; }
function red($text) { return "\033[31m{$text}\033[0m"; }
function yellow($text) { return "\033[33m{$text}\033[0m"; }
function blue($text) { return "\033[34m{$text}\033[0m"; }

$passed = 0;
$failed = 0;

function test($description, $callback) {
    global $passed, $failed;

    echo "• Testing: " . yellow($description) . " ... ";

    try {
        $result = $callback();
        if ($result === true) {
            echo green("✓ PASSED") . "\n";
            $passed++;
        } else {
            echo red("✗ FAILED") . "\n";
            if (is_string($result)) {
                echo "  Reason: " . red($result) . "\n";
            }
            $failed++;
        }
    } catch (Exception $e) {
        echo red("✗ ERROR") . "\n";
        echo "  " . red($e->getMessage()) . "\n";
        $failed++;
    }
}

echo blue("1. Tests de Migration\n") . "\n";

test("Colonne 'subtitle' existe dans la table articles", function() {
    return \Schema::hasColumn('articles', 'subtitle');
});

test("Colonne 'focus_keyphrase' existe dans la table articles", function() {
    return \Schema::hasColumn('articles', 'focus_keyphrase');
});

echo "\n" . blue("2. Tests du Modèle Article\n") . "\n";

test("'subtitle' est dans \$fillable", function() {
    $fillable = (new Article())->getFillable();
    return in_array('subtitle', $fillable);
});

test("'focus_keyphrase' est dans \$fillable", function() {
    $fillable = (new Article())->getFillable();
    return in_array('focus_keyphrase', $fillable);
});

echo "\n" . blue("3. Tests de SeoAnalyzer\n") . "\n";

test("Méthode analyzeFocusKeyphrase() existe", function() {
    $reflection = new ReflectionClass(SeoAnalyzer::class);
    return $reflection->hasMethod('analyzeFocusKeyphrase');
});

test("Méthode analyzeTransitionWords() existe", function() {
    $reflection = new ReflectionClass(SeoAnalyzer::class);
    return $reflection->hasMethod('analyzeTransitionWords');
});

test("Méthode analyzeLinks() existe", function() {
    $reflection = new ReflectionClass(SeoAnalyzer::class);
    return $reflection->hasMethod('analyzeLinks');
});

echo "\n" . blue("4. Tests Fonctionnels (création article de test)\n") . "\n";

// Récupérer un utilisateur pour les tests
$user = User::where('writer_type', 'team')->orWhere('writer_type', 'community')->first();

if (!$user) {
    echo red("⚠ Aucun utilisateur trouvé pour les tests. Passage des tests fonctionnels.\n\n");
} else {
    echo "  Utilisation de l'utilisateur: " . blue($user->name) . " (ID: {$user->id})\n\n";

    // Créer un article de test
    $testArticle = new Article();
    $testArticle->user_id = $user->id;
    $testArticle->title = "Guide complet pour organiser votre voyage à Bali en 2024";
    $testArticle->subtitle = "Découvrez tous nos conseils pratiques pour un séjour inoubliable";
    $testArticle->slug = Str::slug($testArticle->title);
    $testArticle->focus_keyphrase = "voyage à Bali";
    $testArticle->content = '
        <p>Bali est une destination de rêve pour de nombreux voyageurs. Cependant, organiser son voyage demande une bonne préparation. Dans ce guide, nous vous donnons tous nos conseils.</p>

        <h2>Quand partir à Bali ?</h2>
        <p>La meilleure période pour visiter Bali est d\'avril à octobre. En effet, c\'est la saison sèche. Toutefois, même pendant la saison des pluies, il est possible de profiter de l\'île. Votre voyage à Bali sera mémorable.</p>

        <h2>Budget pour un voyage à Bali</h2>
        <p>Pour un voyage confortable, prévoyez environ 50€ par jour. Ainsi, vous pourrez profiter pleinement de votre séjour. De plus, Bali offre un excellent rapport qualité-prix.</p>

        <p>Pour plus d\'informations, consultez notre <a href="/blog/budget-bali">guide budget détaillé</a>.</p>

        <p>Vous pouvez également consulter le <a href="https://www.indonesia.travel">site officiel du tourisme</a> pour plus de détails.</p>
    ';
    $testArticle->excerpt = "Organisez votre voyage à Bali avec notre guide complet";
    $testArticle->status = 'draft';
    $testArticle->meta_data = [
        'description' => 'Organisez votre voyage à Bali avec notre guide complet : budget, itinéraire, conseils pratiques et bons plans pour un séjour réussi.',
        'keywords' => ['voyage', 'bali', 'guide'],
        'category' => 'Destinations',
        'tags' => 'voyage, bali, indonésie'
    ];

    test("Création d'un article avec subtitle et focus_keyphrase", function() use ($testArticle) {
        $saved = $testArticle->save();
        return $saved && !empty($testArticle->id);
    });

    test("Article sauvegardé contient subtitle", function() use ($testArticle) {
        $article = Article::find($testArticle->id);
        return $article && $article->subtitle === "Découvrez tous nos conseils pratiques pour un séjour inoubliable";
    });

    test("Article sauvegardé contient focus_keyphrase", function() use ($testArticle) {
        $article = Article::find($testArticle->id);
        return $article && $article->focus_keyphrase === "voyage à Bali";
    });

    // Analyser l'article
    $analyzer = new SeoAnalyzer();

    test("Analyse SEO de l'article", function() use ($analyzer, $testArticle, $user) {
        $analysis = $analyzer->analyzeArticle($testArticle->fresh(), $user);
        return $analysis !== null;
    });

    $analysis = $testArticle->fresh()->latestSeoAnalysis;

    if ($analysis) {
        test("Analyse contient keyword_data", function() use ($analysis) {
            return is_array($analysis->keyword_data) && !empty($analysis->keyword_data);
        });

        test("keyword_data contient focus_keyphrase", function() use ($analysis) {
            return isset($analysis->keyword_data['focus_keyphrase']) &&
                   $analysis->keyword_data['focus_keyphrase'] === "voyage à Bali";
        });

        test("keyword_data détecte mot-clé dans titre", function() use ($analysis) {
            return isset($analysis->keyword_data['in_title']) &&
                   $analysis->keyword_data['in_title'] === true;
        });

        test("keyword_data détecte mot-clé dans meta", function() use ($analysis) {
            return isset($analysis->keyword_data['in_meta']) &&
                   $analysis->keyword_data['in_meta'] === true;
        });

        test("keyword_data calcule la densité", function() use ($analysis) {
            return isset($analysis->keyword_data['density']) &&
                   $analysis->keyword_data['density'] > 0;
        });

        test("Mots de transition détectés", function() use ($analysis) {
            return isset($analysis->keyword_data['transitions_count']) &&
                   $analysis->keyword_data['transitions_count'] > 0;
        });

        test("Pourcentage de transitions calculé", function() use ($analysis) {
            return isset($analysis->keyword_data['transitions_percentage']) &&
                   $analysis->keyword_data['transitions_percentage'] > 0;
        });

        test("Liens internes comptés", function() use ($analysis) {
            return $analysis->internal_links_count === 1;
        });

        test("Liens externes comptés", function() use ($analysis) {
            return $analysis->external_links_count === 1;
        });

        echo "\n" . blue("  Résultats de l'analyse:\n");
        echo "  - Focus keyphrase: " . green($analysis->keyword_data['focus_keyphrase'] ?? 'N/A') . "\n";
        echo "  - Dans titre: " . ($analysis->keyword_data['in_title'] ? green('✓') : red('✗')) . "\n";
        echo "  - Dans meta: " . ($analysis->keyword_data['in_meta'] ? green('✓') : red('✗')) . "\n";
        echo "  - Dans sous-titres: " . ($analysis->keyword_data['in_headings'] ? green('✓') : red('✗')) . "\n";
        echo "  - Densité: " . yellow($analysis->keyword_data['density'] . '%') . "\n";
        echo "  - Occurrences: " . yellow($analysis->keyword_data['occurrences']) . "\n";
        echo "  - Mots de transition: " . yellow($analysis->keyword_data['transitions_count']) . " (" . $analysis->keyword_data['transitions_percentage'] . "%)\n";
        echo "  - Liens internes: " . yellow($analysis->internal_links_count) . "\n";
        echo "  - Liens externes: " . yellow($analysis->external_links_count) . "\n";
        echo "  - Score global: " . green($analysis->global_score) . "/100\n";
    }

    // Nettoyer l'article de test
    test("Suppression de l'article de test", function() use ($testArticle) {
        return $testArticle->delete();
    });
}

echo "\n";
echo "===========================================\n";
echo "  Résumé des Tests\n";
echo "===========================================\n";
echo green("✓ Tests réussis: {$passed}\n");
if ($failed > 0) {
    echo red("✗ Tests échoués: {$failed}\n");
} else {
    echo green("✗ Tests échoués: {$failed}\n");
}
echo "  Total: " . ($passed + $failed) . "\n";
echo "===========================================\n\n";

if ($failed === 0) {
    echo green("🎉 Tous les tests sont passés ! Nomad SEO est opérationnel.\n\n");
    exit(0);
} else {
    echo red("⚠ Certains tests ont échoué. Vérifiez les erreurs ci-dessus.\n\n");
    exit(1);
}
