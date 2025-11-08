<?php

namespace App\Services\Seo;

use App\Models\Article;
use App\Models\SeoAnalysis;
use App\Models\SeoAnalysisDetail;
use App\Models\SeoCriterion;
use App\Models\SeoConfiguration;
use App\Models\SeoSuggestion;
use App\Models\User;
use Illuminate\Support\Str;
use DOMDocument;
use DOMXPath;

class SeoAnalyzer
{
    protected $article;
    protected $analysis;
    protected $user;
    protected $writerType;
    protected $mode;
    protected $configurations;
    
    /**
     * Analyser un article
     */
    public function analyzeArticle(Article $article, User $user = null, string $mode = 'libre')
    {
        $this->article = $article;
        $this->user = $user ?? $article->user;
        $this->determineWriterType();
        $this->mode = $mode;
        
        // Créer ou récupérer l'analyse
        $this->analysis = SeoAnalysis::firstOrCreate(
            [
                'article_id' => $article->id,
                'user_id' => $this->user->id,
            ],
            [
                'writer_type' => $this->writerType,
                'mode' => $this->mode,
                'status' => 'analyzing'
            ]
        );
        
        // Charger les configurations pour ce type de rédacteur
        $this->loadConfigurations();
        
        // Effectuer l'analyse
        $this->performAnalysis();
        
        // Calculer les scores
        $this->calculateScores();
        
        // Générer les suggestions
        $this->generateSuggestions();
        
        // Vérifier l'éligibilité dofollow
        $this->checkDoFollowStatus();
        
        // Marquer comme complété
        $this->analysis->status = 'completed';
        $this->analysis->save();
        
        return $this->analysis->fresh()->load(['details.criterion', 'suggestions.suggestedArticle']);
    }

    /**
     * Analyse rapide et basique du contenu (méthode simple)
     */
    public function analyzeRaw($title, $content, $metaDescription = null)
    {
        $plainText = strip_tags($content);
        $wordCount = str_word_count($plainText);
        
        $score = 0;
        $suggestions = [];
        
        // Analyse du titre (20 points max)
        if (strlen($title) >= 30 && strlen($title) <= 60) {
            $score += 15;
        } else {
            $score += 5;
            $suggestions[] = 'Optimisez la longueur du titre (30-60 caractères)';
        }
        
        // Analyse du contenu (30 points max)
        if ($wordCount >= 1500) {
            $score += 25;
        } elseif ($wordCount >= 1000) {
            $score += 15;
            $suggestions[] = 'Augmentez le contenu à 1500+ mots';
        } else {
            $score += 5;
            $suggestions[] = 'Contenu trop court (minimum 1500 mots recommandé)';
        }
        
        // Meta description (15 points max)
        if ($metaDescription && strlen($metaDescription) >= 120 && strlen($metaDescription) <= 160) {
            $score += 15;
        } else {
            $score += 5;
            $suggestions[] = 'Optimisez la meta description (120-160 caractères)';
        }
        
        // Structure (20 points max)
        $hasH2 = preg_match('/<h2/i', $content);
        $hasH3 = preg_match('/<h3/i', $content);
        $hasImages = preg_match('/<img/i', $content);
        
        if ($hasH2) $score += 7;
        if ($hasH3) $score += 7;
        if ($hasImages) $score += 6;
        
        if (!$hasH2) $suggestions[] = 'Ajoutez des sous-titres H2';
        if (!$hasImages) $suggestions[] = 'Ajoutez des images';
        
        // Lisibilité (15 points max)
        $sentences = preg_split('/[.!?]+/', $plainText);
        $avgWordsPerSentence = count($sentences) > 0 ? $wordCount / count($sentences) : 0;
        
        if ($avgWordsPerSentence >= 10 && $avgWordsPerSentence <= 20) {
            $score += 15;
        } else {
            $score += 7;
            $suggestions[] = 'Optimisez la longueur des phrases';
        }
        
        return [
            'score' => min(100, $score),
            'word_count' => $wordCount,
            'suggestions' => array_slice($suggestions, 0, 5)
        ];
    }
    
    /**
     * Déterminer le type de rédacteur
     */
    protected function determineWriterType()
    {
        // Utiliser le writer_type de la base de données
        // Valeurs possibles: 'team', 'partner', 'client_contributor', 'community'
        $this->writerType = $this->user->writer_type ?? 'community';
    }
    
    /**
     * Charger les configurations
     */
    protected function loadConfigurations()
    {
        $this->configurations = SeoConfiguration::with('criterion')
            ->where('writer_type', $this->writerType)
            ->where('mode', $this->mode)
            ->get()
            ->keyBy('criterion.code');
    }
    
    /**
     * Effectuer l'analyse complète
     */
    protected function performAnalysis()
    {
        // Analyses de base
        $this->analyzeContent();
        $this->analyzeTechnical();
        $this->analyzeImages();
        $this->analyzeEngagement();
        $this->analyzeAuthenticity();
    }
    
    /**
     * Analyser le contenu
     */
    protected function analyzeContent()
    {
        $content = strip_tags($this->article->content);
        $title = $this->article->title;
        
        // Title Length
        $this->analyzeTitle($title);
        
        // Word Count
        $this->analyzeWordCount($content);
        
        // Readability
        $this->analyzeReadability($content);
        
        // Paragraph Structure
        $this->analyzeParagraphStructure($this->article->content);
        
        // Headings Hierarchy
        $this->analyzeHeadings($this->article->content);

        // PHASE 6: LSI Keywords (richesse sémantique)
        $this->analyzeLsiKeywords($content);
    }
    
    /**
     * Analyser le titre
     */
    protected function analyzeTitle($title)
    {
        $criterion = SeoCriterion::where('code', 'title_length')->first();
        if (!$criterion) return;
        
        $length = strlen($title);
        $config = $this->configurations['title_length'] ?? null;
        $rules = $criterion->validation_rules;
        
        $score = 0;
        $passed = false;
        $feedback = [];
        
        if ($length >= $rules['min'] && $length <= $rules['max']) {
            $score = $criterion->max_score;
            $passed = true;
            $feedback['message'] = 'La longueur du titre est optimale';
        } else {
            $score = max(0, $criterion->max_score * (1 - abs($length - 55) / 55));
            $feedback['message'] = "Le titre fait {$length} caractères (optimal: 50-60)";
            $feedback['suggestions'] = ['Ajustez la longueur du titre'];
        }
        
        $this->saveDetail($criterion, $score, $passed, $feedback, ['length' => $length]);
    }
    
    /**
     * Analyser le nombre de mots
     */
    protected function analyzeWordCount($content)
    {
        $criterion = SeoCriterion::where('code', 'word_count')->first();
        if (!$criterion) return;
        
        $wordCount = str_word_count($content);
        $this->analysis->word_count = $wordCount;
        
        $rules = $criterion->validation_rules;
        $score = 0;
        $passed = false;
        $feedback = [];
        
        if ($wordCount >= $rules['min']) {
            if ($wordCount >= $rules['optimal']) {
                $score = $criterion->max_score;
            } else {
                $score = $criterion->max_score * ($wordCount / $rules['optimal']);
            }
            $passed = true;
            $feedback['message'] = "L'article contient {$wordCount} mots";
        } else {
            $score = max(0, $criterion->max_score * ($wordCount / $rules['min']));
            $feedback['message'] = "L'article contient seulement {$wordCount} mots (minimum: {$rules['min']})";
            $feedback['suggestions'] = ['Développez davantage votre contenu'];
        }
        
        // Calculer le temps de lecture
        $this->analysis->reading_time = ceil($wordCount / 200);
        
        $this->saveDetail($criterion, $score, $passed, $feedback, ['word_count' => $wordCount]);
    }
    
    /**
     * Analyser la lisibilité avec améliorations françaises et web
     */
    protected function analyzeReadability($content)
    {
        $criterion = SeoCriterion::where('code', 'readability')->first();
        if (!$criterion) return;
        
        // Nettoyer le contenu pour les contractions françaises
        $cleanContent = $this->normalizeContractions($content);
        
        // Nettoyer le contenu et compter plus précisément
        $cleanContent = preg_replace('/\s+/', ' ', trim($cleanContent));
        
        // Compter les phrases (plus précis)
        $sentences = preg_split('/[.!?]+(?:\s+|$)/', $cleanContent, -1, PREG_SPLIT_NO_EMPTY);
        $sentenceCount = count($sentences);
        $wordCount = str_word_count($cleanContent);
        $syllableCount = $this->countSyllablesFrench($cleanContent);
        
        $fleschScore = 0;
        $fleschKincaidGrade = 0;
        
        if ($sentenceCount > 0 && $wordCount > 0) {
            // Flesch Reading Ease adapté au français
            $avgWordsPerSentence = $wordCount / $sentenceCount;
            $avgSyllablesPerWord = $syllableCount / $wordCount;
            
            $fleschScore = 206.835 - (1.015 * $avgWordsPerSentence) - (84.6 * $avgSyllablesPerWord);
            $fleschScore = max(0, min(100, $fleschScore));
            
            // Flesch-Kincaid Grade Level (niveau scolaire)
            $fleschKincaidGrade = (0.39 * $avgWordsPerSentence) + (11.8 * $avgSyllablesPerWord) - 15.59;
            $fleschKincaidGrade = max(0, $fleschKincaidGrade);
        }
        
        // Facteur contenu web (bonus pour structure)
        $webBonus = $this->calculateWebReadabilityBonus($this->article->content);
        $finalScore = min(100, $fleschScore + $webBonus);
        
        // Interpréter le score
        $readabilityLevel = $this->interpretFleschScore($fleschScore);
        
        $rules = $criterion->validation_rules;
        $passed = $finalScore >= ($rules['min_score'] ?? 50);
        
        $criterionScore = ($finalScore / 100) * $criterion->max_score;
        
        $feedback = [
            'message' => sprintf(
                "Score de lisibilité: %.1f/100 (%s) - Niveau scolaire: %.1f%s",
                $finalScore,
                $readabilityLevel,
                $fleschKincaidGrade,
                $webBonus > 0 ? " (+{$webBonus} web)" : ""
            ),
            'suggestions' => !$passed ? [
                'Utilisez des phrases plus courtes (actuellement ' . round($wordCount/$sentenceCount) . ' mots/phrase)',
                'Simplifiez le vocabulaire (actuellement ' . round($syllableCount/$wordCount, 1) . ' syllabes/mot)',
                'Visez un score de 60-70 pour un public général'
            ] : []
        ];
        
        $this->saveDetail($criterion, $criterionScore, $passed, $feedback, [
            'flesch_score' => round($fleschScore, 1),
            'flesch_kincaid_grade' => round($fleschKincaidGrade, 1),
            'web_bonus' => $webBonus,
            'final_score' => round($finalScore, 1),
            'readability_level' => $readabilityLevel,
            'avg_words_per_sentence' => round($wordCount/$sentenceCount, 1),
            'avg_syllables_per_word' => round($syllableCount/$wordCount, 2)
        ]);
    }
    
