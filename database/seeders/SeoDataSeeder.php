<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SeoCriterion;
use App\Models\SeoConfiguration;

class SeoDataSeeder extends Seeder
{
    public function run()
    {
        $this->createCriteria();
        $this->createConfigurations();
    }

    private function createCriteria()
    {
        $criteria = [
            // CONTENT
            [
                'category' => 'content',
                'code' => 'title_length',
                'name' => 'Longueur du titre',
                'description' => 'Le titre doit contenir entre 50 et 60 caractères pour un référencement optimal',
                'max_score' => 10,
                'validation_rules' => ['min' => 50, 'max' => 60]
            ],
            [
                'category' => 'content',
                'code' => 'word_count',
                'name' => 'Nombre de mots',
                'description' => 'Un article doit contenir au minimum 1500 mots pour être considéré comme complet',
                'max_score' => 15,
                'validation_rules' => ['min' => 1500, 'optimal' => 2000]
            ],
            [
                'category' => 'content',
                'code' => 'readability',
                'name' => 'Score de lisibilité',
                'description' => 'Mesure de la facilité de lecture (Flesch-Kincaid)',
                'max_score' => 10,
                'validation_rules' => ['min_score' => 60]
            ],
            [
                'category' => 'content',
                'code' => 'paragraph_structure',
                'name' => 'Structure des paragraphes',
                'description' => 'Paragraphes courts et bien structurés pour une lecture agréable',
                'max_score' => 5,
                'validation_rules' => ['max_length' => 300]
            ],
            [
                'category' => 'content',
                'code' => 'headings_hierarchy',
                'name' => 'Hiérarchie des titres',
                'description' => 'H1 unique, H2/H3 logiques et bien structurés',
                'max_score' => 10,
                'validation_rules' => ['h1_count' => 1, 'min_h2' => 3]
            ],

            // TECHNICAL
            [
                'category' => 'technical',
                'code' => 'meta_description',
                'name' => 'Meta description',
                'description' => 'Description optimisée entre 150-160 caractères',
                'max_score' => 10,
                'validation_rules' => ['min' => 150, 'max' => 160]
            ],
            [
                'category' => 'technical',
                'code' => 'url_structure',
                'name' => 'Structure URL',
                'description' => 'URL propre, courte et descriptive',
                'max_score' => 5,
                'validation_rules' => ['max_length' => 60]
            ],
            [
                'category' => 'technical',
                'code' => 'keyword_density',
                'name' => 'Densité de mots-clés',
                'description' => 'Utilisation équilibrée des mots-clés principaux',
                'max_score' => 10,
                'validation_rules' => ['min' => 0.5, 'max' => 2.5]
            ],
            [
                'category' => 'technical',
                'code' => 'lsi_keywords',
                'name' => 'Mots-clés LSI',
                'description' => 'Utilisation de mots-clés sémantiquement liés',
                'max_score' => 5,
                'validation_rules' => ['min_count' => 5]
            ],
            [
                'category' => 'technical',
                'code' => 'internal_links',
                'name' => 'Liens internes',
                'description' => 'Maillage interne vers d\'autres articles pertinents',
                'max_score' => 10,
                'validation_rules' => ['min' => 3, 'optimal' => 5]
            ],
            [
                'category' => 'technical',
                'code' => 'external_links',
                'name' => 'Liens externes',
                'description' => 'Liens vers des sources externes de qualité',
                'max_score' => 5,
                'validation_rules' => ['min' => 1, 'max' => 5]
            ],
            [
                'category' => 'technical',
                'code' => 'schema_markup',
                'name' => 'Schema markup',
                'description' => 'Données structurées pour les rich snippets',
                'max_score' => 10,
                'validation_rules' => ['required_types' => ['Article', 'Place']]
            ],
            [
                'category' => 'technical',
                'code' => 'open_graph',
                'name' => 'Open Graph',
                'description' => 'Métadonnées pour le partage social',
                'max_score' => 5,
                'validation_rules' => ['required_tags' => ['title', 'description', 'image']]
            ],

            // IMAGES
            [
                'category' => 'images',
                'code' => 'image_count',
                'name' => 'Nombre d\'images',
                'description' => 'Minimum d\'images pour illustrer l\'article',
                'max_score' => 10,
                'validation_rules' => ['min' => 3, 'optimal' => 5]
            ],
            [
                'category' => 'images',
                'code' => 'image_alt_text',
                'name' => 'Alt text des images',
                'description' => 'Toutes les images doivent avoir un texte alternatif',
                'max_score' => 10,
                'validation_rules' => ['required' => true]
            ],
            [
                'category' => 'images',
                'code' => 'image_quality',
                'name' => 'Qualité des images',
                'description' => 'Images authentiques vs stock photos',
                'max_score' => 5,
                'validation_rules' => ['prefer_authentic' => true]
            ],
            [
                'category' => 'images',
                'code' => 'image_optimization',
                'name' => 'Optimisation des images',
                'description' => 'Poids et format optimisés pour le web',
                'max_score' => 5,
                'validation_rules' => ['max_size_kb' => 200]
            ],

            // ENGAGEMENT
            [
                'category' => 'engagement',
                'code' => 'cta_presence',
                'name' => 'Call-to-action',
                'description' => 'Présence de CTA clairs et pertinents',
                'max_score' => 10,
                'validation_rules' => ['min' => 1]
            ],
            [
                'category' => 'engagement',
                'code' => 'questions_to_reader',
                'name' => 'Questions au lecteur',
                'description' => 'Questions directes pour engager le lecteur',
                'max_score' => 5,
                'validation_rules' => ['min' => 2]
            ],
            [
                'category' => 'engagement',
                'code' => 'scannable_structure',
                'name' => 'Structure scannable',
                'description' => 'Listes à puces, tableaux, éléments visuels',
                'max_score' => 10,
                'validation_rules' => ['min_lists' => 2]
            ],
            [
                'category' => 'engagement',
                'code' => 'hook_intro',
                'name' => 'Accroche introduction',
                'description' => 'Les 150 premiers mots doivent captiver',
                'max_score' => 10,
                'validation_rules' => ['max_words' => 150]
            ],

            // AUTHENTICITY
            [
                'category' => 'authenticity',
                'code' => 'plagiarism_check',
                'name' => 'Vérification plagiat',
                'description' => 'Contenu original et unique',
                'max_score' => 15,
                'validation_rules' => ['max_similarity' => 10]
            ],
            [
                'category' => 'authenticity',
                'code' => 'emotional_words',
                'name' => 'Mots émotionnels',
                'description' => 'Utilisation de mots qui créent une connexion',
                'max_score' => 5,
                'validation_rules' => ['min_count' => 10]
            ],
            [
                'category' => 'authenticity',
                'code' => 'personal_experience',
                'name' => 'Expérience personnelle',
                'description' => 'Anecdotes et expériences personnelles',
                'max_score' => 10,
                'validation_rules' => ['min_mentions' => 2]
            ],
            [
                'category' => 'authenticity',
                'code' => 'geo_coherence',
                'name' => 'Cohérence géographique',
                'description' => 'Vérification de l\'existence des lieux mentionnés',
                'max_score' => 5,
                'validation_rules' => ['verify_locations' => true]
            ],
            [
                'category' => 'authenticity',
                'code' => 'source_quality',
                'name' => 'Qualité des sources',
                'description' => 'Sources fiables et pertinentes citées',
                'max_score' => 5,
                'validation_rules' => ['min_quality_score' => 70]
            ]
        ];

        foreach ($criteria as $criterion) {
            SeoCriterion::updateOrCreate(
                ['code' => $criterion['code']],
                $criterion
            );
        }
    }

