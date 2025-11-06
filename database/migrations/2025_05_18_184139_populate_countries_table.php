<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        // Vérifions d'abord les continents existants
        $continents = DB::table('continents')->get()->pluck('id', 'slug')->toArray();
        
        // Si aucun continent n'existe, nous allons les créer
        if (empty($continents)) {
            echo "Aucun continent trouvé. Création des continents...\n";
            
            $continentsData = [
                ['name' => 'Afrique', 'slug' => 'afrique', 'color' => '#FFB347', 'position' => 1],
                ['name' => 'Amérique du Nord', 'slug' => 'amerique-du-nord', 'color' => '#FF6961', 'position' => 2],
                ['name' => 'Amérique du Sud', 'slug' => 'amerique-du-sud', 'color' => '#CB99C9', 'position' => 3],
                ['name' => 'Antarctique', 'slug' => 'antarctique', 'color' => '#FDFD96', 'position' => 4],
                ['name' => 'Asie', 'slug' => 'asie', 'color' => '#77DD77', 'position' => 5],
                ['name' => 'Europe', 'slug' => 'europe', 'color' => '#AEC6CF', 'position' => 6],
                ['name' => 'Océanie', 'slug' => 'oceanie', 'color' => '#B39EB5', 'position' => 7],
            ];
            
            foreach ($continentsData as $continent) {
                DB::table('continents')->insert([
                    'name' => $continent['name'],
                    'slug' => $continent['slug'],
                    'color' => $continent['color'],
                    'position' => $continent['position'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Récupérer les continents après les avoir créés
            $continents = DB::table('continents')->get()->pluck('id', 'slug')->toArray();
        }
        
        // Liste des pays par continent
        $countries = [
            // Europe
            'europe' => [
                'Albanie', 'Allemagne', 'Andorre', 'Autriche', 'Belgique', 'Biélorussie', 'Bosnie-Herzégovine', 
                'Bulgarie', 'Chypre', 'Croatie', 'Danemark', 'Espagne', 'Estonie', 'Finlande', 'France', 
                'Grèce', 'Hongrie', 'Irlande', 'Islande', 'Italie', 'Kosovo', 'Lettonie', 'Liechtenstein', 
                'Lituanie', 'Luxembourg', 'Macédoine du Nord', 'Malte', 'Moldavie', 'Monaco', 'Monténégro', 
                'Norvège', 'Pays-Bas', 'Pologne', 'Portugal', 'République tchèque', 'Roumanie', 
                'Royaume-Uni', 'Russie', 'Saint-Marin', 'Serbie', 'Slovaquie', 'Slovénie', 'Suède', 
                'Suisse', 'Ukraine', 'Vatican'
            ],
            
            // Asie
            'asie' => [
                'Afghanistan', 'Arabie saoudite', 'Arménie', 'Azerbaïdjan', 'Bahreïn', 'Bangladesh', 
                'Bhoutan', 'Birmanie', 'Brunei', 'Cambodge', 'Chine', 'Corée du Nord', 'Corée du Sud', 
                'Émirats arabes unis', 'Géorgie', 'Inde', 'Indonésie', 'Irak', 'Iran', 'Israël', 'Japon', 
                'Jordanie', 'Kazakhstan', 'Kirghizistan', 'Koweït', 'Laos', 'Liban', 'Malaisie', 'Maldives', 
                'Mongolie', 'Népal', 'Oman', 'Ouzbékistan', 'Pakistan', 'Philippines', 'Qatar', 'Singapour', 
                'Sri Lanka', 'Syrie', 'Tadjikistan', 'Taïwan', 'Thaïlande', 'Timor oriental', 'Turkménistan', 
                'Turquie', 'Viêt Nam', 'Yémen'
            ],
            
            // Afrique
            'afrique' => [
                'Afrique du Sud', 'Algérie', 'Angola', 'Bénin', 'Botswana', 'Burkina Faso', 'Burundi', 
                'Cameroun', 'Cap-Vert', 'République centrafricaine', 'Comores', 'République du Congo', 
                'République démocratique du Congo', 'Côte d\'Ivoire', 'Djibouti', 'Égypte', 'Érythrée', 
                'Eswatini', 'Éthiopie', 'Gabon', 'Gambie', 'Ghana', 'Guinée', 'Guinée-Bissau', 'Guinée équatoriale', 
                'Kenya', 'Lesotho', 'Liberia', 'Libye', 'Madagascar', 'Malawi', 'Mali', 'Maroc', 'Maurice', 
                'Mauritanie', 'Mozambique', 'Namibie', 'Niger', 'Nigeria', 'Ouganda', 'Rwanda', 
                'São Tomé-et-Principe', 'Sénégal', 'Seychelles', 'Sierra Leone', 'Somalie', 'Soudan', 
                'Soudan du Sud', 'Tanzanie', 'Tchad', 'Togo', 'Tunisie', 'Zambie', 'Zimbabwe'
            ],
            
            // Amérique du Nord
            'amerique-du-nord' => [
                'Antigua-et-Barbuda', 'Bahamas', 'Barbade', 'Belize', 'Canada', 'Costa Rica', 'Cuba', 
                'République dominicaine', 'Dominique', 'El Salvador', 'États-Unis', 'Grenade', 'Guatemala', 
                'Haïti', 'Honduras', 'Jamaïque', 'Mexique', 'Nicaragua', 'Panama', 'Saint-Christophe-et-Niévès', 
                'Sainte-Lucie', 'Saint-Vincent-et-les-Grenadines', 'Trinité-et-Tobago'
            ],
            
            // Amérique du Sud
            'amerique-du-sud' => [
                'Argentine', 'Bolivie', 'Brésil', 'Chili', 'Colombie', 'Équateur', 'Guyana', 
                'Paraguay', 'Pérou', 'Suriname', 'Uruguay', 'Venezuela'
            ],
            
            // Océanie
            'oceanie' => [
                'Australie', 'Fidji', 'Îles Marshall', 'Îles Salomon', 'Kiribati', 'Micronésie', 
                'Nauru', 'Nouvelle-Zélande', 'Palaos', 'Papouasie-Nouvelle-Guinée', 'Samoa', 
                'Tonga', 'Tuvalu', 'Vanuatu'
            ],
            
            // Antarctique (pas de pays)
            'antarctique' => []
        ];

        // Compteurs pour le rapport
        $added = 0;
        $skipped = 0;

        // Parcourir chaque continent et ajouter ses pays
        foreach ($countries as $continentSlug => $countriesInContinent) {
            // Vérifier si le continent existe
            if (!isset($continents[$continentSlug])) {
                echo "Le continent '{$continentSlug}' n'existe pas dans la base de données. Pays ignorés.\n";
                continue;
            }
            
            $continentId = $continents[$continentSlug];
            
            foreach ($countriesInContinent as $countryName) {
                // Créer un slug à partir du nom du pays
                $slug = $this->createSlug($countryName);
                
                // Vérifier si ce pays existe déjà
                $exists = DB::table('countries')
                    ->where('name', $countryName)
                    ->orWhere('slug', $slug)
                    ->exists();
                
                // Si le pays n'existe pas encore, l'ajouter
                if (!$exists) {
                    DB::table('countries')->insert([
                        'name' => $countryName,
                        'slug' => $slug,
                        'continent_id' => $continentId,
                        'description' => 'Découvrez la richesse culturelle et les paysages exceptionnels de ' . $countryName,
                        'popular' => false,
                        'rating' => 4.0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $added++;
                } else {
                    $skipped++;
                }
            }
        }
        
        echo "Migration terminée : {$added} pays ajoutés, {$skipped} pays déjà existants ignorés.\n";
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        // Cette méthode est vide car nous ne voulons pas supprimer les pays ajoutés
    }
    
    /**
     * Créer un slug à partir du nom du pays
     */
    private function createSlug($name)
    {
        // Convertir en minuscules
        $slug = strtolower($name);
        
        // Remplacer les caractères accentués
        $slug = str_replace(
            ['à','á','â','ã','ä','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ù','ú','û','ü','ý','ÿ'],
            ['a','a','a','a','a','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','u','u','u','u','y','y'],
            $slug
        );
        
        // Remplacer les caractères spéciaux et espaces par des tirets
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        
        // Enlever les tirets au début et à la fin
        $slug = trim($slug, '-');
        
        return $slug;
    }
};