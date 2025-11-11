<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSiteSettingsController extends Controller
{
    /**
     * Afficher la page de gestion du bandeau d'accueil
     */
    public function heroBanner()
    {
        $settings = [
            'image' => SiteSetting::get('hero_banner_image'),
            'title' => SiteSetting::get('hero_banner_title'),
            'subtitle' => SiteSetting::get('hero_banner_subtitle'),
        ];

        return view('admin.settings.hero-banner', compact('settings'));
    }

    /**
     * Mettre à jour les paramètres du bandeau d'accueil
     */
    public function updateHeroBanner(Request $request)
    {
        $request->validate([
            'hero_banner_image' => 'nullable|image|max:5120', // 5MB max
            'hero_banner_title' => 'required|string|max:200',
            'hero_banner_subtitle' => 'nullable|string|max:500',
        ]);

        // Mettre à jour le titre et sous-titre
        SiteSetting::set('hero_banner_title', $request->hero_banner_title, 'text');
        SiteSetting::set('hero_banner_subtitle', $request->hero_banner_subtitle, 'text');

        // Gestion de l'image
        if ($request->hasFile('hero_banner_image')) {
            // Supprimer l'ancienne image si elle existe
            $oldImage = SiteSetting::get('hero_banner_image');
            if ($oldImage && $oldImage !== 'images/hero-bg.jpg') {
                Storage::disk('public')->delete($oldImage);
            }

            // Enregistrer la nouvelle image
            $path = $request->file('hero_banner_image')->store('banners', 'public');
            SiteSetting::set('hero_banner_image', $path, 'image');
        }

        // Si case "supprimer image" cochée
        if ($request->has('remove_image') && $request->remove_image) {
            $oldImage = SiteSetting::get('hero_banner_image');
            if ($oldImage && $oldImage !== 'images/hero-bg.jpg') {
                Storage::disk('public')->delete($oldImage);
            }
            SiteSetting::set('hero_banner_image', null, 'image');
        }

        return back()->with('success', 'Bandeau d\'accueil mis à jour avec succès');
    }
}