    /**
     * Normaliser les contractions françaises
     */
    protected function normalizeContractions($text)
    {
        // Gérer les contractions principales l', d', c', s', j', m', t', n'
        $text = preg_replace("/([ldjcmstnoui])'([aeiouhy])/i", '$1 $2', $text);
        
        // Cas spéciaux fréquents
        $text = str_ireplace(["qu'", "jusqu'", "lorsqu'", "puisqu'"], 
                           ["que ", "jusque ", "lorsque ", "puisque "], $text);
        
        return $text;
    }
    
    /**
     * Compter les syllabes en français avec diphtongues principales
     */
    protected function countSyllablesFrench($text)
    {
        $words = str_word_count(mb_strtolower($text), 1);
        $syllables = 0;
        
        foreach ($words as $word) {
            // Normaliser les accents
            $word = $this->normalizeAccents($word);
            
            // Traiter les diphtongues principales (= 1 syllabe)
            $word = str_replace(['eau', 'oi', 'ou', 'ai', 'ei'], 'a', $word);
            
            // Compter les voyelles restantes
            preg_match_all('/[aeiouy]/', $word, $matches);
            $count = count($matches[0]);
            
            // E muet final
            if (preg_match('/[^aeiouy]e$/', $word)) {
                $count--;
            }
            
            $syllables += max(1, $count);
        }
        
        return $syllables;
    }
    
    /**
     * Calculer le bonus web pour la lisibilité
     */
    protected function calculateWebReadabilityBonus($htmlContent)
    {
        $bonus = 0;
        
        // Bonus pour les listes (améliorent la scannabilité)
        if (preg_match('/<ul|<ol/i', $htmlContent)) {
            $bonus += 3;
        }
        
        // Bonus pour les sous-titres (structurent le contenu)
        $headingCount = preg_match_all('/<h[23]/i', $htmlContent);
        if ($headingCount >= 3) {
            $bonus += 5;
        }
        
        return $bonus;
    }
    
    /**
     * Normaliser les accents pour le comptage de syllabes
     */
    protected function normalizeAccents($text)
    {
        $replacements = [
            'à' => 'a', 'â' => 'a', 'ä' => 'a', 'á' => 'a',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'ÿ' => 'y', 'ý' => 'y', 'ç' => 'c'
        ];
        
        return strtr(mb_strtolower($text), $replacements);
    }
    
    /**
     * Interpréter le score Flesch
     */
    protected function interpretFleschScore($score)
    {
        if ($score >= 90) return "Très facile";
        if ($score >= 80) return "Facile"; 
        if ($score >= 70) return "Assez facile";
        if ($score >= 60) return "Standard";
        if ($score >= 50) return "Assez difficile";
        if ($score >= 30) return "Difficile";
        return "Très difficile";
    }
    
    /**
     * Analyser la structure des paragraphes
     */
    protected function analyzeParagraphStructure($htmlContent)
    {
        $criterion = SeoCriterion::where('code', 'paragraph_structure')->first();
        if (!$criterion) return;
        
        $dom = new DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8'));
        $paragraphs = $dom->getElementsByTagName('p');
        
        $rules = $criterion->validation_rules;
        $longParagraphs = 0;
        $totalParagraphs = $paragraphs->length;
        
        foreach ($paragraphs as $p) {
            $text = strip_tags($p->textContent);
            if (str_word_count($text) > $rules['max_length']) {
                $longParagraphs++;
            }
        }
        
        $score = $criterion->max_score;
        $passed = true;
        
        if ($totalParagraphs > 0 && $longParagraphs / $totalParagraphs > 0.3) {
            $score = $criterion->max_score * (1 - $longParagraphs / $totalParagraphs);
            $passed = false;
        }
        
        $feedback = [
            'message' => "{$longParagraphs} paragraphes trop longs sur {$totalParagraphs}",
            'suggestions' => $passed ? [] : ['Divisez les paragraphes longs en sections plus courtes']
        ];
        
        $this->saveDetail($criterion, $score, $passed, $feedback, [
            'total_paragraphs' => $totalParagraphs,
            'long_paragraphs' => $longParagraphs
        ]);
    }

    /**
     * PHASE 6: Analyser la richesse sémantique (LSI Keywords)
     * Évalue la diversité du vocabulaire et la profondeur sémantique
     */
    protected function analyzeLsiKeywords($content)
    {
        $criterion = SeoCriterion::where('code', 'lsi_keywords')->first();
        if (!$criterion) return;

        $rules = $criterion->validation_rules;

        // Nettoyer et préparer le contenu
        $cleanContent = strtolower($content);
        $words = str_word_count($cleanContent, 1);
        $totalWords = count($words);

        if ($totalWords === 0) {
            $this->saveDetail($criterion, 0, false, [
                'message' => 'Pas de contenu à analyser',
                'suggestions' => ['Ajoutez du contenu pour activer l\'analyse LSI']
            ], []);
            return;
        }

        // Stop words français à ignorer
        $stopWords = [
            'le', 'la', 'les', 'un', 'une', 'des', 'de', 'du', 'à', 'au', 'aux',
            'et', 'ou', 'mais', 'donc', 'or', 'ni', 'car', 'je', 'tu', 'il', 'elle',
            'nous', 'vous', 'ils', 'elles', 'ce', 'cette', 'ces', 'mon', 'ton', 'son',
            'ma', 'ta', 'sa', 'mes', 'tes', 'ses', 'qui', 'que', 'quoi', 'dont', 'où',
            'est', 'sont', 'a', 'ai', 'as', 'ont', 'être', 'avoir', 'faire', 'pour',
            'dans', 'sur', 'avec', 'sans', 'sous', 'vers', 'chez', 'par', 'en', 'y'
        ];

        // Filtrer les stop words
        $meaningfulWords = array_filter($words, fn($w) => !in_array($w, $stopWords) && strlen($w) > 2);
        $meaningfulWordsCount = count($meaningfulWords);

        // 1. Calculer la diversité lexicale (ratio mots uniques)
        $uniqueWords = array_unique($meaningfulWords);
        $uniqueWordsCount = count($uniqueWords);
        $lexicalDiversity = $meaningfulWordsCount > 0 ? $uniqueWordsCount / $meaningfulWordsCount : 0;

        // 2. Analyser la distribution des mots (répétitions)
        $wordFrequency = array_count_values($meaningfulWords);
        arsort($wordFrequency);
        $topWords = array_slice($wordFrequency, 0, 10, true);

        // Calculer le % du mot le plus répété
        $maxRepetition = $meaningfulWordsCount > 0 ? (reset($topWords) / $meaningfulWordsCount) * 100 : 0;

        // 3. Analyser la longueur moyenne des mots (vocabulaire complexe)
        $totalLength = array_sum(array_map('strlen', $meaningfulWords));
        $avgWordLength = $meaningfulWordsCount > 0 ? $totalLength / $meaningfulWordsCount : 0;

        // 4. Ratio de mots longs (7+ caractères = vocabulaire riche)
        $longWords = array_filter($meaningfulWords, fn($w) => strlen($w) >= 7);
        $longWordsRatio = $meaningfulWordsCount > 0 ? count($longWords) / $meaningfulWordsCount : 0;

        // Calculer le score
        $score = $criterion->max_score;
        $passed = true;
        $issues = [];
        $suggestions = [];

        // Check 1: Diversité lexicale
        $minDiversity = $rules['min_lexical_diversity'] ?? 0.45;
        $optimalDiversity = $rules['optimal_lexical_diversity'] ?? 0.60;

        if ($lexicalDiversity < $minDiversity) {
            $score *= 0.7;
            $passed = false;
            $issues[] = sprintf("Diversité lexicale faible (%.0f%%)", $lexicalDiversity * 100);
            $suggestions[] = "Utilisez plus de synonymes et variez votre vocabulaire";
        } elseif ($lexicalDiversity < $optimalDiversity) {
            $score *= 0.9;
        }

        // Check 2: Répétitions excessives
        $maxRepetitionAllowed = $rules['max_word_repetition'] ?? 3.0;
        if ($maxRepetition > $maxRepetitionAllowed) {
            $score *= 0.85;
            $passed = false;
            $topWord = array_key_first($topWords);
            $issues[] = sprintf("Mot '%s' trop répété (%.1f%%)", $topWord, $maxRepetition);
            $suggestions[] = "Réduisez la répétition excessive de certains mots, utilisez des synonymes";
        }

        // Check 3: Longueur moyenne des mots
        $minAvgLength = $rules['min_avg_word_length'] ?? 5.0;
        if ($avgWordLength < $minAvgLength) {
            $score *= 0.9;
            $issues[] = sprintf("Vocabulaire simple (moy. %.1f lettres/mot)", $avgWordLength);
            $suggestions[] = "Enrichissez votre vocabulaire avec des termes plus précis et techniques";
        }

        // Check 4: Ratio de mots longs
        $minLongWordsRatio = $rules['min_long_words_ratio'] ?? 0.25;
        if ($longWordsRatio < $minLongWordsRatio) {
            $score *= 0.9;
            $issues[] = sprintf("Peu de mots complexes (%.0f%%)", $longWordsRatio * 100);
            $suggestions[] = "Utilisez un vocabulaire plus riche et spécialisé (mots de 7+ lettres)";
        }

        $score = max(0, $score);

        $feedback = [
            'message' => $passed
                ? sprintf(
                    "Richesse sémantique excellente (diversité: %.0f%%, moy: %.1f lettres)",
                    $lexicalDiversity * 100,
                    $avgWordLength
                )
                : 'Problèmes: ' . implode(', ', $issues),
            'suggestions' => $suggestions
        ];

        $this->saveDetail($criterion, $score, $passed, $feedback, [
            'total_words' => $totalWords,
            'meaningful_words' => $meaningfulWordsCount,
            'unique_words' => $uniqueWordsCount,
            'lexical_diversity' => round($lexicalDiversity * 100, 1),
            'max_word_repetition' => round($maxRepetition, 2),
            'most_repeated_word' => array_key_first($topWords) ?? null,
            'avg_word_length' => round($avgWordLength, 2),
            'long_words_count' => count($longWords),
            'long_words_ratio' => round($longWordsRatio * 100, 1),
            'top_10_words' => $topWords
        ]);
    }

