<?php

namespace App\Services;

use App\Models\Comment;

class ImprovedSpamDetector
{
    // Mots spam adaptés au contexte français
    protected array $spamWords = [
        // Casino/Jeux d'argent
        'casino' => 4, 'poker' => 4, 'pari' => 3, 'jackpot' => 4, 
        'roulette' => 3, 'mise' => 2, 'gains faciles' => 4,
        
        // Finance frauduleuse
        'prêt rapide' => 4, 'crédit facile' => 4, 'argent rapidement' => 4,
        'revenus passifs' => 3, 'trading miracle' => 4, 'crypto facile' => 4,
        'investissement garanti' => 4,
        
        // Santé frauduleuse
        'maigrir vite' => 3, 'perdre 10kg' => 3, 'pilule miracle' => 4,
        'remède naturel' => 2, 'guérir cancer' => 4,
        
        // Travail à domicile
        'travail domicile' => 2, 'gagner facilement' => 3, 
        'revenus automatiques' => 3, '1000€ par jour' => 4,
        
        // Patterns marketing agressif
        'cliquez ici' => 2, 'offre limitée' => 2, 'profitez maintenant' => 2,
        'dernière chance' => 2, 'gratuit' => 1, 'promotion' => 1,
        
        // Spam généraliste
        'viagra' => 4, 'cialis' => 4, 'rencontre' => 2, 'sexy' => 3
    ];
    
    protected array $suspiciousPatterns = [
        '/\b(?:https?:\/\/)[^\s]{70,}/i' => 3,    // URLs très longues
        '/\b[A-Z]{7,}\b/' => 2,                   // Mots en majuscules
        '/(.)\1{6,}/' => 3,                       // Répétitions excessives
        '/\d{15,}/' => 3,                         // Très longues séries de chiffres
        '/[€$£¥]{2,}/' => 2,                      // Symboles monétaires répétés
        '/\b(?:www\.)?[a-z0-9-]+\.(?:tk|ml|ga|cf|xyz)\b/i' => 4, // Domaines suspects
        '/[!?]{4,}/' => 2,                        // Ponctuation excessive
        '/\b[0-9]{4}-[0-9]{4}-[0-9]{4}-[0-9]{4}\b/' => 4 // Numéros de carte
    ];

    // Domaines d'emails temporaires
    protected array $tempEmailDomains = [
        'yopmail.com', 'mailinator.com', 'guerrillamail.com', 
        '10minutemail.com', 'temp-mail.org', 'jetable.org',
        'poubelle-email.com', 'kasmail.com', 'maildrop.cc',
        'throwaway.email', 'getnada.com'
    ];

    public function analyzeComment(string $content, ?string $email = null, ?string $ip = null, ?array 
$userHistory = null): array
    {
        $analysis = [
            'score' => 0,
            'flags' => [],
            'confidence' => 'low',
            'recommendation' => 'approve',
            'reasons' => []
        ];

        // 1. Analyse du contenu
        $contentAnalysis = $this->analyzeContent($content);
        $analysis['score'] += $contentAnalysis['score'];
        $analysis['flags'] = array_merge($analysis['flags'], $contentAnalysis['flags']);

        // 2. Analyse de l'email
        if ($email) {
            $emailAnalysis = $this->analyzeEmail($email);
            $analysis['score'] += $emailAnalysis['score'];
            $analysis['flags'] = array_merge($analysis['flags'], $emailAnalysis['flags']);
        }

        // 3. Analyse IP et historique
        if ($ip) {
            $ipAnalysis = $this->analyzeIP($ip);
            $analysis['score'] += $ipAnalysis['score'];
            $analysis['flags'] = array_merge($analysis['flags'], $ipAnalysis['flags']);
        }

        // 4. Bonus de confiance utilisateur
        if ($userHistory) {
            $trustBonus = $this->calculateTrustBonus($userHistory);
            $analysis['score'] -= $trustBonus;
            if ($trustBonus > 0) {
                $analysis['flags'][] = "Utilisateur de confiance (-{$trustBonus} points)";
            }
        }

        // 5. Déterminer la recommandation finale
        $analysis = $this->determineRecommendation($analysis);

        return $analysis;
    }

    protected function analyzeContent(string $content): array
    {
        $score = 0;
        $flags = [];
        
        $contentLower = mb_strtolower($content);
        $length = mb_strlen($content);

        // Vérification des mots spam avec pondération
        foreach ($this->spamWords as $word => $weight) {
            if (mb_strpos($contentLower, $word) !== false) {
                $score += $weight;
                $flags[] = "Terme suspect: {$word} (+{$weight})";
            }
        }

        // Patterns suspects
        foreach ($this->suspiciousPatterns as $pattern => $weight) {
            if (preg_match($pattern, $content)) {
                $score += $weight;
                $flags[] = "Pattern suspect détecté (+{$weight})";
            }
        }

        // Analyse des URLs
        preg_match_all('/https?:\/\/[^\s]+/', $content, $urls);
        $urlCount = count($urls[0]);
        if ($urlCount > 1) {
            $penalty = min($urlCount * 2, 8);
            $score += $penalty;
            $flags[] = "Liens multiples: {$urlCount} (+{$penalty})";
        }

        // Analyse de la longueur
        if ($length < 5) {
            $score += 4;
            $flags[] = "Contenu très court (+4)";
        } elseif ($length > 3000) {
            $score += 3;
            $flags[] = "Contenu très long (+3)";
        }

        // Ratio majuscules/minuscules
        $uppercaseCount = preg_match_all('/[A-Z]/', $content);
        if ($length > 10) {
            $uppercaseRatio = $uppercaseCount / $length;
            if ($uppercaseRatio > 0.3) {
                $score += 3;
                $flags[] = "Trop de majuscules (+3)";
            }
        }

        // Détection de texte répétitif
        if ($this->hasRepetitiveContent($content)) {
            $score += 3;
            $flags[] = "Contenu répétitif (+3)";
        }

        return ['score' => $score, 'flags' => $flags];
    }

