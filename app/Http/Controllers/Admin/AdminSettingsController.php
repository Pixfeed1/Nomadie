<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminSettingsController extends Controller
{
    /**
     * Display settings page.
     */
    public function index()
    {
        // Récupérer les paramètres depuis le cache ou la base de données
        $settings = [
            'site_name' => config('app.name'),
            'site_description' => Cache::get('settings.site_description', ''),
            'contact_email' => Cache::get('settings.contact_email', config('mail.from.address')),
            'phone' => Cache::get('settings.phone', ''),
            'address' => Cache::get('settings.address', ''),

            // SEO
            'seo_title' => Cache::get('settings.seo_title', config('app.name')),
            'seo_description' => Cache::get('settings.seo_description', ''),
            'seo_keywords' => Cache::get('settings.seo_keywords', ''),

            // Email
            'smtp_host' => config('mail.mailers.smtp.host'),
            'smtp_port' => config('mail.mailers.smtp.port'),
            'smtp_username' => config('mail.mailers.smtp.username'),

            // Réseaux sociaux
            'facebook_url' => Cache::get('settings.facebook_url', ''),
            'twitter_url' => Cache::get('settings.twitter_url', ''),
            'instagram_url' => Cache::get('settings.instagram_url', ''),
            'linkedin_url' => Cache::get('settings.linkedin_url', ''),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update general settings.
     */
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'site_description' => 'nullable|string|max:500',
            'contact_email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:200',
            'facebook_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
        ]);

        foreach ($request->except('_token', '_method') as $key => $value) {
            Cache::put("settings.{$key}", $value, now()->addYears(10));
        }

        return back()->with('success', 'Paramètres généraux mis à jour avec succès.');
    }

    /**
     * Update SEO settings.
     */
    public function updateSeo(Request $request)
    {
        $request->validate([
            'seo_title' => 'nullable|string|max:100',
            'seo_description' => 'nullable|string|max:200',
            'seo_keywords' => 'nullable|string|max:500',
        ]);

        Cache::put('settings.seo_title', $request->seo_title, now()->addYears(10));
        Cache::put('settings.seo_description', $request->seo_description, now()->addYears(10));
        Cache::put('settings.seo_keywords', $request->seo_keywords, now()->addYears(10));

        return back()->with('success', 'Paramètres SEO mis à jour avec succès.');
    }

    /**
     * Update email settings.
     */
    public function updateEmail(Request $request)
    {
        $request->validate([
            'smtp_host' => 'required|string',
            'smtp_port' => 'required|integer',
            'smtp_username' => 'required|string',
            'smtp_password' => 'nullable|string',
        ]);

        // Note: Ces paramètres nécessitent une modification du fichier .env
        // Pour la sécurité, on pourrait stocker ça dans une table settings

        return back()->with('info', 'Pour modifier les paramètres email, veuillez éditer le fichier .env');
    }
}