    /**
     * PHASE 6: Analyser la hiérarchie des titres (H1-H6 complet)
     * Vérifie la structure complète et l'ordre logique sans saut de niveau
     */
    protected function analyzeHeadings($htmlContent)
    {
        $criterion = SeoCriterion::where('code', 'headings_hierarchy')->first();
        if (!$criterion) return;

        $dom = new DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8'));

        // Compter tous les niveaux H1-H6
        $headingCounts = [
            'h1' => $dom->getElementsByTagName('h1')->length,
            'h2' => $dom->getElementsByTagName('h2')->length,
            'h3' => $dom->getElementsByTagName('h3')->length,
            'h4' => $dom->getElementsByTagName('h4')->length,
            'h5' => $dom->getElementsByTagName('h5')->length,
            'h6' => $dom->getElementsByTagName('h6')->length,
        ];

        // Extraire l'ordre d'apparition des titres pour vérifier la hiérarchie
        $headingsOrder = $this->extractHeadingsOrder($dom);

        $rules = $criterion->validation_rules;
        $score = $criterion->max_score;
        $passed = true;
        $issues = [];
        $suggestions = [];

        // 1. Vérifier le H1 (doit être exactement 1)
        if ($headingCounts['h1'] !== ($rules['h1_count'] ?? 1)) {
            $score *= 0.7;
            $passed = false;
            if ($headingCounts['h1'] === 0) {
                $issues[] = "Pas de H1";
                $suggestions[] = "Ajoutez un titre H1 unique pour votre article";
            } else {
                $issues[] = "Plusieurs H1 ({$headingCounts['h1']})";
                $suggestions[] = "Utilisez un seul H1 - les autres doivent être H2+";
            }
        }

        // 2. Vérifier le minimum de H2
        if ($headingCounts['h2'] < ($rules['min_h2'] ?? 3)) {
            $score *= 0.85;
            $passed = false;
            $issues[] = "Seulement {$headingCounts['h2']} H2 (minimum: {$rules['min_h2']})";
            $suggestions[] = "Ajoutez plus de sous-titres H2 pour structurer votre contenu";
        }

        // 3. PHASE 6: Vérifier la hiérarchie (pas de saut de niveau)
        if ($rules['check_hierarchy'] ?? true) {
            $hierarchyIssues = $this->checkHeadingsHierarchy($headingsOrder);
            if (!empty($hierarchyIssues)) {
                $score *= 0.9;
                $passed = false;
                $issues = array_merge($issues, $hierarchyIssues);
                $suggestions[] = "Respectez l'ordre logique : H1 > H2 > H3 > H4 > H5 > H6 sans sauter de niveau";
            }
        }

        // 4. Avertissement si trop de niveaux imbriqués
        $maxLevel = $this->getMaxHeadingLevel($headingCounts);
        if ($maxLevel > ($rules['warn_deep_nesting'] ?? 5)) {
            $issues[] = "Profondeur excessive: jusqu'à H{$maxLevel}";
            $suggestions[] = "Évitez d'utiliser plus de 5 niveaux de titres (H1-H5)";
        }

        // Calculer le score final
        $score = max(0, $score);

        $feedback = [
            'message' => $passed
                ? sprintf('Structure H1-H6 correcte (%d niveaux utilisés)', $maxLevel)
                : 'Problèmes: ' . implode(', ', $issues),
            'suggestions' => $suggestions
        ];

        $this->saveDetail($criterion, $score, $passed, $feedback, [
            'h1_count' => $headingCounts['h1'],
            'h2_count' => $headingCounts['h2'],
            'h3_count' => $headingCounts['h3'],
            'h4_count' => $headingCounts['h4'],
            'h5_count' => $headingCounts['h5'],
            'h6_count' => $headingCounts['h6'],
            'max_level' => $maxLevel,
            'hierarchy_valid' => empty($hierarchyIssues ?? []),
            'headings_order' => array_slice($headingsOrder, 0, 10) // Max 10 premiers pour debug
        ]);
    }

    /**
     * Extraire l'ordre d'apparition des titres dans le document
     */
    protected function extractHeadingsOrder($dom)
    {
        $headings = [];
        $xpath = new DOMXPath($dom);

        // Récupérer tous les titres H1-H6 dans l'ordre d'apparition
        $nodes = $xpath->query('//h1 | //h2 | //h3 | //h4 | //h5 | //h6');

        foreach ($nodes as $node) {
            $level = (int) substr($node->nodeName, 1); // h1 -> 1, h2 -> 2, etc.
            $headings[] = [
                'level' => $level,
                'text' => substr(trim($node->textContent), 0, 50) // Premier 50 chars
            ];
        }

        return $headings;
    }

    /**
     * Vérifier qu'il n'y a pas de saut dans la hiérarchie
     */
    protected function checkHeadingsHierarchy($headingsOrder)
    {
        $issues = [];
        $previousLevel = 0;

        foreach ($headingsOrder as $index => $heading) {
            $currentLevel = $heading['level'];

            // Vérifier qu'on ne saute pas de niveau en descendant
            if ($currentLevel > $previousLevel + 1 && $previousLevel > 0) {
                $issues[] = sprintf(
                    "Saut de H%d à H%d (position %d)",
                    $previousLevel,
                    $currentLevel,
                    $index + 1
                );
            }

            $previousLevel = $currentLevel;
        }

        return array_unique($issues);
    }

    /**
     * Obtenir le niveau maximum utilisé
     */
    protected function getMaxHeadingLevel($headingCounts)
    {
        $maxLevel = 0;
        foreach ($headingCounts as $tag => $count) {
            if ($count > 0) {
                $level = (int) substr($tag, 1); // h1 -> 1, h2 -> 2, etc.
                $maxLevel = max($maxLevel, $level);
            }
        }
        return $maxLevel;
    }
    
    /**
     * Analyser les aspects techniques
     */
    protected function analyzeTechnical()
    {
        // Meta Description
        $this->analyzeMetaDescription();
        
        // URL Structure
        $this->analyzeUrlStructure();
        
        // Keyword Density
        $this->analyzeKeywordDensity();
        
        // Internal/External Links
        $this->analyzeLinks();
        
        // Schema Markup avec validation JSON-LD intégrée
        $this->analyzeSchemaMarkup();
        
        // Open Graph
        $this->analyzeOpenGraph();
    }

    /**
     * PHASE 6: Analyser la qualité de la meta description
     * Vérifie longueur, mots-clés, CTA et unicité
     */
    protected function analyzeMetaDescription()
    {
        $criterion = SeoCriterion::where('code', 'meta_description')->first();
        if (!$criterion) return;

        $metaData = $this->article->meta_data ?? [];
        $description = $metaData['description'] ?? '';
        $excerpt = $this->article->excerpt ?? '';
        $title = $this->article->title ?? '';
        $length = strlen($description);

        $rules = $criterion->validation_rules;
        $score = $criterion->max_score;
        $passed = true;
        $issues = [];
        $suggestions = [];
        $qualityChecks = [];

        // 1. Vérifier la longueur (critère de base)
        if ($length === 0) {
            $score = 0;
            $passed = false;
            $issues[] = "Pas de meta description";
            $suggestions[] = "Rédigez une meta description entre {$rules['min']} et {$rules['max']} caractères";
        } elseif ($length < $rules['min'] || $length > $rules['max']) {
            $score *= 0.7;
            $passed = false;
            $issues[] = "Longueur incorrecte ({$length} caractères)";
            $suggestions[] = $length < $rules['min']
                ? "Allongez votre meta description (actuel: {$length}, optimal: {$rules['min']}-{$rules['max']})"
                : "Raccourcissez votre meta description (actuel: {$length}, optimal: {$rules['min']}-{$rules['max']})";
        } else {
            $qualityChecks['length'] = true;
        }

        if ($length > 0) {
            $descriptionLower = strtolower($description);
            $titleLower = strtolower($title);

            // 2. PHASE 6: Vérifier présence mots-clés du titre
            if ($rules['check_keywords'] ?? true) {
                $titleWords = array_filter(
                    str_word_count($titleLower, 1),
                    fn($w) => strlen($w) > 3  // Mots de 4+ caractères
                );

                $keywordsMatched = 0;
                $matchedWords = [];
                foreach ($titleWords as $word) {
                    if (strpos($descriptionLower, $word) !== false) {
                        $keywordsMatched++;
                        $matchedWords[] = $word;
                    }
                }

                $minKeywords = $rules['min_keywords_match'] ?? 2;
                if ($keywordsMatched >= $minKeywords) {
                    $qualityChecks['keywords'] = true;
                } else {
                    $score *= 0.85;
                    $passed = false;
                    $issues[] = "Seulement {$keywordsMatched} mot(s)-clé du titre";
                    $suggestions[] = "Incluez au moins {$minKeywords} mots-clés importants du titre dans la meta description";
                }

                $qualityChecks['keywords_matched'] = $keywordsMatched;
                $qualityChecks['matched_words'] = array_slice($matchedWords, 0, 5);
            }

            // 3. PHASE 6: Vérifier présence CTA (Call To Action)
            if ($rules['check_cta'] ?? true) {
                $ctaWords = $rules['cta_words'] ?? [
                    'découvrez', 'explorez', 'consultez', 'lisez', 'apprenez',
                    'trouvez', 'visitez', 'réservez', 'planifiez', 'préparez'
                ];

                $hasCta = false;
                $ctaFound = [];
                foreach ($ctaWords as $cta) {
                    if (strpos($descriptionLower, $cta) !== false) {
                        $hasCta = true;
                        $ctaFound[] = $cta;
                    }
                }

                if ($hasCta) {
                    $qualityChecks['has_cta'] = true;
                    $qualityChecks['cta_words'] = $ctaFound;
                } else {
                    $score *= 0.9;
                    $issues[] = "Pas de call-to-action";
                    $suggestions[] = "Ajoutez un verbe d'action pour inciter au clic (découvrez, explorez, lisez, etc.)";
                }
            }

            // 4. PHASE 6: Vérifier unicité (différente de l'excerpt)
            if ($rules['check_uniqueness'] ?? true) {
                if (!empty($excerpt)) {
                    $similarity = similar_text($descriptionLower, strtolower($excerpt), $percent);

                    if ($percent > 80) {
                        $score *= 0.9;
                        $issues[] = sprintf("Trop similaire à l'extrait (%.0f%% identique)", $percent);
                        $suggestions[] = "Rédigez une meta description unique, différente de l'extrait";
                    } else {
                        $qualityChecks['unique'] = true;
                    }

                    $qualityChecks['similarity_with_excerpt'] = round($percent, 1);
                } else {
                    $qualityChecks['unique'] = true; // Pas d'excerpt = unique par défaut
                }
            }
        }

        // Calculer le score de qualité global
        $qualityScore = count(array_filter($qualityChecks, fn($v) => $v === true));
        $maxQualityChecks = 4; // length, keywords, cta, unique
        $score = max(0, $score);

        $feedback = [
            'message' => $passed
                ? sprintf("Meta description de qualité (%d/%d checks OK)", $qualityScore, $maxQualityChecks)
                : 'Problèmes: ' . implode(', ', $issues),
            'suggestions' => $suggestions
        ];

        $this->saveDetail($criterion, $score, $passed, $feedback, [
            'length' => $length,
            'quality_checks' => $qualityChecks,
            'quality_score' => $qualityScore,
            'max_checks' => $maxQualityChecks,
            'description_preview' => substr($description, 0, 100)
        ]);
    }
    
