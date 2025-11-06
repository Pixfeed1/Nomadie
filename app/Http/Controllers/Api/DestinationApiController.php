<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Continent;
use App\Models\Country;
use App\Models\TravelType;
use Illuminate\Http\Request;

class DestinationApiController extends Controller
{
    /**
     * Récupère les pays d'un continent avec filtres
     */
    public function getCountriesByContinent(Request $request, $continentSlug)
    {
        try {
            // Si tous les continents sont demandés
            if ($continentSlug === 'all') {
                $query = Country::whereNotNull('continent_id')
                    ->whereNotNull('slug')
                    ->whereNotNull('image');
                    
                // Filtrer par type de voyage si spécifié
                if ($request->has('travel_type') && $request->travel_type !== 'all') {
                    $query->whereHas('travelTypes', function($q) use ($request) {
                        $q->where('slug', $request->travel_type);
                    });
                }
                
                // Recherche par terme
                if ($request->has('search') && !empty($request->search)) {
                    $search = $request->search;
                    $query->where(function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%")
                          ->orWhereJsonContains('tags', $search);
                    });
                }
                
                // Tri
                $sortField = $request->sort_by ?? 'popular';
                $sortDirection = 'desc';
                
                if ($sortField === 'name') {
                    $sortDirection = 'asc';
                }
                
                if ($sortField === 'popular') {
                    $query->orderBy('popular', 'desc');
                } else {
                    $query->orderBy($sortField, $sortDirection);
                }
                
                $countries = $query->with(['continent', 'travelTypes'])->get();
                
                return response()->json([
                    'success' => true,
                    'continent' => null,
                    'countries' => $countries // Format attendu par le JavaScript
                ]);
            }
            
            // Récupérer le continent par son slug
            $continent = Continent::where('slug', $continentSlug)->firstOrFail();
            
            $query = $continent->countries();
            
            // Filtrer par type de voyage si spécifié
            if ($request->has('travel_type') && $request->travel_type !== 'all') {
                $query->whereHas('travelTypes', function($q) use ($request) {
                    $q->where('slug', $request->travel_type);
                });
            }
            
            // Recherche par terme
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereJsonContains('tags', $search);
                });
            }
            
            // Tri
            $sortField = $request->sort_by ?? 'popular';
            $sortDirection = 'desc';
            
            if ($sortField === 'name') {
                $sortDirection = 'asc';
            }
            
            if ($sortField === 'popular') {
                $query->orderBy('popular', 'desc');
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
            
            // Assurez-vous que les pays ont des données complètes
            $query->whereNotNull('slug')
                  ->whereNotNull('image');
            
            $countries = $query->with('travelTypes')->get();
            
            return response()->json([
                'success' => true,
                'continent' => $continent,
                'countries' => $countries // Format attendu par le JavaScript
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Continent non trouvé ou erreur de base de données: ' . $e->getMessage(),
                'continent' => null,
                'countries' => []
            ]);
        }
    }
    
    /**
     * Récupère les destinations en vedette
     */
    public function getFeaturedDestinations()
    {
        try {
            $featuredDestinations = Country::where('popular', true)
                ->whereNotNull('continent_id')
                ->whereNotNull('slug')
                ->whereNotNull('image')
                ->with('continent', 'travelTypes')
                ->orderByDesc('rating')
                ->take(3)
                ->get();
                
            return response()->json([
                'success' => true,
                'featuredDestinations' => $featuredDestinations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des destinations en vedette: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Recherche de pays par terme
     */
    public function searchCountries(Request $request)
    {
        try {
            $search = $request->search;
            
            if (empty($search)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun terme de recherche fourni',
                    'countries' => []
                ]);
            }
            
            $countries = Country::whereNotNull('continent_id')
                ->whereNotNull('slug')
                ->whereNotNull('image')
                ->where(function($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereJsonContains('tags', $search);
                })
                ->with('continent', 'travelTypes')
                ->take(10)
                ->get();
                
            return response()->json([
                'success' => true,
                'searchTerm' => $search,
                'countries' => $countries // Format attendu par le JavaScript
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la recherche des pays: ' . $e->getMessage(),
                'countries' => []
            ]);
        }
    }
    
    /**
     * Récupère les filtres disponibles
     */
    public function getFilters()
    {
        try {
            $continents = Continent::all();
            $travelTypes = TravelType::all();
            
            return response()->json([
                'success' => true,
                'continents' => $continents,
                'travelTypes' => $travelTypes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des filtres: ' . $e->getMessage(),
                'continents' => [],
                'travelTypes' => []
            ]);
        }
    }
}