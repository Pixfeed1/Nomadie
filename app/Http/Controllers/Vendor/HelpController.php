<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HelpController extends Controller
{
    /**
     * Affiche la page d'aide sur les types d'offres
     */
    public function tripTypes()
    {
        $tripTypes = [
            [
                'id' => 'per_person_per_day',
                'name' => 'Séjour organisé',
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>',
                'color' => 'accent',
                'description' => 'Voyages tout compris avec hébergement, transport et activités',
                'pricing' => 'Prix par personne pour la durée totale du séjour',
                'examples' => [
                    'Trek au Népal de 15 jours',
                    'Circuit découverte du Japon',
                    'Safari photo au Kenya',
                    'Croisière en Méditerranée'
                ],
                'features' => [
                    'Gestion des places par date de départ',
                    'Prix adulte et enfant distincts',
                    'Minimum de participants requis',
                    'Départs garantis possibles',
                    'Itinéraire jour par jour'
                ],
                'ideal_for' => 'Agences de voyage, tour-opérateurs, guides professionnels'
            ],
            [
                'id' => 'per_night_property',
                'name' => 'Location de vacances',
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>',
                'color' => 'primary',
                'description' => 'Hébergements entiers à louer pour des vacances',
                'pricing' => 'Prix par nuit pour le logement complet',
                'examples' => [
                    'Villa avec piscine en Provence',
                    'Chalet de montagne à Chamonix',
                    'Appartement vue mer à Nice',
                    'Maison de campagne en Normandie'
                ],
                'features' => [
                    'Calendrier de disponibilités',
                    'Prix unique par nuit',
                    'Capacité maximale du logement',
                    'Tarifs saisonniers possibles',
                    'Équipements et services inclus'
                ],
                'ideal_for' => 'Propriétaires, gestionnaires de biens, agences immobilières'
            ],
            [
                'id' => 'per_person_activity',
                'name' => 'Activité ou expérience',
                'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>',
                'color' => 'success',
                'description' => 'Activités de courte durée, visites guidées, ateliers',
                'pricing' => 'Prix par personne pour l\'activité',
                'examples' => [
                    'Vol en montgolfière',
                    'Cours de cuisine française',
                    'Visite guidée du Louvre',
                    'Baptême de plongée',
                    'Dégustation de vins'
                ],
                'features' => [
                    'Créneaux horaires multiples',
                    'Durée de quelques heures',
                    'Prix par participant',
                    'Groupes privés possibles',
                    'Réservation instantanée'
                ],
                'ideal_for' => 'Prestataires d\'activités, guides touristiques, artisans'
            ]
        ];

        $faqs = [
            [
                'question' => 'Puis-je proposer plusieurs types d\'offres ?',
                'answer' => 'Oui ! Vous pouvez créer des offres de différents types selon vos besoins. Par exemple, proposer à la fois des locations de vacances et des activités.'
            ],
            [
                'question' => 'Comment sont calculées les commissions ?',
                'answer' => 'Les commissions dépendent de votre plan d\'abonnement : 20% pour le plan gratuit, 10% pour Essential et 5% pour Pro. Elles s\'appliquent sur le montant total de chaque réservation.'
            ],
            [
                'question' => 'Puis-je changer le type d\'une offre après création ?',
                'answer' => 'Non, le type d\'offre ne peut pas être modifié après création car il détermine la structure tarifaire et les options disponibles. Vous devrez créer une nouvelle offre.'
            ],
            [
                'question' => 'Quelle est la différence entre un séjour et une activité ?',
                'answer' => 'Un séjour est généralement sur plusieurs jours avec hébergement inclus, tandis qu\'une activité dure quelques heures sans hébergement. La tarification et les options de réservation sont adaptées à chaque type.'
            ]
        ];

        return view('vendor.help.trip-types', compact('tripTypes', 'faqs'));
    }
}