    /**
     * Analyser la structure de l'URL
     */
    protected function analyzeUrlStructure()
    {
        $criterion = SeoCriterion::where('code', 'url_structure')->first();
        if (!$criterion) return;
        
        $slug = $this->article->slug;
        $length = strlen($slug);
        $rules = $criterion->validation_rules;
        
        $score = $criterion->max_score;
        $passed = true;
        $issues = [];
        
        if ($length > $rules['max_length']) {
            $score *= 0.7;
            $passed = false;
            $issues[] = "URL trop longue ({$length} caractères)";
        }
        
        if (preg_match('/[_A-Z]/', $slug)) {
            $score *= 0.8;
            $passed = false;
            $issues[] = "URL contient des majuscules ou underscores";
        }
        
        $feedback = [
            'message' => $passed ? 'Structure URL correcte' : 'Problèmes: ' . implode(', ', $issues),
            'suggestions' => $passed ? [] : ['Utilisez des tirets et minuscules uniquement']
        ];
        
        $this->saveDetail($criterion, $score, $passed, $feedback, ['slug' => $slug]);
    }
    
    /**
     * Analyser la densité des mots-clés avec vérification titre-contenu
     */
    protected function analyzeKeywordDensity()
    {
        $criterion = SeoCriterion::where('code', 'keyword_density')->first();
        if (!$criterion) return;
        
        // Pour simplifier, on va analyser les mots les plus fréquents
        $content = strtolower(strip_tags($this->article->content));
        $words = str_word_count($content, 1);
        $totalWords = count($words);
        
        // Enlever les stop words
        $stopWords = ['le', 'la', 'les', 'de', 'du', 'des', 'un', 'une', 'et', 'ou', 'mais'];
        $words = array_diff($words, $stopWords);
        
        $frequency = array_count_values($words);
        arsort($frequency);
        
        $topKeywords = array_slice($frequency, 0, 5, true);
        $densities = [];
        
        foreach ($topKeywords as $keyword => $count) {
            $densities[$keyword] = round(($count / $totalWords) * 100, 2);
        }
        
        $rules = $criterion->validation_rules;
        $score = $criterion->max_score;
        $passed = true;
        
        // Vérifier si la densité est dans la plage optimale
        $mainKeywordDensity = reset($densities) ?: 0;
        if ($mainKeywordDensity < $rules['min'] || $mainKeywordDensity > $rules['max']) {
            $score *= 0.7;
            $passed = false;
        }
        
        // Vérifier que les mots du titre sont dans le contenu
        $suggestions = $passed ? [] : ['Ajustez l\'utilisation de vos mots-clés principaux'];
        $titleWords = array_filter(
            str_word_count(mb_strtolower($this->article->title), 1), 
            fn($w) => strlen($w) > 3
        );
        
        $missingWords = [];
        foreach ($titleWords as $word) {
            if (substr_count($content, $word) < 2) {
                $missingWords[] = $word;
            }
        }
        
        if (!empty($missingWords)) {
            $suggestions[] = "Ces mots du titre manquent dans le contenu : " . implode(', ', array_slice($missingWords, 0, 3));
            $passed = false;
            $score *= 0.9; // Légère pénalité
        }
        
        $this->analysis->keyword_data = [
            'top_keywords' => $topKeywords,
            'densities' => $densities,
            'title_words_missing' => $missingWords
        ];
        
        $feedback = [
            'message' => "Densité mot-clé principal: {$mainKeywordDensity}%",
            'suggestions' => $suggestions
        ];
        
        $this->saveDetail($criterion, $score, $passed, $feedback, $densities);
    }
    
    /**
     * Analyser les liens avec équilibre interne/externe amélioré
     */
    protected function analyzeLinks()
    {
        $dom = new DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($this->article->content, 'HTML-ENTITIES', 'UTF-8'));
        
        $links = $dom->getElementsByTagName('a');
        $internalLinks = 0;
        $externalLinks = 0;
        $nofollowExternal = 0;
        
        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            $rel = $link->getAttribute('rel');
            
