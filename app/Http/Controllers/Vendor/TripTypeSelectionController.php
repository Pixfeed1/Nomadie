<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TripTypeSelectionController extends Controller
{
   /**
    * Afficher la page de sélection du type
    */
   public function index()
   {
       $vendor = auth()->user()->vendor;
       
       // Définir les types disponibles
       $tripTypes = [
           [
               'id' => 'location',
               'pricing_mode' => \App\Models\Trip::PRICING_MODE_PER_NIGHT_PROPERTY,
               'title' => 'Location d\'hébergement',
               'description' => 'Louez votre gîte, villa, appartement ou maison entière',
               'icon' => 'home',
               'examples' => ['Gîte rural', 'Villa avec piscine', 'Chalet montagne', 'Appartement en ville'],
               'features' => [
                   'Prix par nuit pour le logement entier',
                   'Capacité maximale définie',
                   'Durée flexible (minimum de nuits)',
                   'Idéal pour les locations saisonnières'
               ]
           ],
           [
               'id' => 'sejour',
               'pricing_mode' => \App\Models\Trip::PRICING_MODE_PER_PERSON_PER_DAY,
               'title' => 'Séjour organisé',
               'description' => 'Proposez un séjour tout compris avec activités et services',
               'icon' => 'map',
               'examples' => ['Retraite yoga', 'Stage de surf', 'Circuit culturel', 'Séjour bien-être'],
               'features' => [
                   'Prix par personne pour la durée',
                   'Programme d\'activités inclus',
                   'Repas et services possibles',
                   'Dates de départ fixes'
               ]
           ],
           [
               'id' => 'activite',
               'pricing_mode' => \App\Models\Trip::PRICING_MODE_PER_PERSON_ACTIVITY,
               'title' => 'Activité ou expérience',
               'description' => 'Offrez une activité ponctuelle de quelques heures',
               'icon' => 'activity',
               'examples' => ['Randonnée guidée', 'Cours de cuisine', 'Visite culturelle', 'Activité nautique'],
               'features' => [
                   'Prix par personne',
                   'Durée en heures',
                   'Matériel inclus possible',
                   'Disponibilités flexibles'
               ]
           ],
           [
               'id' => 'sur-mesure',
               'pricing_mode' => \App\Models\Trip::PRICING_MODE_CUSTOM,
               'title' => 'Offre sur mesure',
               'description' => 'Créez une offre personnalisée qui ne rentre pas dans les catégories standard',
               'icon' => 'star',
               'examples' => ['Package complexe', 'Tarification spéciale', 'Offre hybride'],
               'features' => [
                   'Tarification flexible',
                   'Description personnalisée',
                   'Adaptée à vos besoins',
                   'Support de notre équipe'
               ]
           ]
       ];
       
       return view('vendor.trips.select-type', compact('vendor', 'tripTypes'));
   }
   
   /**
    * Rediriger vers le bon formulaire selon le type choisi
    */
   public function redirect(Request $request)
   {
       $request->validate([
           'type' => 'required|in:location,sejour,activite,sur-mesure',
           'pricing_mode' => 'required'
       ]);
       
       // Stocker le type en session pour le formulaire de création
       session([
           'trip_creation_type' => $request->type,
           'trip_pricing_mode' => $request->pricing_mode
       ]);
       
       // Rediriger vers le formulaire de création avec le type
       return redirect()->route('vendor.trips.create', ['type' => $request->type]);
   }
}