    protected function analyzeEmail(string $email): array
    {
        $score = 0;
        $flags = [];
        
        $domain = substr(strrchr($email, "@"), 1);

        // Emails temporaires
        if (in_array($domain, $this->tempEmailDomains)) {
            $score += 5;
            $flags[] = "Email temporaire (+5)";
        }

        // Domaines suspects
        if (preg_match('/\.(tk|ml|ga|cf|xyz)$/i', $domain)) {
            $score += 3;
            $flags[] = "Domaine email suspect (+3)";
        }

        // Pattern email générique (ex: user123456@gmail.com)
        if (preg_match('/^[a-z]+\d{4,}@/i', $email)) {
            $score += 2;
            $flags[] = "Pattern email générique (+2)";
        }

        // Email très court ou très long
        $emailLength = strlen($email);
        if ($emailLength < 6) {
            $score += 3;
            $flags[] = "Email trop court (+3)";
        } elseif ($emailLength > 50) {
            $score += 2;
            $flags[] = "Email très long (+2)";
        }

        return ['score' => $score, 'flags' => $flags];
    }

    protected function analyzeIP(string $ip): array
    {
        $score = 0;
        $flags = [];

        // Vérifier l'historique de spam de cette IP
        $spamHistory = Comment::where('ip_address', $ip)
            ->where('status', 'spam')
            ->where('created_at', '>', now()->subDays(30))
            ->count();

        if ($spamHistory > 0) {
            $penalty = min($spamHistory * 4, 12);
            $score += $penalty;
            $flags[] = "IP avec historique spam: {$spamHistory} (+{$penalty})";
        }

        // Rate limiting sophistiqué
        $recentComments = Comment::where('ip_address', $ip)
            ->where('created_at', '>', now()->subHour())
            ->count();

        if ($recentComments >= 5) {
            $score += 6;
            $flags[] = "Trop de commentaires récents: {$recentComments} (+6)";
        }

        // Commentaires rejetés récemment
        $recentRejected = Comment::where('ip_address', $ip)
            ->whereIn('status', ['spam', 'trash'])
            ->where('created_at', '>', now()->subDays(7))
            ->count();

        if ($recentRejected > 0) {
            $penalty = $recentRejected * 2;
            $score += $penalty;
            $flags[] = "Commentaires rejetés récents: {$recentRejected} (+{$penalty})";
        }

        return ['score' => $score, 'flags' => $flags];
    }

    protected function calculateTrustBonus(array $userHistory): int
    {
        $bonus = 0;

        // Utilisateur avec des articles approuvés
        if (($userHistory['approved_articles'] ?? 0) > 0) {
            $bonus += 4;
        }

        // Commentaires approuvés dans le passé
        $approvedComments = $userHistory['approved_comments'] ?? 0;
        if ($approvedComments > 2) {
            $bonus += min($approvedComments, 6);
        }

        // Ancienneté du compte
        $accountAge = $userHistory['account_age_days'] ?? 0;
        if ($accountAge > 30) {
            $bonus += 2;
        }
        if ($accountAge > 365) {
            $bonus += 2; // Bonus supplémentaire pour les comptes anciens
        }

        return $bonus;
    }

    protected function determineRecommendation(array $analysis): array
    {
        $score = $analysis['score'];

        if ($score >= 12) {
            $analysis['recommendation'] = 'reject';
            $analysis['confidence'] = 'high';
            $analysis['reasons'][] = 'Score de spam très élevé';
        } elseif ($score >= 8) {
            $analysis['recommendation'] = 'moderate';
            $analysis['confidence'] = 'high';
            $analysis['reasons'][] = 'Score de spam élevé';
        } elseif ($score >= 4) {
            $analysis['recommendation'] = 'moderate';
            $analysis['confidence'] = 'medium';
            $analysis['reasons'][] = 'Score de spam modéré';
        } elseif ($score >= 2) {
            $analysis['recommendation'] = 'moderate';
            $analysis['confidence'] = 'low';
            $analysis['reasons'][] = 'Quelques indicateurs suspects';
        } else {
            $analysis['recommendation'] = 'approve';
            $analysis['confidence'] = 'high';
            $analysis['reasons'][] = 'Commentaire légitime';
        }

        return $analysis;
    }

    protected function hasRepetitiveContent(string $content): bool
    {
        // Détecter si le même mot/phrase est répété anormalement
        $words = str_word_count($content, 1);
        if (count($words) < 5) return false;
        
        $wordCounts = array_count_values($words);
        $maxCount = max($wordCounts);
        $totalWords = count($words);
        
        // Si un mot représente plus de 30% du contenu
        return ($maxCount / $totalWords) > 0.3;
    }
}