            if (strpos($href, 'http') === 0) {
                if (strpos($href, config('app.url')) !== false) {
                    $internalLinks++;
                } else {
                    $externalLinks++;
                    if (strpos($rel, 'nofollow') !== false) {
                        $nofollowExternal++;
                    }
                }
            } else {
                $internalLinks++;
            }
        }
        
        $this->analysis->internal_links_count = $internalLinks;
        $this->analysis->external_links_count = $externalLinks;
        
        // Analyser l'équilibre des liens
        $criterion = SeoCriterion::where('code', 'links_balance')->first();
        if ($criterion) {
            $totalLinks = $internalLinks + $externalLinks;
            $balance = $totalLinks > 0 ? $internalLinks / $totalLinks : 0;
            
            $score = 0;
            $passed = false;
            
            // Pénalité pour manque de liens
            if ($totalLinks < 3) {
                $score = $criterion->max_score * 0.5;
                $passed = false;
            } elseif ($balance >= 0.6 && $balance <= 0.8) {
                // Idéal : 60-80% de liens internes
                $score = $criterion->max_score;
                $passed = true;
            } elseif ($balance >= 0.5 && $balance <= 0.9) {
                $score = $criterion->max_score * 0.7;
                $passed = true;
            } else {
                $score = $criterion->max_score * 0.3;
            }
            
            $feedback = [
                'message' => sprintf(
                    "Équilibre: %d internes (%.0f%%), %d externes (%.0f%%)",
                    $internalLinks,
                    $balance * 100,
                    $externalLinks,
                    (1 - $balance) * 100
                ),
                'suggestions' => !$passed ? [
                    $totalLinks < 3 ? 'Ajoutez au moins 3 liens dans votre article' :
                    ($balance < 0.6 ? 'Ajoutez plus de liens internes vers vos articles' : 
                    'Trop de liens internes, ajoutez quelques sources externes de qualité')
                ] : []
            ];
            
            $this->saveDetail($criterion, $score, $passed, $feedback, [
                'internal' => $internalLinks,
                'external' => $externalLinks,
                'total' => $totalLinks,
                'balance_ratio' => round($balance, 2),
                'nofollow_external' => $nofollowExternal
            ]);
        }
        
        // Analyser liens internes
        $criterion = SeoCriterion::where('code', 'internal_links')->first();
        if ($criterion) {
            $rules = $criterion->validation_rules;
            $score = min($criterion->max_score, ($internalLinks / $rules['optimal']) * $criterion->max_score);
            $passed = $internalLinks >= $rules['min'];
            
            $this->saveDetail($criterion, $score, $passed, [
                'message' => "{$internalLinks} liens internes",
                'suggestions' => $passed ? [] : ['Ajoutez plus de liens internes vers d\'autres articles']
            ], ['count' => $internalLinks]);
        }
        
        // Analyser liens externes
        $criterion = SeoCriterion::where('code', 'external_links')->first();
        if ($criterion) {
            $rules = $criterion->validation_rules;
            $passed = $externalLinks >= $rules['min'] && $externalLinks <= $rules['max'];
            $score = $passed ? $criterion->max_score : $criterion->max_score * 0.5;
            
            $this->saveDetail($criterion, $score, $passed, [
                'message' => "{$externalLinks} liens externes",
                'suggestions' => !$passed ? ['Équilibrez le nombre de liens externes'] : []
            ], ['count' => $externalLinks]);
        }
    }
    
    /**
     * Analyser le Schema Markup avec validation JSON-LD intégrée
     */
    protected function analyzeSchemaMarkup()
    {
        $criterion = SeoCriterion::where('code', 'schema_markup')->first();
        if (!$criterion) return;
        
        // Génération des schemas
        $schemas = $this->detectSchemaTypes();
        
        // Validation intégrée
        $validationResults = $this->validateSchemaStructure($schemas);
        
        $score = $this->calculateSchemaScore($criterion, $schemas, $validationResults);
        $passed = count($schemas) > 0 && count($validationResults['errors']) === 0;
        
        // Stocker les schémas validés
        $this->analysis->schema_markup = $schemas;
        
        $feedback = [
            'message' => $this->generateSchemaFeedbackMessage($schemas, $validationResults),
            'suggestions' => $this->generateSchemaSuggestions($validationResults)
        ];
        
        $this->saveDetail($criterion, $score, $passed, $feedback, [
            'schemas' => $schemas,
            'validation' => $validationResults
        ]);
    }
    
    /**
     * Valider la structure des schemas
     */
    protected function validateSchemaStructure($schemas)
    {
        $errors = [];
        $warnings = [];
        
        foreach ($schemas as $schemaName => $schema) {
            // Vérifications de base
            if (!isset($schema['type'])) {
                $errors[] = "Schema $schemaName: type manquant";
                continue;
            }
            
            // Vérifications spécifiques par type
            switch ($schema['type']) {
                case 'Article':
                    if (!isset($schema['headline'])) {
                        $warnings[] = "Article: headline recommandé";
                    }
                    if (!isset($schema['author'])) {
                        $warnings[] = "Article: author recommandé";
                    }
                    if (!isset($schema['datePublished'])) {
                        $warnings[] = "Article: datePublished recommandé";
                    }
                    break;
                    
                case 'Place':
                    if (!isset($schema['name'])) {
                        $warnings[] = "Place: name recommandé pour une meilleure visibilité";
                    }
                    break;
                    
                case 'Review':
                    if (!isset($schema['reviewBody'])) {
                        $errors[] = "Review: reviewBody requis";
                    }
                    break;
            }
            
            // Test de sérialisation JSON
            $json = json_encode($schema);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $errors[] = "Schema $schemaName: JSON invalide - " . json_last_error_msg();
            }
        }
        
        return ['errors' => $errors, 'warnings' => $warnings];
    }
    
    /**
     * Calculer le score du schema
     */
    protected function calculateSchemaScore($criterion, $schemas, $validationResults)
    {
        if (empty($schemas)) {
            return 0;
        }
        
        $baseScore = $criterion->max_score;
        
        // Pénalités pour erreurs
        if (count($validationResults['errors']) > 0) {
            $baseScore *= 0.3;
        }
        
        // Pénalités mineures pour avertissements
        if (count($validationResults['warnings']) > 0) {
            $baseScore *= 0.8;
        }
        
        return $baseScore;
    }
    
    /**
     * Générer le message de feedback
     */
    protected function generateSchemaFeedbackMessage($schemas, $validationResults)
    {
        $schemaCount = count($schemas);
        $errorCount = count($validationResults['errors']);
        $warningCount = count($validationResults['warnings']);
        
        if ($schemaCount === 0) {
            return 'Aucun schema détecté';
        }
        
        if ($errorCount > 0) {
            return "$schemaCount schemas avec $errorCount erreur(s)";
        }
        
        if ($warningCount > 0) {
            return "$schemaCount schemas avec $warningCount avertissement(s)";
        }
        
        return "$schemaCount schemas valides détectés";
    }
    
    /**
     * Générer les suggestions
     */
    protected function generateSchemaSuggestions($validationResults)
    {
        $suggestions = [];
        
        foreach ($validationResults['errors'] as $error) {
            $suggestions[] = "Erreur: $error";
        }
        
        foreach ($validationResults['warnings'] as $warning) {
            $suggestions[] = "Amélioration: $warning";
        }
        
        if (empty($suggestions)) {
            $suggestions[] = 'Ajoutez des données structurées pour améliorer les rich snippets';
        }
        
        return $suggestions;
    }
    
    /**
     * Analyser l'Open Graph
     */
    protected function analyzeOpenGraph()
    {
        $criterion = SeoCriterion::where('code', 'open_graph')->first();
        if (!$criterion) return;
        
        // Vérifier la présence des éléments essentiels
        $hasTitle = !empty($this->article->title);
        $hasDescription = !empty($this->article->excerpt) || !empty($this->article->meta_data['description'] ?? '');
        $hasImage = !empty($this->article->featured_image);
        
        // Score basé sur les éléments présents
        $elementsCount = 0;
        if ($hasTitle) $elementsCount++;
        if ($hasDescription) $elementsCount++;
        if ($hasImage) $elementsCount++;
        
        $score = ($elementsCount / 3) * $criterion->max_score;
        $passed = $elementsCount >= 2; // Au minimum titre + description
        
        $suggestions = [];
        if (!$hasDescription) $suggestions[] = 'Ajoutez une meta description ou un extrait';
        if (!$hasImage) $suggestions[] = 'Ajoutez une image à la une pour le partage social';
        
        $this->saveDetail($criterion, $score, $passed, [
            'message' => "Open Graph: {$elementsCount}/3 éléments présents",
            'suggestions' => $suggestions
        ], [
            'has_title' => $hasTitle,
            'has_description' => $hasDescription,
            'has_image' => $hasImage
        ]);
    }
    
    /**
     * Analyser les images
     */
    protected function analyzeImages()
    {
        $dom = new DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($this->article->content, 'HTML-ENTITIES', 'UTF-8'));
        
        $images = $dom->getElementsByTagName('img');
        $imageCount = $images->length;
        $this->analysis->images_count = $imageCount;
        
        // Image Count
        $criterion = SeoCriterion::where('code', 'image_count')->first();
        if ($criterion) {
            $rules = $criterion->validation_rules;
            $score = min($criterion->max_score, ($imageCount / $rules['optimal']) * $criterion->max_score);
            $passed = $imageCount >= $rules['min'];
            
            $this->saveDetail($criterion, $score, $passed, [
                'message' => "{$imageCount} images dans l'article",
                'suggestions' => $passed ? [] : ['Ajoutez plus d\'images pour illustrer votre contenu']
            ], ['count' => $imageCount]);
        }
        
        // Alt Text
        $missingAlt = 0;
        foreach ($images as $img) {
            if (!$img->getAttribute('alt')) {
                $missingAlt++;
            }
        }
        
        $criterion = SeoCriterion::where('code', 'image_alt_text')->first();
        if ($criterion && $imageCount > 0) {
            $score = $missingAlt === 0 ? $criterion->max_score : $criterion->max_score * (1 - $missingAlt / $imageCount);
            $passed = $missingAlt === 0;
            
            $this->saveDetail($criterion, $score, $passed, [
                'message' => $missingAlt > 0 ? "{$missingAlt} images sans alt text" : "Toutes les images ont un alt text",
                'suggestions' => $passed ? [] : ['Ajoutez un texte alternatif à toutes les images']
            ], ['missing_alt' => $missingAlt]);
        }
        
        // Analyser l'authenticité des images
        $this->analyzeImageAuthenticity();

        // PHASE 6: Analyser le ratio images/contenu
        $this->analyzeImageRatio();

        // Analyser l'optimisation des images (poids, format)
        $this->analyzeImageOptimization();
    }
    
    /**
     * Analyser l'authenticité des images (version finale)
     */
    protected function analyzeImageAuthenticity()
    {
        $criterion = SeoCriterion::where('code', 'image_quality')->first();
        if (!$criterion) return;
        
        // Stock payant complet
        $paidStockDomains = [
            'shutterstock', 'gettyimages', 'istockphoto', 'depositphotos',
            'adobestock', 'dreamstime', 'alamy', '123rf', 'bigstock', 
            'fotolia', 'stocksy', 'pond5', 'vectorstock'
        ];
        
        // Stock gratuit complet
        $freeStockDomains = [
            'unsplash', 'pexels', 'pixabay', 'freepik', 'burst.shopify',
            'stockvault', 'picjumbo', 'gratisography'
        ];
        
        // Indicateurs d'authenticité
        $authenticityMarkers = [
            'notre voyage', 'notre séjour', 'nous avons', 'j\'ai pris',
            'photo personnelle', 'crédit photo :', '© 20'
        ];
        
        // Extraire UNIQUEMENT les URLs des images
        $imageUrls = [];
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $this->article->content, $matches);
        if (isset($matches[1])) {
            $imageUrls = $matches[1];
        }
        
        // Ajouter l'image à la une si elle existe
        if ($this->article->featured_image) {
            $imageUrls[] = $this->article->featured_image;
        }
        
        $totalImages = count($imageUrls);
        $paidStockCount = 0;
        $freeStockCount = 0;
        
        // Analyser SEULEMENT les URLs d'images
        foreach ($imageUrls as $url) {
            $urlLower = strtolower($url);
            
            foreach ($paidStockDomains as $domain) {
                if (stripos($urlLower, $domain) !== false) {
                    $paidStockCount++;
                    break; // Une image = une source
                }
            }
            
            foreach ($freeStockDomains as $domain) {
                if (stripos($urlLower, $domain) !== false) {
                    $freeStockCount++;
                    break;
                }
            }
        }
        
        // Chercher les marqueurs d'authenticité dans le texte
        $contentText = strip_tags($this->article->content);
        $authenticMarkers = 0;
        foreach ($authenticityMarkers as $marker) {
            if (stripos($contentText, $marker) !== false) {
                $authenticMarkers++;
            }
        }
        
        // Calculer le score avec logique proportionnelle
        $score = $criterion->max_score;
        
        if ($totalImages > 0) {
            $paidStockRatio = $paidStockCount / $totalImages;
            $freeStockRatio = $freeStockCount / $totalImages;
            
            // Réduction proportionnelle, pas absolue
            $score *= (1 - ($paidStockRatio * 0.5)); // -50% max si tout est stock payant
            $score *= (1 - ($freeStockRatio * 0.2)); // -20% max si tout est stock gratuit
            
            // Bonus pour authenticité (max +20%)
            if ($authenticMarkers > 0) {
                $score *= (1 + min(0.2, $authenticMarkers * 0.05));
            }
        }
        
        $score = max(0, min($criterion->max_score, $score));
        $passed = $score >= ($criterion->max_score * 0.6);
        
        $feedback = [
            'message' => $this->generateAuthenticityMessage(
                $paidStockCount, 
                $freeStockCount, 
                $totalImages, 
                $authenticMarkers
            ),
            'suggestions' => !$passed ? [
                'Privilégiez vos propres photos de voyage',
                'Si stock nécessaire, préférez les sources gratuites avec crédit',
                'Mentionnez explicitement vos photos personnelles'
            ] : []
        ];
        
        $this->saveDetail($criterion, $score, $passed, $feedback, [
            'total_images' => $totalImages,
            'paid_stock_count' => $paidStockCount,
            'free_stock_count' => $freeStockCount,
            'authenticity_markers' => $authenticMarkers,
            'paid_stock_ratio' => $totalImages > 0 ? round($paidStockCount / $totalImages * 100, 1) : 0,
            'free_stock_ratio' => $totalImages > 0 ? round($freeStockCount / $totalImages * 100, 1) : 0
        ]);
    }

    /**
     * Générer le message d'authenticité
     */
    private function generateAuthenticityMessage($paid, $free, $total, $markers)
    {
        if ($total == 0) {
            return "Aucune image détectée";
        }
        
        $paidPercent = round($paid / $total * 100);
        $freePercent = round($free / $total * 100);
        $authenticPercent = 100 - $paidPercent - $freePercent;
        
        if ($paidPercent > 50) {
            return "Attention: {$paidPercent}% d'images de stocks payants détectées";
        }
        
        if ($authenticPercent >= 70) {
            return "Excellente authenticité : {$authenticPercent}% d'images originales";
        }
        
        if ($markers > 0) {
            return "Authenticité correcte avec {$markers} marqueur(s) d'expérience personnelle";
        }
        
        return "Mix d'images : {$authenticPercent}% originales, {$freePercent}% stock gratuit, {$paidPercent}% stock payant";
    }

    /**
     * PHASE 6: Analyser le ratio images/contenu
     * Règle: 1 image minimum tous les 300 mots pour une bonne lisibilité
     */
    protected function analyzeImageRatio()
    {
        $criterion = SeoCriterion::where('code', 'image_ratio')->first();
        if (!$criterion) return;

        // Récupérer le nombre de mots et d'images
        $wordCount = $this->analysis->word_count ?? str_word_count(strip_tags($this->article->content));
        $imageCount = $this->analysis->images_count ?? 0;

        $rules = $criterion->validation_rules;
        $wordsPerImage = $rules['words_per_image'] ?? 300;

        // Calculer le nombre d'images recommandé
        $recommendedImages = ceil($wordCount / $wordsPerImage);

        // Calculer le ratio actuel vs optimal
        $actualRatio = $imageCount > 0 ? $wordCount / $imageCount : $wordCount;
        $ratioScore = $recommendedImages > 0 ? min(1, $imageCount / $recommendedImages) : 0;

        // Calculer le score
        $score = 0;
        $passed = false;

        if ($imageCount >= $recommendedImages) {
            // Parfait ou mieux
            $score = $criterion->max_score;
            $passed = true;
        } elseif ($ratioScore >= ($rules['min_ratio'] ?? 0.8)) {
            // Acceptable (80%+ du ratio optimal)
            $score = $criterion->max_score * $ratioScore;
            $passed = true;
        } else {
            // Insuffisant
            $score = $criterion->max_score * $ratioScore * 0.5;
            $passed = false;
        }

        // Générer le feedback
        $feedback = [
            'message' => sprintf(
                "%d image(s) pour %d mots (recommandé: %d images, soit 1/%d mots)",
                $imageCount,
                $wordCount,
                $recommendedImages,
                $wordsPerImage
            ),
            'suggestions' => []
        ];

        if (!$passed) {
            $missingImages = $recommendedImages - $imageCount;
            $feedback['suggestions'][] = sprintf(
                "Ajoutez %d image(s) supplémentaire(s) pour atteindre le ratio optimal de 1 image/%d mots",
                $missingImages,
                $wordsPerImage
            );
            $feedback['suggestions'][] = "Les images améliorent la lisibilité et le temps de lecture";
        } elseif ($imageCount > $recommendedImages * 1.5) {
            // Trop d'images (bonus warning)
            $feedback['suggestions'][] = "Attention: beaucoup d'images. Assurez-vous qu'elles apportent toutes de la valeur";
        }

        $this->saveDetail($criterion, $score, $passed, $feedback, [
            'word_count' => $wordCount,
            'image_count' => $imageCount,
            'recommended_images' => $recommendedImages,
            'actual_ratio' => round($actualRatio, 0),
            'optimal_ratio' => $wordsPerImage,
            'ratio_score' => round($ratioScore * 100, 1)
        ]);
    }

    /**
     * Analyser l'optimisation des images (poids et format)
     * Vérifie que les images sont optimisées pour le web
     */
    protected function analyzeImageOptimization()
    {
        $criterion = SeoCriterion::where('code', 'image_optimization')->first();
        if (!$criterion) return;

        // Extraire les URLs d'images
        $imageUrls = [];
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $this->article->content, $matches);
        if (isset($matches[1])) {
            $imageUrls = $matches[1];
        }

        // Ajouter l'image à la une si elle existe
        if ($this->article->featured_image) {
            $imageUrls[] = $this->article->featured_image;
        }

        $totalImages = count($imageUrls);
        if ($totalImages === 0) {
            // Pas d'images = score neutre
            $this->saveDetail($criterion, $criterion->max_score * 0.5, false, [
                'message' => "Aucune image à optimiser",
                'suggestions' => ['Ajoutez des images pour illustrer votre contenu']
            ]);
            return;
        }

        $rules = $criterion->validation_rules;
        $maxSizeKb = $rules['max_size_kb'] ?? 200;

        $optimizedCount = 0;
        $heavyImages = [];
        $unsupportedFormats = [];
        $totalWeight = 0;

        foreach ($imageUrls as $url) {
            // Vérifier si c'est une URL complète ou relative
            if (strpos($url, 'http') === 0) {
                // URL externe - on skip (on ne peut pas vérifier)
                continue;
            }

            // Construire le chemin vers le fichier
            $filePath = null;
            if (strpos($url, '/storage/') === 0) {
                $filePath = public_path($url);
            } elseif (strpos($url, 'storage/') === 0) {
                $filePath = public_path('/' . $url);
            }

            if ($filePath && file_exists($filePath)) {
                $sizeKb = filesize($filePath) / 1024;
                $totalWeight += $sizeKb;

                // Vérifier le format
                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                $supportedFormats = ['jpg', 'jpeg', 'png', 'webp', 'svg'];

                if (!in_array($extension, $supportedFormats)) {
                    $unsupportedFormats[] = basename($filePath);
                }

                // Vérifier le poids
                if ($sizeKb <= $maxSizeKb) {
                    $optimizedCount++;
                } else {
                    $heavyImages[] = [
                        'name' => basename($filePath),
                        'size_kb' => round($sizeKb, 1)
                    ];
                }
            }
        }

        // Calculer le score
        $checkableImages = $totalImages; // Simplification: on compte toutes les images
        $optimizationRate = $checkableImages > 0 ? $optimizedCount / $checkableImages : 0;
        $score = $criterion->max_score * $optimizationRate;
        $passed = $optimizationRate >= 0.8; // 80% des images doivent être optimisées

        // Générer le feedback
        $message = sprintf(
            "%d/%d images optimisées (poids moyen: %s KB)",
            $optimizedCount,
            $checkableImages,
            $checkableImages > 0 ? round($totalWeight / $checkableImages, 1) : 0
        );

        $suggestions = [];
        if (count($heavyImages) > 0) {
            $suggestions[] = sprintf(
                "%d image(s) dépassent %d KB: utilisez un outil de compression",
                count($heavyImages),
                $maxSizeKb
            );
            $suggestions[] = "Outils recommandés: TinyPNG, ImageOptim, Squoosh";
        }

        if (count($unsupportedFormats) > 0) {
            $suggestions[] = sprintf(
                "%d image(s) dans un format non optimal. Privilégiez WEBP, JPG ou PNG",
                count($unsupportedFormats)
            );
        }

        if ($passed && count($heavyImages) === 0) {
            $suggestions[] = "Excellente optimisation des images !";
        }

        $this->saveDetail($criterion, $score, $passed, [
            'message' => $message,
            'suggestions' => $suggestions
        ], [
            'total_images' => $totalImages,
            'checkable_images' => $checkableImages,
            'optimized_count' => $optimizedCount,
            'heavy_images' => $heavyImages,
            'unsupported_formats' => $unsupportedFormats,
            'avg_weight_kb' => $checkableImages > 0 ? round($totalWeight / $checkableImages, 1) : 0,
            'optimization_rate' => round($optimizationRate * 100, 1)
        ]);
    }

    /**
     * Analyser l'engagement
     */
    protected function analyzeEngagement()
    {
        $content = $this->article->content;

        // CTA Presence
        $this->analyzeCTA($content);

        // Questions to Reader
        $this->analyzeQuestions($content);

        // Scannable Structure (listes, tableaux)
        $this->analyzeScannableStructure($content);

        // Hook Intro
        $this->analyzeHookIntro($content);
    }
    
    /**
     * Analyser les CTA
     */
    protected function analyzeCTA($content)
    {
        $criterion = SeoCriterion::where('code', 'cta_presence')->first();
        if (!$criterion) return;
        
        $ctaPhrases = [
            'cliquez', 'découvrez', 'réservez', 'contactez', 'inscrivez',
            'téléchargez', 'partagez', 'commentez', 'abonnez', 'consultez'
        ];
        
        $ctaCount = 0;
        foreach ($ctaPhrases as $phrase) {
            $ctaCount += substr_count(strtolower($content), $phrase);
        }
        
        $rules = $criterion->validation_rules;
        $score = min($criterion->max_score, ($ctaCount / $rules['min']) * $criterion->max_score);
        $passed = $ctaCount >= $rules['min'];
        
        $this->saveDetail($criterion, $score, $passed, [
            'message' => "{$ctaCount} call-to-action détectés",
            'suggestions' => $passed ? [] : ['Ajoutez des appels à l\'action clairs']
        ], ['count' => $ctaCount]);
    }
    
    /**
     * Analyser les questions
     */
    protected function analyzeQuestions($content)
    {
        $criterion = SeoCriterion::where('code', 'questions_to_reader')->first();
        if (!$criterion) return;

        $questionCount = substr_count($content, '?');

        $rules = $criterion->validation_rules;
        $score = min($criterion->max_score, ($questionCount / $rules['min']) * $criterion->max_score);
        $passed = $questionCount >= $rules['min'];

        $this->saveDetail($criterion, $score, $passed, [
            'message' => "{$questionCount} questions au lecteur",
            'suggestions' => $passed ? [] : ['Posez des questions pour engager vos lecteurs']
        ], ['count' => $questionCount]);
    }

    /**
     * Analyser la structure scannable (listes et tableaux)
     */
    protected function analyzeScannableStructure($content)
    {
        $criterion = SeoCriterion::where('code', 'scannable_structure')->first();
        if (!$criterion) return;

        // Compter les listes à puces
        $ulCount = substr_count($content, '<ul');

        // Compter les listes numérotées
        $olCount = substr_count($content, '<ol');

        // Compter les tableaux
        $tableCount = substr_count($content, '<table');

        $totalScannableElements = $ulCount + $olCount + $tableCount;

        $rules = $criterion->validation_rules;
        $minRequired = $rules['min'] ?? 2;

        // Calculer le score proportionnellement
        if ($totalScannableElements >= $minRequired) {
            $score = $criterion->max_score;
            $passed = true;
        } else {
            $score = ($totalScannableElements / $minRequired) * $criterion->max_score;
            $passed = false;
        }

        $suggestions = [];
        if (!$passed) {
            $suggestions[] = sprintf(
                "Ajoutez au moins %d éléments de structure (listes ou tableaux) pour améliorer la lisibilité",
                $minRequired
            );
            if ($ulCount === 0 && $olCount === 0) {
                $suggestions[] = "Utilisez des listes à puces pour structurer l'information";
            }
            if ($tableCount === 0) {
                $suggestions[] = "Les tableaux sont excellents pour comparer des données";
            }
        }

        $message = sprintf(
            "%d éléments scannables détectés (%d listes à puces, %d listes numérotées, %d tableaux)",
            $totalScannableElements,
            $ulCount,
            $olCount,
            $tableCount
        );

        $this->saveDetail($criterion, $score, $passed, [
            'message' => $message,
            'suggestions' => $suggestions
        ], [
            'total_scannable' => $totalScannableElements,
            'ul_count' => $ulCount,
            'ol_count' => $olCount,
            'table_count' => $tableCount,
            'min_required' => $minRequired
        ]);
    }

    /**
     * Analyser l'accroche d'introduction
     */
    protected function analyzeHookIntro($content)
    {
        $criterion = SeoCriterion::where('code', 'hook_intro')->first();
        if (!$criterion) return;
        
        $plainContent = strip_tags($content);
        $words = str_word_count($plainContent, 1);
        $first150Words = implode(' ', array_slice($words, 0, 150));
        
        // Analyse simplifiée : vérifier si l'intro contient des éléments engageants
        $hookElements = ['saviez-vous', 'imaginez', 'découvrez', 'incroyable', 'unique'];
        $hasHook = false;
        
        foreach ($hookElements as $element) {
            if (stripos($first150Words, $element) !== false) {
                $hasHook = true;
                break;
            }
        }
        
        $score = $hasHook ? $criterion->max_score : $criterion->max_score * 0.6;
        $passed = $hasHook;
        
        $this->saveDetail($criterion, $score, $passed, [
            'message' => $hasHook ? 'Introduction accrocheuse' : 'Introduction peut être améliorée',
            'suggestions' => $passed ? [] : ['Rendez votre introduction plus captivante']
        ], ['has_hook' => $hasHook]);
    }
    
    /**
     * Analyser l'authenticité
     */
    protected function analyzeAuthenticity()
    {
        // Pour les partenaires, vérifier l'auto-promo
        if ($this->writerType === 'partner') {
            $this->analyzeAutoPromo();
        }

        // Emotional Words
        $this->analyzeEmotionalWords();

        // Personal Experience
        $this->analyzePersonalExperience();

        // Geographic Coherence
        $this->analyzeGeoCoherence();

        // Source Quality
        $this->analyzeSourceQuality();
    }
    
    /**
     * Analyser l'auto-promotion (partenaires) - PHASE 3 STRICT
     *
     * Détecte les liens auto-promotionnels (vers le domaine du partenaire)
     * et calcule le pourcentage réel : (liens auto-promo / total liens externes) * 100
     *
     * Pour les partenaires : MAX 20% de liens auto-promo tolérés
     */
    protected function analyzeAutoPromo()
    {
        $criterion = SeoCriterion::where('code', 'auto_promo_limit')->first();
        if (!$criterion) return;

        // Extraire tous les liens externes
        $dom = new DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($this->article->content, 'HTML-ENTITIES', 'UTF-8'));

        $links = $dom->getElementsByTagName('a');
        $externalLinks = [];
        $autoPromoLinks = [];
        $partnerDomains = [];

        // Récupérer les domaines du partenaire depuis partner_offer_url
        if ($this->user->writer_type === User::WRITER_TYPE_PARTNER && $this->user->partner_offer_url) {
            $offerDomain = $this->extractDomain($this->user->partner_offer_url);
            if ($offerDomain) {
                $partnerDomains[] = $offerDomain;
            }
        }

        // Analyser tous les liens
        foreach ($links as $link) {
            $href = $link->getAttribute('href');

            // Ignorer liens internes et ancres
            if (empty($href) || strpos($href, '#') === 0 || strpos($href, '/') === 0) {
                continue;
            }

            // Ignorer liens vers le site Nomadie
            if (strpos($href, config('app.url')) !== false) {
                continue;
            }

            // C'est un lien externe
            if (strpos($href, 'http') === 0) {
                $linkDomain = $this->extractDomain($href);
                $externalLinks[] = [
                    'url' => $href,
                    'domain' => $linkDomain,
                    'text' => $link->textContent
                ];

                // Vérifier si c'est un lien auto-promo
                foreach ($partnerDomains as $partnerDomain) {
                    if ($linkDomain === $partnerDomain || strpos($linkDomain, $partnerDomain) !== false) {
                        $autoPromoLinks[] = [
                            'url' => $href,
                            'domain' => $linkDomain,
                            'text' => $link->textContent
                        ];
                        break;
                    }
                }
            }
        }

        // Calculer le pourcentage réel d'auto-promo
        $totalExternal = count($externalLinks);
        $totalAutoPromo = count($autoPromoLinks);

        // Pourcentage = (liens auto-promo / total liens externes) * 100
        $autoPromoPercentage = 0;
        if ($totalExternal > 0) {
            $autoPromoPercentage = ($totalAutoPromo / $totalExternal) * 100;
        } elseif ($totalAutoPromo > 0) {
            // Si aucun lien externe SAUF auto-promo = 100%
            $autoPromoPercentage = 100;
        }

        // Détecter aussi les mots-clés auto-promo dans le texte
        $content = strtolower(strip_tags($this->article->content));
        $promoKeywords = [
            'notre offre', 'nos services', 'nous proposons', 'contactez-nous',
            'réservez avec nous', 'notre équipe', 'notre agence', 'nos tarifs',
            'notre site', 'notre plateforme', 'chez nous'
        ];

        $promoKeywordCount = 0;
        foreach ($promoKeywords as $keyword) {
            $promoKeywordCount += substr_count($content, $keyword);
        }

        // Bonus pénalité si beaucoup de mots-clés promo (max +10%)
        if ($promoKeywordCount > 5) {
            $autoPromoPercentage += min(10, ($promoKeywordCount - 5) * 2);
        }

        $autoPromoPercentage = min(100, round($autoPromoPercentage, 1));

        // Sauvegarder dans l'analyse
        $this->analysis->has_auto_promo = $totalAutoPromo > 0 || $promoKeywordCount > 0;
        $this->analysis->auto_promo_percentage = $autoPromoPercentage;

        // Vérifier si le critère est respecté
        $rules = $criterion->validation_rules;
        $maxAllowed = $rules['max_percentage'] ?? 20;
        $passed = $autoPromoPercentage <= $maxAllowed;

        // Calculer le score
        if ($passed) {
            $score = $criterion->max_score;
        } else {
            // Pénalité proportionnelle au dépassement
            $excess = $autoPromoPercentage - $maxAllowed;
            $score = max(0, $criterion->max_score * (1 - ($excess / 100)));
        }

        $message = sprintf(
            "Auto-promotion: %.1f%% (%d lien(s) promo / %d externes)",
            $autoPromoPercentage,
            $totalAutoPromo,
            $totalExternal
        );

        if ($promoKeywordCount > 0) {
            $message .= " + {$promoKeywordCount} mot(s)-clé promo";
        }

        $suggestions = [];
        if (!$passed) {
            $suggestions[] = sprintf(
                'LIMITE DÉPASSÉE : %.1f%% auto-promo (max autorisé: %d%% pour partenaires)',
                $autoPromoPercentage,
                $maxAllowed
            );
            $suggestions[] = 'Supprimez des liens vers votre domaine ou ajoutez plus de liens externes neutres';

            if ($totalExternal === $totalAutoPromo && $totalAutoPromo > 0) {
                $suggestions[] = 'CRITIQUE : Tous vos liens externes sont auto-promotionnels !';
            }
        }

        $this->saveDetail($criterion, $score, $passed, [
            'message' => $message,
            'suggestions' => $suggestions
        ], [
            'percentage' => $autoPromoPercentage,
            'auto_promo_links_count' => $totalAutoPromo,
            'external_links_count' => $totalExternal,
            'promo_keywords_count' => $promoKeywordCount,
            'auto_promo_links' => array_slice($autoPromoLinks, 0, 5), // Max 5 exemples
            'partner_domains' => $partnerDomains
        ]);
    }

    /**
     * Extraire le domaine d'une URL
     */
    protected function extractDomain($url)
    {
        $parsed = parse_url($url);
        if (!isset($parsed['host'])) {
            return null;
        }

        $host = $parsed['host'];

        // Retirer www.
        $host = preg_replace('/^www\./', '', $host);

        return strtolower($host);
    }
    
    /**
     * Analyser les mots émotionnels
     */
    protected function analyzeEmotionalWords()
    {
        $criterion = SeoCriterion::where('code', 'emotional_words')->first();
        if (!$criterion) return;
        
        $emotionalWords = [
            'incroyable', 'magnifique', 'époustouflant', 'merveilleux', 'unique',
            'inoubliable', 'magique', 'extraordinaire', 'fascinant', 'sublime',
            'authentique', 'chaleureux', 'passionnant', 'émouvant', 'surprenant'
        ];
        
        $content = strtolower($this->article->content);
        $count = 0;
        
        foreach ($emotionalWords as $word) {
            $count += substr_count($content, $word);
        }
        
        $rules = $criterion->validation_rules;
        $score = min($criterion->max_score, ($count / $rules['min_count']) * $criterion->max_score);
        $passed = $count >= $rules['min_count'];
        
        $this->saveDetail($criterion, $score, $passed, [
            'message' => "{$count} mots émotionnels détectés",
            'suggestions' => $passed ? [] : ['Utilisez plus de mots qui créent une connexion émotionnelle']
        ], ['count' => $count]);
    }
    
    /**
     * Analyser l'expérience personnelle
     */
    protected function analyzePersonalExperience()
    {
        $criterion = SeoCriterion::where('code', 'personal_experience')->first();
        if (!$criterion) return;
        
        $personalIndicators = [
            'j\'ai', 'nous avons', 'mon expérience', 'personnellement',
            'lors de mon voyage', 'quand j\'étais', 'ma visite', 'notre séjour'
        ];
        
        $content = strtolower($this->article->content);
        $count = 0;
        
        foreach ($personalIndicators as $indicator) {
            $count += substr_count($content, $indicator);
        }
        
        $rules = $criterion->validation_rules;
        $score = min($criterion->max_score, ($count / $rules['min_mentions']) * $criterion->max_score);
        $passed = $count >= $rules['min_mentions'];
        
        $this->saveDetail($criterion, $score, $passed, [
            'message' => "{$count} mentions d'expérience personnelle",
            'suggestions' => $passed ? [] : ['Partagez plus d\'anecdotes et expériences personnelles']
        ], ['count' => $count]);
    }

    /**
     * Analyser la cohérence géographique
     */
    protected function analyzeGeoCoherence()
    {
        $criterion = SeoCriterion::where('code', 'geo_coherence')->first();
        if (!$criterion) return;

        $content = $this->article->content;
        $plainContent = strip_tags($content);

        // Mots-clés de localisation
        $locationKeywords = [
            'ville', 'pays', 'région', 'quartier', 'arrondissement',
            'capitale', 'province', 'département', 'canton', 'district',
            'nord', 'sud', 'est', 'ouest', 'centre',
            'km', 'kilomètres', 'mètres', 'altitude'
        ];

        // Indicateurs de localisation précise
        $preciseLocationIndicators = [
            'situé à', 'se trouve à', 'localisé à', 'dans le', 'près de',
            'à proximité de', 'au cœur de', 'au centre de', 'coordonnées',
            'latitude', 'longitude', 'adresse'
        ];

        $locationScore = 0;
        $preciseLocationScore = 0;

        foreach ($locationKeywords as $keyword) {
            $locationScore += substr_count(strtolower($plainContent), $keyword);
        }

        foreach ($preciseLocationIndicators as $indicator) {
            $preciseLocationScore += substr_count(strtolower($plainContent), $indicator);
        }

        // Détecter les noms propres potentiels (mots commençant par une majuscule)
        preg_match_all('/\b[A-ZÀÂÄÇÉÈÊËÏÎÔÙÛÜ][a-zàâäçéèêëïîôùûüÿ]{2,}\b/', $plainContent, $properNouns);
        $properNounCount = count($properNouns[0] ?? []);

        $totalGeoReferences = $locationScore + $preciseLocationScore;

        // Calculer le score
        if ($totalGeoReferences >= 10 && $properNounCount >= 5) {
            $score = $criterion->max_score;
            $passed = true;
        } elseif ($totalGeoReferences >= 5 || $properNounCount >= 3) {
            $score = $criterion->max_score * 0.7;
            $passed = true;
        } else {
            $score = $criterion->max_score * 0.4;
            $passed = false;
        }

        $suggestions = [];
        if (!$passed) {
            $suggestions[] = "Ajoutez plus de références géographiques précises (noms de lieux, distances, directions)";
            if ($preciseLocationScore < 3) {
                $suggestions[] = "Utilisez des indicateurs de localisation: 'situé à', 'se trouve à', 'près de', etc.";
            }
            if ($properNounCount < 3) {
                $suggestions[] = "Mentionnez des noms de lieux spécifiques (villes, monuments, quartiers)";
            }
        }

        $message = sprintf(
            "%d références géographiques détectées (%d noms de lieux)",
            $totalGeoReferences,
            $properNounCount
        );

        $this->saveDetail($criterion, $score, $passed, [
            'message' => $message,
            'suggestions' => $suggestions
        ], [
            'location_keywords' => $locationScore,
            'precise_indicators' => $preciseLocationScore,
            'proper_nouns' => $properNounCount,
            'total_references' => $totalGeoReferences
        ]);
    }

    /**
     * Analyser la qualité des sources
     */
    protected function analyzeSourceQuality()
    {
        $criterion = SeoCriterion::where('code', 'source_quality')->first();
        if (!$criterion) return;

        $content = $this->article->content;

        // Extraire les liens externes
        preg_match_all('/<a[^>]+href=["\']([^"\']+)["\'][^>]*>/i', $content, $linkMatches);
        $links = $linkMatches[1] ?? [];

        // Filtrer les liens externes (pas les liens internes ni #)
        $externalLinks = array_filter($links, function($link) {
            return strpos($link, 'http') === 0 &&
                   strpos($link, config('app.url')) === false &&
                   strpos($link, '#') !== 0;
        });

        // Domaines de qualité reconnus
        $qualityDomains = [
            'wikipedia.org', 'gov', '.edu', 'unesco.org',
            'nationalgeographic', 'lonelyplanet', 'routard.com',
            'tripadvisor', 'booking.com', 'airbnb'
        ];

        $qualityLinksCount = 0;
        foreach ($externalLinks as $link) {
            foreach ($qualityDomains as $domain) {
                if (stripos($link, $domain) !== false) {
                    $qualityLinksCount++;
                    break;
                }
            }
        }

        // Indicateurs de citations
        $citationIndicators = [
            'selon', 'd\'après', 'source', 'étude', 'recherche',
            'rapport', 'données', 'statistiques', 'chiffres officiels'
        ];

        $citationCount = 0;
        $plainContent = strip_tags($content);
        foreach ($citationIndicators as $indicator) {
            $citationCount += substr_count(strtolower($plainContent), $indicator);
        }

        $totalExternalLinks = count($externalLinks);

        // Calculer le score
        if ($qualityLinksCount >= 2 && $citationCount >= 3) {
            $score = $criterion->max_score;
            $passed = true;
        } elseif ($qualityLinksCount >= 1 || $citationCount >= 2) {
            $score = $criterion->max_score * 0.7;
            $passed = true;
        } else {
            $score = $criterion->max_score * 0.4;
            $passed = false;
        }

        $suggestions = [];
        if (!$passed) {
            $suggestions[] = "Citez des sources fiables pour appuyer vos informations";
            if ($qualityLinksCount === 0) {
                $suggestions[] = "Ajoutez des liens vers des sources reconnues (sites officiels, Wikipedia, National Geographic, etc.)";
            }
            if ($citationCount < 2) {
                $suggestions[] = "Utilisez des formules comme 'selon', 'd'après', 'source' pour référencer vos informations";
            }
        }

        $message = sprintf(
            "%d source(s) de qualité, %d citation(s) détectée(s)",
            $qualityLinksCount,
            $citationCount
        );

        $this->saveDetail($criterion, $score, $passed, [
            'message' => $message,
            'suggestions' => $suggestions
        ], [
            'quality_links' => $qualityLinksCount,
            'total_external_links' => $totalExternalLinks,
            'citation_count' => $citationCount
        ]);
    }

    /**
     * Calculer les scores par catégorie
     */
    protected function calculateScores()
    {
        $categories = ['content', 'technical', 'images', 'engagement', 'authenticity'];
        $categoryScores = [];
        
        foreach ($categories as $category) {
            $details = $this->analysis->details()->whereHas('criterion', function($q) use ($category) {
                $q->where('category', $category);
            })->get();
            
            $totalScore = 0;
            $totalMaxScore = 0;
            $totalWeight = 0;
            
            foreach ($details as $detail) {
                $config = $this->configurations[$detail->criterion->code] ?? null;
                $weight = $config ? $config->weight : 1.0;
                
                $totalScore += $detail->score * $weight;
                $totalMaxScore += $detail->criterion->max_score * $weight;
                $totalWeight += $weight;
            }
            
            if ($totalMaxScore > 0) {
                $categoryScore = ($totalScore / $totalMaxScore) * 100;
            } else {
                $categoryScore = 0;
            }
            
            $categoryScores[$category] = round($categoryScore, 2);
        }
        
        // Sauvegarder les scores
        $this->analysis->content_score = $categoryScores['content'] ?? 0;
        $this->analysis->technical_score = $categoryScores['technical'] ?? 0;
        $this->analysis->images_score = $categoryScores['images'] ?? 0;
        $this->analysis->engagement_score = $categoryScores['engagement'] ?? 0;
        $this->analysis->authenticity_score = $categoryScores['authenticity'] ?? 0;
        
        // Calculer le score global
        $this->analysis->calculateGlobalScore();
        $this->analysis->save();
    }
    
    /**
     * Générer les suggestions de maillage
     */
    protected function generateSuggestions()
    {
        // Déléguer à la méthode statique du modèle
        SeoSuggestion::generateSuggestionsFor($this->analysis);
    }
    
    /**
     * Vérifier le statut dofollow
     */
    protected function checkDoFollowStatus()
    {
        $this->analysis->checkDoFollowEligibility();
    }
    
    /**
     * Sauvegarder un détail d'analyse
     */
    protected function saveDetail($criterion, $score, $passed, $feedback, $data = null)
    {
        SeoAnalysisDetail::updateOrCreate(
            [
                'seo_analysis_id' => $this->analysis->id,
                'criterion_id' => $criterion->id
            ],
            [
                'score' => round($score, 2),
                'passed' => $passed,
                'feedback' => $feedback,
                'data' => $data
            ]
        );
    }
    
    /**
     * Détecter les types de schema appropriés
     */
    protected function detectSchemaTypes()
    {
        $content = strtolower($this->article->content);
        $title = strtolower($this->article->title);
        $schemas = [];
        
        // Article (toujours)
        $schemas['Article'] = [
            'type' => 'Article',
            'headline' => $this->article->title,
            'author' => $this->user->name,
            'datePublished' => $this->article->published_at
        ];
        
        // Place (si destination)
        $locationKeywords = ['voyage', 'visiter', 'destination', 'séjour', 'découvrir'];
        foreach ($locationKeywords as $keyword) {
            if (stripos($title . ' ' . $content, $keyword) !== false) {
                $schemas['Place'] = ['type' => 'Place'];
                break;
            }
        }
        
        // Review (si avis)
        if (stripos($title, 'avis') !== false || stripos($title, 'test') !== false) {
            $schemas['Review'] = ['type' => 'Review'];
        }
        
        // Event (si événement)
        if (stripos($content, 'festival') !== false || stripos($content, 'événement') !== false) {
            $schemas['Event'] = ['type' => 'Event'];
        }
        
        return $schemas;
    }
}