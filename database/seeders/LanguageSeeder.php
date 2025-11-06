<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = [
            // Langues européennes principales
            ['code' => 'fr', 'name' => 'Français', 'native_name' => 'Français', 'region' => 'France', 'is_popular' => true, 'sort_order' => 1],
            ['code' => 'en', 'name' => 'Anglais', 'native_name' => 'English', 'region' => 'International', 'is_popular' => true, 'sort_order' => 2],
            ['code' => 'es', 'name' => 'Espagnol', 'native_name' => 'Español', 'region' => 'Espagne', 'is_popular' => true, 'sort_order' => 3],
            ['code' => 'de', 'name' => 'Allemand', 'native_name' => 'Deutsch', 'region' => 'Allemagne', 'is_popular' => true, 'sort_order' => 4],
            ['code' => 'it', 'name' => 'Italien', 'native_name' => 'Italiano', 'region' => 'Italie', 'is_popular' => true, 'sort_order' => 5],
            ['code' => 'pt', 'name' => 'Portugais', 'native_name' => 'Português', 'region' => 'Portugal', 'is_popular' => true, 'sort_order' => 6],
            ['code' => 'nl', 'name' => 'Néerlandais', 'native_name' => 'Nederlands', 'region' => 'Pays-Bas', 'is_popular' => false, 'sort_order' => 7],
            ['code' => 'ru', 'name' => 'Russe', 'native_name' => 'Русский', 'region' => 'Russie', 'is_popular' => true, 'sort_order' => 8],
            
            // Variantes régionales
            ['code' => 'pt-BR', 'name' => 'Portugais', 'native_name' => 'Português', 'region' => 'Brésil', 'is_popular' => false, 'sort_order' => 9],
            ['code' => 'en-US', 'name' => 'Anglais', 'native_name' => 'English', 'region' => 'États-Unis', 'is_popular' => false, 'sort_order' => 10],
            ['code' => 'en-GB', 'name' => 'Anglais', 'native_name' => 'English', 'region' => 'Royaume-Uni', 'is_popular' => false, 'sort_order' => 11],
            ['code' => 'es-MX', 'name' => 'Espagnol', 'native_name' => 'Español', 'region' => 'Mexique', 'is_popular' => false, 'sort_order' => 12],
            
            // Langues asiatiques
            ['code' => 'zh-CN', 'name' => 'Mandarin (Simplifié)', 'native_name' => '普通话', 'region' => 'Chine', 'is_popular' => true, 'sort_order' => 20],
            ['code' => 'zh-TW', 'name' => 'Mandarin (Traditionnel)', 'native_name' => '國語', 'region' => 'Taiwan', 'is_popular' => false, 'sort_order' => 21],
            ['code' => 'yue', 'name' => 'Cantonais', 'native_name' => '廣東話', 'region' => 'Hong Kong', 'is_popular' => false, 'sort_order' => 22],
            ['code' => 'ja', 'name' => 'Japonais', 'native_name' => '日本語', 'region' => 'Japon', 'is_popular' => true, 'sort_order' => 23],
            ['code' => 'ko', 'name' => 'Coréen', 'native_name' => '한국어', 'region' => 'Corée du Sud', 'is_popular' => false, 'sort_order' => 24],
            ['code' => 'th', 'name' => 'Thaï', 'native_name' => 'ไทย', 'region' => 'Thaïlande', 'is_popular' => false, 'sort_order' => 25],
            ['code' => 'vi', 'name' => 'Vietnamien', 'native_name' => 'Tiếng Việt', 'region' => 'Vietnam', 'is_popular' => false, 'sort_order' => 26],
            ['code' => 'id', 'name' => 'Indonésien', 'native_name' => 'Bahasa Indonesia', 'region' => 'Indonésie', 'is_popular' => false, 'sort_order' => 27],
            ['code' => 'ms', 'name' => 'Malais', 'native_name' => 'Bahasa Melayu', 'region' => 'Malaisie', 'is_popular' => false, 'sort_order' => 28],
            ['code' => 'hi', 'name' => 'Hindi', 'native_name' => 'हिन्दी', 'region' => 'Inde', 'is_popular' => false, 'sort_order' => 29],
            ['code' => 'bn', 'name' => 'Bengali', 'native_name' => 'বাংলা', 'region' => 'Bangladesh', 'is_popular' => false, 'sort_order' => 30],
            
            // Langues du Moyen-Orient et Afrique
            ['code' => 'ar', 'name' => 'Arabe', 'native_name' => 'العربية', 'region' => 'Moyen-Orient', 'is_popular' => true, 'sort_order' => 40],
            ['code' => 'he', 'name' => 'Hébreu', 'native_name' => 'עברית', 'region' => 'Israël', 'is_popular' => false, 'sort_order' => 41],
            ['code' => 'tr', 'name' => 'Turc', 'native_name' => 'Türkçe', 'region' => 'Turquie', 'is_popular' => false, 'sort_order' => 42],
            ['code' => 'fa', 'name' => 'Persan', 'native_name' => 'فارسی', 'region' => 'Iran', 'is_popular' => false, 'sort_order' => 43],
            ['code' => 'sw', 'name' => 'Swahili', 'native_name' => 'Kiswahili', 'region' => 'Afrique de l\'Est', 'is_popular' => false, 'sort_order' => 44],
            
            // Langues nordiques
            ['code' => 'sv', 'name' => 'Suédois', 'native_name' => 'Svenska', 'region' => 'Suède', 'is_popular' => false, 'sort_order' => 50],
            ['code' => 'no', 'name' => 'Norvégien', 'native_name' => 'Norsk', 'region' => 'Norvège', 'is_popular' => false, 'sort_order' => 51],
            ['code' => 'da', 'name' => 'Danois', 'native_name' => 'Dansk', 'region' => 'Danemark', 'is_popular' => false, 'sort_order' => 52],
            ['code' => 'fi', 'name' => 'Finnois', 'native_name' => 'Suomi', 'region' => 'Finlande', 'is_popular' => false, 'sort_order' => 53],
            ['code' => 'is', 'name' => 'Islandais', 'native_name' => 'Íslenska', 'region' => 'Islande', 'is_popular' => false, 'sort_order' => 54],
            
            // Langues régionales françaises
            ['code' => 'oc', 'name' => 'Occitan', 'native_name' => 'Occitan', 'region' => 'France (Sud)', 'is_popular' => false, 'sort_order' => 60],
            ['code' => 'br', 'name' => 'Breton', 'native_name' => 'Brezhoneg', 'region' => 'Bretagne', 'is_popular' => false, 'sort_order' => 61],
            ['code' => 'co', 'name' => 'Corse', 'native_name' => 'Corsu', 'region' => 'Corse', 'is_popular' => false, 'sort_order' => 62],
            ['code' => 'eu', 'name' => 'Basque', 'native_name' => 'Euskara', 'region' => 'Pays Basque', 'is_popular' => false, 'sort_order' => 63],
            ['code' => 'ca', 'name' => 'Catalan', 'native_name' => 'Català', 'region' => 'Catalogne', 'is_popular' => false, 'sort_order' => 64],
            
            // Autres langues européennes
            ['code' => 'pl', 'name' => 'Polonais', 'native_name' => 'Polski', 'region' => 'Pologne', 'is_popular' => false, 'sort_order' => 70],
            ['code' => 'cs', 'name' => 'Tchèque', 'native_name' => 'Čeština', 'region' => 'République Tchèque', 'is_popular' => false, 'sort_order' => 71],
            ['code' => 'sk', 'name' => 'Slovaque', 'native_name' => 'Slovenčina', 'region' => 'Slovaquie', 'is_popular' => false, 'sort_order' => 72],
            ['code' => 'hu', 'name' => 'Hongrois', 'native_name' => 'Magyar', 'region' => 'Hongrie', 'is_popular' => false, 'sort_order' => 73],
            ['code' => 'ro', 'name' => 'Roumain', 'native_name' => 'Română', 'region' => 'Roumanie', 'is_popular' => false, 'sort_order' => 74],
            ['code' => 'bg', 'name' => 'Bulgare', 'native_name' => 'Български', 'region' => 'Bulgarie', 'is_popular' => false, 'sort_order' => 75],
            ['code' => 'el', 'name' => 'Grec', 'native_name' => 'Ελληνικά', 'region' => 'Grèce', 'is_popular' => false, 'sort_order' => 76],
            ['code' => 'hr', 'name' => 'Croate', 'native_name' => 'Hrvatski', 'region' => 'Croatie', 'is_popular' => false, 'sort_order' => 77],
            ['code' => 'sr', 'name' => 'Serbe', 'native_name' => 'Српски', 'region' => 'Serbie', 'is_popular' => false, 'sort_order' => 78],
            ['code' => 'sl', 'name' => 'Slovène', 'native_name' => 'Slovenščina', 'region' => 'Slovénie', 'is_popular' => false, 'sort_order' => 79],
            ['code' => 'uk', 'name' => 'Ukrainien', 'native_name' => 'Українська', 'region' => 'Ukraine', 'is_popular' => false, 'sort_order' => 80],
        ];

        foreach ($languages as $language) {
            Language::create($language);
        }
    }
}