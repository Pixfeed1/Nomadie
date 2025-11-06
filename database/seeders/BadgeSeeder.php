<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Badge;

class BadgeSeeder extends Seeder
{
    public function run()
    {
        $badges = [
            [
                'code' => 'premier_pas',
                'name' => 'Premier Pas',
                'description' => 'Publication de votre 1er article validé par NomadSEO',
                'icon' => '🌱',
                'color' => 'green',
                'order' => 1,
                'conditions' => [
                    'type' => 'articles_published',
                    'count' => 1,
                    'min_score' => 78
                ],
                'rewards' => [
                    'seo_tool_access' => 'Accès complet à l\'outil NomadSEO gratuit',
                    'profile_visibility' => 'Visibilité sur votre profil Nomadie'
                ]
            ],
            [
                'code' => 'contributeur_confirme',
                'name' => 'Contributeur Confirmé',
                'description' => '5 articles publiés avec score NomadSEO ≥ 75/100',
                'icon' => '✍️',
                'color' => 'blue',
                'order' => 2,
                'conditions' => [
                    'type' => 'articles_published',
                    'count' => 5,
                    'min_score' => 75,
                    'social_share' => true
                ],
                'rewards' => [
                    'badge_kit' => 'Badge intégrable sur votre site personnel',
                    'monthly_mention' => 'Mention dans la section "Contributeurs du mois"',
                    'priority_suggestions' => 'Priorité dans les suggestions de contenu'
                ]
            ],
            [
                'code' => 'redacteur_expert',
                'name' => 'Rédacteur Expert',
                'description' => '20 articles publiés avec maintien de la qualité',
                'icon' => '📖',
                'color' => 'purple',
                'order' => 3,
                'conditions' => [
                    'type' => 'articles_published',
                    'count' => 20,
                    'min_score' => 75,
                    'min_months' => 6
                ],
                'rewards' => [
                    'expert_status' => 'Statut de référence dans votre domaine',
                    'event_invitations' => 'Invitations aux événements communautaires',
                    'topic_proposals' => 'Possibilité de proposer des sujets'
                ]
            ],
            [
                'code' => 'dofollow_debloque',
                'name' => 'Dofollow Débloqué',
                'description' => 'Premier article qualifié pour liens dofollow',
                'icon' => '🎯',
                'color' => 'indigo',
                'order' => 4,
                'conditions' => [
                    'type' => 'dofollow_achieved',
                    'min_score' => 78
                ],
                'rewards' => [
                    'dofollow_links' => 'Backlinks dofollow sur tous vos articles',
                    'seo_boost' => 'Boost SEO significatif',
                    'certificate' => 'Certificat numérique téléchargeable'
                ]
            ],
            [
                'code' => 'maitre_nomadseo',
                'name' => 'Maître NomadSEO',
                'description' => '3 articles consécutifs avec score ≥ 90/100',
                'icon' => '🏅',
                'color' => 'yellow',
                'order' => 5,
                'conditions' => [
                    'type' => 'consecutive_high_score',
                    'count' => 3,
                    'min_score' => 90
                ],
                'rewards' => [
                    'expert_recognition' => 'Reconnaissance d\'expert en optimisation',
                    'beta_features' => 'Accès aux fonctionnalités en avant-première',
                    'early_access' => 'Beta-testeur des nouvelles features'
                ]
            ],
            [
                'code' => 'ambassadeur_social',
                'name' => 'Ambassadeur Social',
                'description' => '50 commentaires reçus et 100% partages respectés',
                'icon' => '🤝',
                'color' => 'pink',
                'order' => 6,
                'conditions' => [
                    'type' => 'social_engagement',
                    'min_comments' => 50,
                    'share_compliance' => 100
                ],
                'rewards' => [
                    'co_promotion' => 'Co-promotion sur les réseaux officiels',
                    'content_highlight' => 'Mise en avant régulière de vos contenus',
                    'advanced_stats' => 'Accès aux statistiques avancées'
                ]
            ],
            [
                'code' => 'favori_lecteurs',
                'name' => 'Favori des Lecteurs',
                'description' => '3 articles dans le top 10 mensuel',
                'icon' => '❤️',
                'color' => 'red',
                'order' => 7,
                'conditions' => [
                    'type' => 'top_articles',
                    'count' => 3,
                    'top_rank' => 10
                ],
                'rewards' => [
                    'newsletter_mention' => 'Mention newsletter mensuelle',
                    'priority_placement' => 'Placement privilégié des contenus',
                    'special_collaborations' => 'Invitations aux collaborations'
                ]
            ],
            [
                'code' => 'pionnier_nomadie',
                'name' => 'Pionnier Nomadie',
                'description' => 'Parmi les 100 premiers contributeurs actifs',
                'icon' => '👑',
                'color' => 'gold',
                'order' => 8,
                'conditions' => [
                    'type' => 'early_adopter',
                    'max_rank' => 100,
                    'min_articles' => 3
                ],
                'rewards' => [
                    'vip_status' => 'Statut VIP permanent',
                    'priority_access' => 'Accès prioritaire aux événements',
                    'platform_consultation' => 'Consultation sur l\'évolution de la plateforme',
                    'rare_badge' => 'Badge de prestige rare et recherché'
                ]
            ]
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(
                ['code' => $badge['code']],
                $badge
            );
        }
    }
}