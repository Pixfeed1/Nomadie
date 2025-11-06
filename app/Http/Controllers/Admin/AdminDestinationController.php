<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Destination;
use App\Models\Trip;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;

class AdminDestinationController extends Controller
{
    /**
     * Display a listing of the destinations.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Récupérer tous les continents avec leurs pays (destinations)
        $continents = Destination::select('continent')
            ->distinct()
            ->orderBy('continent')
            ->get()
            ->map(function ($item) {
                $destinations = Destination::where('continent', $item->continent)
                    ->orderBy('country')
                    ->get();
                
                // Calculer pour chaque pays le nombre de voyages et de vendeurs
                foreach ($destinations as $destination) {
                    $destination->trip_count = Trip::where('destination_id', $destination->id)->count();
                    $destination->vendor_count = Trip::where('destination_id', $destination->id)
                        ->distinct('vendor_id')
                        ->count('vendor_id');
                }
                
                return [
                    'name' => $item->continent,
                    'countries_count' => $destinations->count(),
                    'destinations' => $destinations
                ];
            });
        
        // Statistiques pour le dashboard de destinations
        $stats = [
            'total_continents' => $continents->count(),
            'total_countries' => Destination::distinct('country')->count(),
            'total_cities' => Destination::count(),
            // Correction de la requête qui génère l'erreur SQL
            'most_popular' => Destination::select(
                    'destinations.id', 
                    'destinations.continent', 
                    'destinations.country', 
                    'destinations.city',
                    DB::raw('COUNT(trips.id) as trip_count')
                )
                ->leftJoin('trips', 'destinations.id', '=', 'trips.destination_id')
                ->groupBy('destinations.id', 'destinations.continent', 'destinations.country', 'destinations.city')
                ->orderBy('trip_count', 'desc')
                ->first()
        ];
        
        // Récupérer tous les vendeurs pour les modales
        $vendors = Vendor::orderBy('name')->get();
        
        return view('admin.destinations.index', compact('continents', 'stats', 'vendors'));
    }
    
    /**
     * Store a newly created destination in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'continent' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'city' => 'sometimes|nullable|string|max:255',
        ]);
        
        Destination::create($request->all());
        
        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destination ajoutée avec succès');
    }
    
    /**
     * Update the specified destination in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'continent' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'city' => 'sometimes|nullable|string|max:255',
        ]);
        
        $destination = Destination::findOrFail($id);
        $destination->update($request->all());
        
        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destination mise à jour avec succès');
    }
    
    /**
     * Remove the specified destination from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $destination = Destination::findOrFail($id);
        $destination->delete();
        
        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destination supprimée avec succès');
    }
}