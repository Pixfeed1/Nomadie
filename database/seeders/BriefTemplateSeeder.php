<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BriefTemplate;
use App\Models\User;

class BriefTemplateSeeder extends Seeder
{
    public function run()
    {
        // Récupérer le premier admin ou créer un utilisateur système
        $admin = User::where('email', 'admin@nomadie.com')->first()
            ?? User::first()
            ?? User::factory()->create(['email' => 'system@nomadie.com']);

        $templates = [
            [
                'name' => 'Guide Destination Complet',
                'slug' => 'guide-destination-complet',
                'description' => 'Template pour un guide complet de destination (ville ou pays)',
                'type' => 'destination',
                'content_requirements' => [
                    'sections' => [
                        'Introduction' => 'Présentation générale de la destination (histoire, culture, ambiance)',
                        'Quand partir ?' => 'Meilleure période, climat, événements saisonniers',
                        'Comment y aller ?' => 'Transports (avion, train, bus), visas, formalités',
                        'Où dormir ?' => 'Recommandations hébergements (budget, milieu de gamme, luxe)',
                        'Que voir et faire ?' => 'Top attractions, activités incontournables, lieux hors des sentiers battus',
                        'Où manger ?' => 'Restaurants recommandés, spécialités locales',
                        'Budget' => 'Coût estimé par jour (budget routard, moyen, confort)',
                        'Conseils pratiques' => 'Sécurité, santé, langue, monnaie, culture locale',
                    ],
                    'images_requises' => 8,
                    'ton' => 'Informatif et inspirant, accessible à tous',
                ],
                'keywords' => ['destination', 'voyage', 'guide', 'tourisme', 'visiter'],
                'min_words' => 2000,
                'target_score' => 85,
                'seo_requirements' => [
                    'min_h2' => 5,
                    'images_ratio' => '1/250',
                    'internal_links' => 3,
                ],
            ],

            [
                'name' => 'Guide Pratique',
                'slug' => 'guide-pratique',
                'description' => 'Template pour guides pratiques (ex: comment obtenir un visa, préparer son sac, etc.)',
                'type' => 'guide_pratique',
                'content_requirements' => [
                    'sections' => [
                        'Introduction' => 'Pourquoi ce guide est utile, à qui il s\'adresse',
                        'Étapes détaillées' => 'Liste numérotée des étapes à suivre',
                        'Documents nécessaires' => 'Checklist complète',
                        'Coûts' => 'Budget à prévoir',
                        'Délais' => 'Combien de temps ça prend',
                        'Astuces et conseils' => 'Tips d\'experts, pièges à éviter',
                        'FAQ' => 'Questions fréquentes',
                    ],
                    'format' => 'Très structuré avec listes à puces et numérotées',
                    'ton' => 'Pratique, clair, pédagogique',
                ],
                'keywords' => ['guide', 'comment', 'pratique', 'conseils', 'astuces'],
                'min_words' => 1500,
                'target_score' => 80,
                'seo_requirements' => [
                    'min_h2' => 4,
                    'lists_required' => true,
                ],
            ],

            [
                'name' => 'Article Culture et Traditions',
                'slug' => 'culture-traditions',
                'description' => 'Template pour articles sur la culture locale, traditions, festivals',
                'type' => 'culture',
                'content_requirements' => [
                    'sections' => [
                        'Contexte historique' => 'Origines de la tradition/culture',
                        'Description' => 'En quoi ça consiste, comment ça se déroule',
                        'Signification' => 'Importance culturelle, symbolisme',
                        'Où et quand l\'observer' => 'Lieux, dates, périodes',
                        'Expérience personnelle' => 'Témoignage, anecdotes',
                        'Conseils pour les visiteurs' => 'Etiquette, respect, immersion',
                    ],
                    'ton' => 'Respectueux, immersif, storytelling',
                    'images_requises' => 6,
                ],
                'keywords' => ['culture', 'tradition', 'local', 'festival', 'patrimoine'],
                'min_words' => 1500,
                'target_score' => 80,
            ],

            [
                'name' => 'Guide Gastronomie',
                'slug' => 'guide-gastronomie',
                'description' => 'Template pour articles gastronomiques (spécialités, restaurants, food tours)',
                'type' => 'gastronomie',
                'content_requirements' => [
                    'sections' => [
                        'Introduction' => 'Présentation de la gastronomie locale',
                        'Plats incontournables' => 'Top 5-10 plats à essayer absolument',
                        'Restaurants recommandés' => 'Sélection par budget',
                        'Marchés et street food' => 'Où manger local et pas cher',
                        'Spécialités régionales' => 'Variations par région',
                        'Recettes' => '1-2 recettes à essayer chez soi (optionnel)',
                        'Conseils' => 'Allergies, végétariens, où acheter des produits',
                    ],
                    'ton' => 'Gourmand, descriptif, sensoriel',
                    'images_requises' => 10,
                ],
                'keywords' => ['gastronomie', 'cuisine', 'restaurant', 'plat', 'spécialité'],
                'min_words' => 1800,
                'target_score' => 82,
            ],

            [
                'name' => 'Comparatif Budget',
                'slug' => 'comparatif-budget',
                'description' => 'Template pour articles de budget voyage détaillé',
                'type' => 'budget',
                'content_requirements' => [
                    'sections' => [
                        'Budget par profil' => 'Routard, moyen, confort - coût par jour',
                        'Hébergement' => 'Prix moyens par type',
                        'Transport' => 'Coûts des différents moyens de transport',
                        'Nourriture' => 'Prix des repas (rue, restaurant local, touristique)',
                        'Activités' => 'Coût des attractions, excursions',
                        'Astuces économies' => 'Comment réduire ses dépenses',
                        'Tableau récapitulatif' => 'Budget total estimé par durée',
                    ],
                    'format' => 'Tableaux, chiffres précis, comparaisons',
                    'ton' => 'Factuel, transparent, utile',
                ],
                'keywords' => ['budget', 'prix', 'coût', 'combien', 'économiser'],
                'min_words' => 1500,
                'target_score' => 78,
            ],
        ];

        foreach ($templates as $templateData) {
            BriefTemplate::create(array_merge($templateData, [
                'created_by' => $admin->id,
                'is_active' => true,
                'usage_count' => 0,
            ]));
        }

        $this->command->info('✅ 5 brief templates créés avec succès !');
    }
}