    private function createConfigurations()
    {
        $configurations = [
            // COMMUNAUTE - MODE LIBRE
            'communaute_libre' => [
                'writer_type' => 'communaute',
                'mode' => 'libre',
                'configs' => [
                    'title_length' => ['weight' => 1.0, 'threshold' => 7.8, 'is_required' => true],
                    'word_count' => ['weight' => 1.5, 'threshold' => 8.0, 'is_required' => true],
                    'readability' => ['weight' => 1.0, 'threshold' => 6.0, 'is_required' => false],
                    'paragraph_structure' => ['weight' => 0.8, 'threshold' => 5.0, 'is_required' => false],
                    'headings_hierarchy' => ['weight' => 1.0, 'threshold' => 7.0, 'is_required' => true],
                    'meta_description' => ['weight' => 1.0, 'threshold' => 7.0, 'is_required' => true],
                    'url_structure' => ['weight' => 0.8, 'threshold' => 6.0, 'is_required' => false],
                    'keyword_density' => ['weight' => 0.8, 'threshold' => 5.0, 'is_required' => false],
                    'lsi_keywords' => ['weight' => 0.6, 'threshold' => 4.0, 'is_required' => false],
                    'internal_links' => ['weight' => 1.2, 'threshold' => 6.0, 'is_required' => false],
                    'external_links' => ['weight' => 0.5, 'threshold' => 4.0, 'is_required' => false],
                    'schema_markup' => ['weight' => 0.8, 'threshold' => 5.0, 'is_required' => false],
                    'open_graph' => ['weight' => 0.8, 'threshold' => 6.0, 'is_required' => false],
                    'image_count' => ['weight' => 1.0, 'threshold' => 6.0, 'is_required' => true],
                    'image_alt_text' => ['weight' => 0.8, 'threshold' => 7.0, 'is_required' => false],
                    'image_quality' => ['weight' => 0.6, 'threshold' => 5.0, 'is_required' => false],
                    'image_optimization' => ['weight' => 0.5, 'threshold' => 5.0, 'is_required' => false],
                    'cta_presence' => ['weight' => 0.8, 'threshold' => 5.0, 'is_required' => false],
                    'questions_to_reader' => ['weight' => 0.6, 'threshold' => 4.0, 'is_required' => false],
                    'scannable_structure' => ['weight' => 0.8, 'threshold' => 6.0, 'is_required' => false],
                    'hook_intro' => ['weight' => 1.0, 'threshold' => 6.0, 'is_required' => false],
                    'plagiarism_check' => ['weight' => 2.0, 'threshold' => 8.0, 'is_required' => true],
                    'emotional_words' => ['weight' => 0.8, 'threshold' => 5.0, 'is_required' => false],
                    'personal_experience' => ['weight' => 1.0, 'threshold' => 6.0, 'is_required' => false],
                    'geo_coherence' => ['weight' => 0.8, 'threshold' => 7.0, 'is_required' => false],
                    'source_quality' => ['weight' => 0.6, 'threshold' => 5.0, 'is_required' => false]
                ]
            ],

            // CLIENT - MODE LIBRE
            'client_libre' => [
                'writer_type' => 'client',
                'mode' => 'libre',
                'configs' => [
                    'title_length' => ['weight' => 1.0, 'threshold' => 7.0, 'is_required' => true],
                    'word_count' => ['weight' => 1.2, 'threshold' => 7.0, 'is_required' => true],
                    'readability' => ['weight' => 0.9, 'threshold' => 5.0, 'is_required' => false],
                    'paragraph_structure' => ['weight' => 0.7, 'threshold' => 4.0, 'is_required' => false],
                    'headings_hierarchy' => ['weight' => 0.9, 'threshold' => 6.0, 'is_required' => false],
                    'meta_description' => ['weight' => 0.9, 'threshold' => 6.0, 'is_required' => false],
                    'url_structure' => ['weight' => 0.7, 'threshold' => 5.0, 'is_required' => false],
                    'keyword_density' => ['weight' => 0.7, 'threshold' => 5.0, 'is_required' => false],
                    'lsi_keywords' => ['weight' => 0.5, 'threshold' => 4.0, 'is_required' => false],
                    'internal_links' => ['weight' => 1.0, 'threshold' => 5.0, 'is_required' => false],
                    'external_links' => ['weight' => 0.4, 'threshold' => 3.0, 'is_required' => false],
                    'schema_markup' => ['weight' => 0.6, 'threshold' => 4.0, 'is_required' => false],
                    'open_graph' => ['weight' => 0.7, 'threshold' => 5.0, 'is_required' => false],
                    'image_count' => ['weight' => 1.2, 'threshold' => 7.0, 'is_required' => true],
                    'image_alt_text' => ['weight' => 0.7, 'threshold' => 6.0, 'is_required' => false],
                    'image_quality' => ['weight' => 1.0, 'threshold' => 7.0, 'is_required' => false],
                    'image_optimization' => ['weight' => 0.4, 'threshold' => 4.0, 'is_required' => false],
                    'cta_presence' => ['weight' => 0.6, 'threshold' => 4.0, 'is_required' => false],
                    'questions_to_reader' => ['weight' => 0.5, 'threshold' => 3.0, 'is_required' => false],
                    'scannable_structure' => ['weight' => 0.7, 'threshold' => 5.0, 'is_required' => false],
                    'hook_intro' => ['weight' => 0.9, 'threshold' => 5.0, 'is_required' => false],
                    'plagiarism_check' => ['weight' => 1.8, 'threshold' => 7.0, 'is_required' => true],
                    'emotional_words' => ['weight' => 1.0, 'threshold' => 6.0, 'is_required' => false],
                    'personal_experience' => ['weight' => 1.5, 'threshold' => 8.0, 'is_required' => true],
                    'geo_coherence' => ['weight' => 1.0, 'threshold' => 8.0, 'is_required' => true],
                    'source_quality' => ['weight' => 0.5, 'threshold' => 4.0, 'is_required' => false]
                ]
            ],

            // PARTENAIRE - MODE LIBRE (avec contrôle renforcé)
            'partenaire_libre' => [
                'writer_type' => 'partenaire',
                'mode' => 'libre',
                'configs' => [
                    'title_length' => ['weight' => 1.0, 'threshold' => 8.0, 'is_required' => true],
                    'word_count' => ['weight' => 1.3, 'threshold' => 8.0, 'is_required' => true],
                    'readability' => ['weight' => 1.0, 'threshold' => 7.0, 'is_required' => true],
                    'paragraph_structure' => ['weight' => 0.9, 'threshold' => 6.0, 'is_required' => false],
                    'headings_hierarchy' => ['weight' => 1.0, 'threshold' => 7.0, 'is_required' => true],
                    'meta_description' => ['weight' => 1.0, 'threshold' => 7.0, 'is_required' => true],
                    'url_structure' => ['weight' => 0.9, 'threshold' => 6.0, 'is_required' => false],
                    'keyword_density' => ['weight' => 1.0, 'threshold' => 6.0, 'is_required' => false],
                    'lsi_keywords' => ['weight' => 0.8, 'threshold' => 5.0, 'is_required' => false],
                    'internal_links' => ['weight' => 1.3, 'threshold' => 7.0, 'is_required' => true],
                    'external_links' => ['weight' => 0.6, 'threshold' => 4.0, 'is_required' => false],
                    'schema_markup' => ['weight' => 1.0, 'threshold' => 6.0, 'is_required' => false],
                    'open_graph' => ['weight' => 1.0, 'threshold' => 7.0, 'is_required' => false],
                    'image_count' => ['weight' => 1.0, 'threshold' => 7.0, 'is_required' => true],
                    'image_alt_text' => ['weight' => 0.9, 'threshold' => 7.0, 'is_required' => false],
                    'image_quality' => ['weight' => 0.8, 'threshold' => 6.0, 'is_required' => false],
                    'image_optimization' => ['weight' => 0.7, 'threshold' => 6.0, 'is_required' => false],
                    'cta_presence' => ['weight' => 0.5, 'threshold' => 4.0, 'is_required' => false],
                    'questions_to_reader' => ['weight' => 0.7, 'threshold' => 5.0, 'is_required' => false],
                    'scannable_structure' => ['weight' => 1.0, 'threshold' => 7.0, 'is_required' => false],
                    'hook_intro' => ['weight' => 1.2, 'threshold' => 7.0, 'is_required' => true],
                    'plagiarism_check' => ['weight' => 2.5, 'threshold' => 9.0, 'is_required' => true],
                    'emotional_words' => ['weight' => 0.7, 'threshold' => 5.0, 'is_required' => false],
                    'personal_experience' => ['weight' => 0.8, 'threshold' => 5.0, 'is_required' => false],
                    'geo_coherence' => ['weight' => 1.0, 'threshold' => 8.0, 'is_required' => true],
                    'source_quality' => ['weight' => 1.0, 'threshold' => 7.0, 'is_required' => false]
                ]
            ],

            // EQUIPE - MODE LIBRE
            'equipe_libre' => [
                'writer_type' => 'equipe',
                'mode' => 'libre',
                'configs' => [
                    // L'équipe a plus de liberté, pondérations égales et seuils plus bas
                    'title_length' => ['weight' => 1.0, 'threshold' => 5.0, 'is_required' => false],
                    'word_count' => ['weight' => 1.0, 'threshold' => 5.0, 'is_required' => false],
                    'readability' => ['weight' => 1.0, 'threshold' => 5.0, 'is_required' => false],
                    'paragraph_structure' => ['weight' => 1.0, 'threshold' => 5.0, 'is_required' => false],
                    'headings_hierarchy' => ['weight' => 1.0, 'threshold' => 5.0, 'is_required' => false],
                    'meta_description' => ['weight' => 1.0, 'threshold' => 5.0, 'is_required' => false],
                    'url_structure' => ['weight' => 1.0, 'threshold' => 5.0, 'is_required' => false],
                    'keyword_density' => ['weight' => 1.0, 'threshold' => 5.0, 'is_required' => false],
                    'lsi_keywords' => ['weight' => 1.0, 'threshold' => 5.0, 'is_required' => false],
                    'internal_links' => ['weight' => 1.0, 'threshold' => 5.0, 'is_required' => false],
                    'external_links' => ['weight' => 1.0, 'threshold' => 5.0, 'is_required' => false],
                    'schema_markup' => ['weight' => 1.0, 'threshold' => 5.0, 'is_required' => false],
                    'open_graph' => ['weight' => 1.0, 'threshold' => 5.0, 'is_required' => false],
                    'image_count' => ['weight' => 1.0, 'threshold' => 5.0, 'is_required' => false],
                    'image_alt_text' => ['weight' => 1.0, 'threshold' => 5.0, 'is_required' => false],
                    'image_quality' => ['weight' => 1.0, 'threshold' => 5.0, 'is_required' => false],
                    'image_optimization' => ['weight' => 1.0, 'threshold' => 5.0, 'is_required' => false],
                    'cta_presence' => ['weight' => 1.0, 'threshold' => 5.0, 'is_required' => false],
                    'questions_to_reader' => ['weight' => 1.0, 'threshold' => 5.0, 'is_required' => false],
                    'scannable_structure' => ['weight' => 1.0, 'threshold' => 5.0, 'is_required' => false],
                    'hook_intro' => ['weight' => 1.0, 'threshold' => 5.0, 'is_required' => false],
                    'plagiarism_check' => ['weight' => 1.0, 'threshold' => 5.0, 'is_required' => false],
                    'emotional_words' => ['weight' => 1.0, 'threshold' => 5.0, 'is_required' => false],
                    'personal_experience' => ['weight' => 1.0, 'threshold' => 5.0, 'is_required' => false],
                    'geo_coherence' => ['weight' => 1.0, 'threshold' => 5.0, 'is_required' => false],
                    'source_quality' => ['weight' => 1.0, 'threshold' => 5.0, 'is_required' => false]
                ]
            ],

            // EQUIPE - MODE COMMANDE INTERNE
            'equipe_commande' => [
                'writer_type' => 'equipe',
                'mode' => 'commande_interne',
                'configs' => [
                    // Configuration stricte pour les commandes internes
                    'title_length' => ['weight' => 1.0, 'threshold' => 8.0, 'is_required' => true],
                    'word_count' => ['weight' => 1.0, 'threshold' => 8.0, 'is_required' => true],
                    'readability' => ['weight' => 1.0, 'threshold' => 7.0, 'is_required' => true],
                    'paragraph_structure' => ['weight' => 1.0, 'threshold' => 7.0, 'is_required' => false],
                    'headings_hierarchy' => ['weight' => 1.0, 'threshold' => 8.0, 'is_required' => true],
                    'meta_description' => ['weight' => 1.0, 'threshold' => 8.0, 'is_required' => true],
                    'url_structure' => ['weight' => 1.0, 'threshold' => 7.0, 'is_required' => true],
                    'keyword_density' => ['weight' => 1.2, 'threshold' => 7.0, 'is_required' => true],
                    'lsi_keywords' => ['weight' => 1.0, 'threshold' => 6.0, 'is_required' => false],
                    'internal_links' => ['weight' => 1.5, 'threshold' => 8.0, 'is_required' => true],
                    'external_links' => ['weight' => 0.8, 'threshold' => 5.0, 'is_required' => false],
                    'schema_markup' => ['weight' => 1.2, 'threshold' => 8.0, 'is_required' => true],
                    'open_graph' => ['weight' => 1.0, 'threshold' => 8.0, 'is_required' => true],
                    'image_count' => ['weight' => 1.0, 'threshold' => 7.0, 'is_required' => true],
                    'image_alt_text' => ['weight' => 1.0, 'threshold' => 8.0, 'is_required' => true],
                    'image_quality' => ['weight' => 1.0, 'threshold' => 7.0, 'is_required' => false],
                    'image_optimization' => ['weight' => 1.0, 'threshold' => 7.0, 'is_required' => true],
                    'cta_presence' => ['weight' => 1.0, 'threshold' => 7.0, 'is_required' => true],
                    'questions_to_reader' => ['weight' => 0.8, 'threshold' => 6.0, 'is_required' => false],
                    'scannable_structure' => ['weight' => 1.0, 'threshold' => 8.0, 'is_required' => true],
                    'hook_intro' => ['weight' => 1.2, 'threshold' => 8.0, 'is_required' => true],
                    'plagiarism_check' => ['weight' => 1.5, 'threshold' => 9.0, 'is_required' => true],
                    'emotional_words' => ['weight' => 0.8, 'threshold' => 6.0, 'is_required' => false],
                    'personal_experience' => ['weight' => 0.8, 'threshold' => 6.0, 'is_required' => false],
                    'geo_coherence' => ['weight' => 1.0, 'threshold' => 8.0, 'is_required' => true],
                    'source_quality' => ['weight' => 1.0, 'threshold' => 7.0, 'is_required' => false]
                ]
            ]
        ];

        foreach ($configurations as $config) {
            foreach ($config['configs'] as $criterionCode => $settings) {
                $criterion = SeoCriterion::where('code', $criterionCode)->first();
                
                if ($criterion) {
                    SeoConfiguration::updateOrCreate(
                        [
                            'writer_type' => $config['writer_type'],
                            'mode' => $config['mode'],
                            'criterion_id' => $criterion->id,
                        ],
                        [
                            'weight' => $settings['weight'],
                            'threshold' => $settings['threshold'],
                            'is_required' => $settings['is_required'],
                            'custom_rules' => $settings['custom_rules'] ?? null
                        ]
                    );
                }
            }
        }
        
        // Configuration spéciale pour auto-promo des partenaires
        $autoPromoCriterion = SeoCriterion::updateOrCreate(
            ['code' => 'auto_promo_limit'],
            [
                'category' => 'authenticity',
                'name' => 'Limite auto-promotion',
                'description' => 'Limite du contenu auto-promotionnel pour les partenaires',
                'max_score' => 10,
                'validation_rules' => ['max_percentage' => 20]
            ]
        );

        SeoConfiguration::updateOrCreate(
            [
                'writer_type' => 'partenaire',
                'mode' => 'libre',
                'criterion_id' => $autoPromoCriterion->id,
            ],
            [
                'weight' => 2.0,
                'threshold' => 8.0,
                'is_required' => true,
                'custom_rules' => ['max_percentage' => 20]
            ]
        );
    }
}