<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminCountryController extends Controller
{
    /**
     * Liste de tous les pays
     */
    public function index(Request $request)
    {
        $query = Country::query();

        // Filtrer par pays avec expériences
        if ($request->filled('with_trips')) {
            $query->has('trips');
        }

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Trier par nombre d'expériences
        $countries = $query->withCount('trips')
            ->orderByDesc('trips_count')
            ->orderBy('name')
            ->paginate(20);

        $stats = [
            'total_countries' => Country::count(),
            'countries_with_trips' => Country::has('trips')->count(),
            'countries_with_images' => Country::whereNotNull('image')->count(),
        ];

        return view('admin.countries.index', compact('countries', 'stats'));
    }

    /**
     * Formulaire d'édition d'un pays
     */
    public function edit(Country $country)
    {
        return view('admin.countries.edit', compact('country'));
    }

    /**
     * Mettre à jour un pays
     */
    public function update(Request $request, Country $country)
    {
        $request->validate([
            'image' => 'nullable|image|max:5120', // 5MB max
            'description' => 'nullable|string|max:1000',
        ]);

        $data = $request->only(['description']);

        // Gestion de l'image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($country->image) {
                Storage::disk('public')->delete($country->image);
            }

            // Enregistrer la nouvelle image
            $path = $request->file('image')->store('countries', 'public');
            $data['image'] = $path;
        }

        // Si case "supprimer image" cochée
        if ($request->has('remove_image') && $request->remove_image) {
            if ($country->image) {
                Storage::disk('public')->delete($country->image);
            }
            $data['image'] = null;
        }

        $country->update($data);

        return redirect()->route('admin.countries.index')
            ->with('success', 'Pays mis à jour avec succès');
    }
}
