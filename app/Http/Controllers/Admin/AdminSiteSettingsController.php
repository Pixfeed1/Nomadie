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
            'title' => SiteSetting::get('writer_banner_title'),
            'subtitle' => SiteSetting::get('writer_banner_subtitle'),
            'feature1_title' => SiteSetting::get('writer_banner_feature1_title'),
            'feature1_desc' => SiteSetting::get('writer_banner_feature1_desc'),
            'feature2_title' => SiteSetting::get('writer_banner_feature2_title'),
            'feature2_desc' => SiteSetting::get('writer_banner_feature2_desc'),
            'feature3_title' => SiteSetting::get('writer_banner_feature3_title'),
            'feature3_desc' => SiteSetting::get('writer_banner_feature3_desc'),
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
            'writer_banner_title' => 'required|string|max:200',
            'writer_banner_subtitle' => 'nullable|string|max:500',
            'writer_banner_feature1_title' => 'required|string|max:100',
            'writer_banner_feature1_desc' => 'nullable|string|max:200',
            'writer_banner_feature2_title' => 'required|string|max:100',
            'writer_banner_feature2_desc' => 'nullable|string|max:200',
            'writer_banner_feature3_title' => 'required|string|max:100',
            'writer_banner_feature3_desc' => 'nullable|string|max:200',
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

        // Mettre à jour le bandeau rédacteurs
        SiteSetting::set('writer_banner_title', $request->writer_banner_title, 'text');
        SiteSetting::set('writer_banner_subtitle', $request->writer_banner_subtitle, 'text');
        SiteSetting::set('writer_banner_feature1_title', $request->writer_banner_feature1_title, 'text');
        SiteSetting::set('writer_banner_feature1_desc', $request->writer_banner_feature1_desc, 'text');
        SiteSetting::set('writer_banner_feature2_title', $request->writer_banner_feature2_title, 'text');
        SiteSetting::set('writer_banner_feature2_desc', $request->writer_banner_feature2_desc, 'text');
        SiteSetting::set('writer_banner_feature3_title', $request->writer_banner_feature3_title, 'text');
        SiteSetting::set('writer_banner_feature3_desc', $request->writer_banner_feature3_desc, 'text');

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
