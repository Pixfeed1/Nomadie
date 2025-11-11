<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSiteSettingsController extends Controller
{
    /**
     * Afficher la page de gestion des bandeaux
     */
    public function heroBanner()
    {
        $settings = [
            'image' => SiteSetting::get('hero_banner_image'),
            'title' => SiteSetting::get('hero_banner_title'),
            'subtitle' => SiteSetting::get('hero_banner_subtitle'),
        ];

        $writerSettings = [
            'image' => SiteSetting::get('writer_banner_image'),
        ];

        return view('admin.settings.hero-banner', compact('settings', 'writerSettings'));
    }

    /**
     * Mettre à jour les paramètres des bandeaux
     */
    public function updateHeroBanner(Request $request)
    {
        $request->validate([
            'hero_banner_image' => 'nullable|image|max:5120', // 5MB max
            'hero_banner_title' => 'required|string|max:200',
            'hero_banner_subtitle' => 'nullable|string|max:500',
            'writer_banner_image' => 'nullable|image|max:5120',
        ]);

        // Mettre à jour le bandeau d'accueil
        SiteSetting::set('hero_banner_title', $request->hero_banner_title, 'text');
        SiteSetting::set('hero_banner_subtitle', $request->hero_banner_subtitle, 'text');

        // Gestion de l'image du bandeau d'accueil
        if ($request->hasFile('hero_banner_image')) {
            $oldImage = SiteSetting::get('hero_banner_image');
            if ($oldImage && $oldImage !== 'images/hero-bg.jpg') {
                Storage::disk('public')->delete($oldImage);
            }
            $path = $request->file('hero_banner_image')->store('banners', 'public');
            SiteSetting::set('hero_banner_image', $path, 'image');
        }

        if ($request->has('remove_image') && $request->remove_image) {
            $oldImage = SiteSetting::get('hero_banner_image');
            if ($oldImage && $oldImage !== 'images/hero-bg.jpg') {
                Storage::disk('public')->delete($oldImage);
            }
            SiteSetting::set('hero_banner_image', null, 'image');
        }

        // Gestion de l'image du bandeau rédacteurs
        if ($request->hasFile('writer_banner_image')) {
            $oldImage = SiteSetting::get('writer_banner_image');
            if ($oldImage && $oldImage !== 'images/writer-bg.jpg') {
                Storage::disk('public')->delete($oldImage);
            }
            $path = $request->file('writer_banner_image')->store('banners', 'public');
            SiteSetting::set('writer_banner_image', $path, 'image');
        }

        if ($request->has('remove_writer_image') && $request->remove_writer_image) {
            $oldImage = SiteSetting::get('writer_banner_image');
            if ($oldImage && $oldImage !== 'images/writer-bg.jpg') {
                Storage::disk('public')->delete($oldImage);
            }
            SiteSetting::set('writer_banner_image', null, 'image');
        }

        return back()->with('success', 'Bandeaux mis à jour avec succès');
    }
}
