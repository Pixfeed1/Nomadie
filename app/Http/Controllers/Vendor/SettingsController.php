<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Models\Vendor;
use App\Models\Payment;
use App\Models\Country;
use App\Models\ServiceCategory;
use App\Models\ServiceAttribute;

class SettingsController extends Controller
{
    /**
     * Afficher la page des paramètres (vue d'ensemble)
     */
    public function index()
    {
        $vendor = Auth::user()->vendor;
        
        // Calculer les statistiques du compte
        $accountStats = [
            'total_trips' => $vendor->trips()->count(),
            'active_trips' => $vendor->trips()->where('status', 'active')->count(),
            'profile_completion' => $this->calculateProfileCompletion($vendor),
            'countries_count' => $vendor->countries()->count(),
            'services_count' => $vendor->serviceCategories()->count(),
            // ✅ AJOUT : Nouvelles stats destinations
            'plan_limits' => $vendor->plan_limits,
        ];

        return view('vendor.settings.index', compact('vendor', 'accountStats'));
    }

    /**
     * Afficher la page du profil entreprise
     */
    public function profile()
    {
        $vendor = Auth::user()->vendor;
        
        return view('vendor.settings.profile', compact('vendor'));
    }

    /**
     * Afficher la page des destinations et services
     */
    public function destinations()
    {
        $vendor = Auth::user()->vendor;
        
        // Charger les relations nécessaires
        $vendor->load(['countries', 'serviceCategories', 'serviceAttributes']);
        
        // Données pour les formulaires
        $allCountries = Country::orderBy('name')->get();
        $allServiceCategories = ServiceCategory::where('is_active', true)->get();
        $allServiceAttributes = ServiceAttribute::where('is_active', true)->get()->groupBy('type');

        // ✅ AJOUT : Informations sur les limites pour l'interface
        $planLimits = $vendor->plan_limits;

        return view('vendor.settings.destinations', compact(
            'vendor',
            'allCountries',
            'allServiceCategories',
            'allServiceAttributes',
            'planLimits'
        ));
    }

    /**
     * Afficher la page de sécurité et compte
     */
    public function security()
    {
        $vendor = Auth::user()->vendor;
        
        // Calculer les statistiques pour la suppression
        $accountStats = [
            'total_trips' => $vendor->trips()->count(),
            'total_bookings' => $vendor->bookings()->count() ?? 0,
            'total_revenue' => Payment::where('payable_type', 'App\Models\Vendor')
                ->where('payable_id', $vendor->id)
                ->where('status', 'succeeded')
                ->sum('amount') ?? 0,
        ];

        return view('vendor.settings.security', compact('vendor', 'accountStats'));
    }

    /**
     * Afficher la page de gestion de l'abonnement
     */
    public function subscription()
    {
        $vendor = Auth::user()->vendor;
        
        // Récupérer l'historique des paiements récents
        $recentPayments = Payment::where('payable_type', 'App\Models\Vendor')
            ->where('payable_id', $vendor->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // ✅ AMÉLIORATION : Statistiques d'utilisation enrichies
        $accountStats = [
            'total_trips' => $vendor->trips()->count(),
            'active_trips' => $vendor->trips()->where('status', 'active')->count(),
            'commission_rate' => $vendor->commission_rate,
            'plan_limits' => $vendor->plan_limits,
        ];

        return view('vendor.settings.subscription', compact(
            'vendor',
            'recentPayments',
            'accountStats'
        ));
    }

    /**
     * Mettre à jour le profil vendeur
     */
    public function updateProfile(Request $request)
    {
        $vendor = Auth::user()->vendor;

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'legal_status' => 'required|string|max:50',
            'siret' => 'required|string|size:14|unique:vendors,siret,' . $vendor->id,
            'vat' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'nullable|string|max:500',
            'postal_code' => 'required|string|max:10',
            'city' => 'required|string|max:100',
            'country' => 'required|string|size:2',
            'description' => 'nullable|string|max:1000',
            'experience' => 'nullable|string|in:1,1-3,3-5,5-10,10+',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'rep_firstname' => 'required|string|max:100',
            'rep_lastname' => 'required|string|max:100',
            'rep_position' => 'required|string|max:100',
            'rep_email' => 'required|email|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Upload du logo si fourni
            if ($request->hasFile('logo')) {
                // Supprimer l'ancien logo
                if ($vendor->logo) {
                    Storage::disk('public')->delete($vendor->logo);
                }

                // Stocker le nouveau logo
                $logoPath = $request->file('logo')->store('vendors/logos', 'public');
                $validated['logo'] = $logoPath;
            }

            // Mettre à jour les données du vendeur
            $vendor->update($validated);

            DB::commit();

            return redirect()->route('vendor.settings.profile')
                ->with('success', 'Profil mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour du profil vendeur: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour du profil.')
                ->withInput();
        }
    }

    /**
     * Mettre à jour les informations de contact du représentant
     */
    public function updateRepresentative(Request $request)
    {
        $vendor = Auth::user()->vendor;

        $validated = $request->validate([
            'rep_firstname' => 'required|string|max:100',
            'rep_lastname' => 'required|string|max:100',
            'rep_position' => 'required|string|max:100',
            'rep_email' => 'required|email|max:255',
            'rep_phone' => 'nullable|string|max:20',
        ]);

        try {
            $vendor->update($validated);

            return redirect()->route('vendor.settings.profile')
                ->with('success', 'Informations du représentant mises à jour.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du représentant: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour.')
                ->withInput();
        }
    }

    /**
     * ✅ MÉTHODE MISE À JOUR : Mettre à jour les pays avec validation des limites
     */
    public function updateDestinations(Request $request)
    {
        $vendor = Auth::user()->vendor;

        $validated = $request->validate([
            'countries' => 'required|array|min:1',
            'countries.*' => 'exists:countries,id'
        ]);

        try {
            // ✅ VALIDATION 1 : Vérifier le nombre maximum de destinations
            $selectedCount = count($validated['countries']);
            if ($selectedCount > $vendor->max_destinations) {
                return redirect()->back()
                    ->with('error', "Votre forfait {$vendor->subscription_plan} limite à {$vendor->max_destinations} destinations. Vous avez sélectionné {$selectedCount} destinations.")
                    ->with('upgrade_needed', true)
                    ->withInput();
            }

            // ✅ VALIDATION 2 : Vérifier si des modifications sont autorisées ce mois
            if (!$vendor->canModifyDestinations()) {
                $remainingChanges = $vendor->getDestinationChangesRemaining();
                return redirect()->back()
                    ->with('error', "Vous avez atteint votre limite de modifications ce mois ({$remainingChanges} restantes). Passez au plan Pro pour des modifications illimitées.")
                    ->with('upgrade_needed', true)
                    ->withInput();
            }

            DB::beginTransaction();

            // Vérifier s'il y a eu des changements
            $currentCountries = $vendor->countries()->pluck('countries.id')->sort()->values();
            $newCountries = collect($validated['countries'])->sort()->values();
            
            $hasChanges = !$currentCountries->equals($newCountries);

            // Synchroniser les pays
            $vendor->countries()->sync($validated['countries']);

            // ✅ INCRÉMENTER le compteur seulement s'il y a eu des changements
            if ($hasChanges && $vendor->subscription_plan !== 'pro') {
                $vendor->incrementDestinationChanges();
            }

            DB::commit();

            Log::info('Destinations updated for vendor', [
                'vendor_id' => $vendor->id,
                'countries_count' => count($validated['countries']),
                'had_changes' => $hasChanges,
                'changes_this_month' => $vendor->destinations_changes_this_month + ($hasChanges ? 1 : 0)
            ]);

            $message = 'Pays d\'activité mis à jour avec succès.';
            if ($hasChanges && $vendor->subscription_plan !== 'pro') {
                $remaining = $vendor->getDestinationChangesRemaining() - 1;
                if ($remaining > 0) {
                    $message .= " ({$remaining} modifications restantes ce mois)";
                } else {
                    $message .= " (Limite de modifications atteinte ce mois)";
                }
            }

            return redirect()->route('vendor.settings.destinations')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour des destinations: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour des destinations.')
                ->withInput();
        }
    }

    /**
     * Mettre à jour les services
     */
    public function updateServices(Request $request)
    {
        $vendor = Auth::user()->vendor;

        $validated = $request->validate([
            'service_categories' => 'nullable|array|max:3',
            'service_categories.*' => 'exists:service_categories,id',
            'service_attributes' => 'nullable|array',
            'service_attributes.*' => 'exists:service_attributes,id'
        ]);

        try {
            DB::beginTransaction();

            // Synchroniser les catégories de services
            if (isset($validated['service_categories'])) {
                $vendor->serviceCategories()->sync($validated['service_categories']);
            }

            // Synchroniser les attributs de services
            if (isset($validated['service_attributes'])) {
                $vendor->serviceAttributes()->sync($validated['service_attributes']);
            }

            DB::commit();

            return redirect()->route('vendor.settings.destinations')
                ->with('success', 'Services mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour des services: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour des services.')
                ->withInput();
        }
    }

    /**
     * Mettre à jour le mot de passe
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed|different:current_password',
        ]);

        $user = Auth::user();

        // Vérifier le mot de passe actuel
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        try {
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            return redirect()->route('vendor.settings.security')
                ->with('success', 'Mot de passe mis à jour avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du mot de passe: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour du mot de passe.');
        }
    }

    /**
     * Mettre à jour les préférences de notification
     */
    public function updateNotifications(Request $request)
    {
        $vendor = Auth::user()->vendor;

        $validated = $request->validate([
            'newsletter' => 'boolean',
            'notify_new_booking' => 'boolean',
            'notify_payment' => 'boolean',
            'notify_review' => 'boolean',
        ]);

        try {
            // Mettre à jour les préférences dans la table vendors
            $vendor->update([
                'newsletter' => $request->has('newsletter')
            ]);

            return redirect()->route('vendor.settings.security')
                ->with('success', 'Préférences de notification mises à jour.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour des notifications: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour des préférences.');
        }
    }

    /**
     * ✅ MÉTHODE AMÉLIORÉE : Gérer l'abonnement avec mise à jour des limites
     */
    public function manageSubscription(Request $request)
    {
        $vendor = Auth::user()->vendor;
        $newPlan = $request->input('plan');

        // Valider le plan
        if (!in_array($newPlan, ['free', 'essential', 'pro'])) {
            return redirect()->back()
                ->with('error', 'Plan d\'abonnement invalide.');
        }

        try {
            if ($newPlan === 'free') {
                // Vérifier si le vendor peut downgrader (voyages)
                if ($vendor->trips()->count() > 5) {
                    return redirect()->back()
                        ->with('error', 'Vous devez avoir 5 voyages ou moins pour passer au plan gratuit.');
                }

                // ✅ AJOUT : Vérifier les destinations
                if ($vendor->countries()->count() > 3) {
                    return redirect()->back()
                        ->with('error', 'Vous devez avoir 3 destinations ou moins pour passer au plan gratuit.');
                }

                // Downgrade vers gratuit
                $vendor->update([
                    'subscription_plan' => 'free',
                    'max_trips' => 5,
                    'max_destinations' => 3,
                    'stripe_subscription_id' => null,
                    'subscription_ends_at' => null,
                ]);

                return redirect()->route('vendor.settings.subscription')
                    ->with('success', 'Abonnement modifié vers le plan gratuit.');

            } else {
                // Upgrade vers plan payant - rediriger vers Stripe
                return redirect()->route('vendor.subscription.index', ['plan' => $newPlan]);
            }

        } catch (\Exception $e) {
            Log::error('Erreur lors de la gestion de l\'abonnement: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Erreur lors de la modification de l\'abonnement.');
        }
    }

    /**
     * Supprimer le compte vendeur
     */
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required',
            'confirmation' => 'required|in:SUPPRIMER',
        ]);

        $user = Auth::user();
        $vendor = $user->vendor;

        // Vérifier le mot de passe
        if (!Hash::check($request->password, $user->password)) {
            return redirect()->back()
                ->withErrors(['password' => 'Mot de passe incorrect.']);
        }

        try {
            DB::beginTransaction();

            // Supprimer tous les voyages du vendeur
            foreach ($vendor->trips as $trip) {
                if ($trip->image) {
                    Storage::disk('public')->delete($trip->image);
                }
                $trip->delete();
            }

            // Supprimer le logo du vendeur
            if ($vendor->logo) {
                Storage::disk('public')->delete($vendor->logo);
            }

            // Détacher toutes les relations
            $vendor->countries()->detach();
            $vendor->serviceCategories()->detach();
            $vendor->serviceAttributes()->detach();

            // Supprimer les données vendeur
            $vendor->delete();

            // Supprimer l'utilisateur
            $user->delete();

            DB::commit();

            Auth::logout();

            return redirect()->route('home')
                ->with('success', 'Votre compte a été supprimé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression du compte: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression du compte.');
        }
    }

    /**
     * ✅ MÉTHODE AMÉLIORÉE : Calculer le pourcentage de completion du profil
     */
    private function calculateProfileCompletion($vendor)
    {
        $fields = [
            'logo' => $vendor->logo ? 1 : 0,
            'description' => $vendor->description ? 1 : 0,
            'website' => $vendor->website ? 1 : 0,
            'phone' => $vendor->phone ? 1 : 0,
            'address' => $vendor->address ? 1 : 0,
            'countries' => $vendor->countries()->count() > 0 ? 1 : 0,
            'services' => $vendor->serviceCategories()->count() > 0 ? 1 : 0,
            // ✅ AJOUT : Bonus pour avoir utilisé ses limites
            'destinations_optimized' => $vendor->countries()->count() >= min(3, $vendor->max_destinations) ? 1 : 0,
        ];

        $completed = array_sum($fields);
        $total = count($fields);

        return round(($completed / $total) * 100);
    }

    /**
     * Exporter les données du vendeur (RGPD)
     */
    public function exportData()
    {
        $vendor = Auth::user()->vendor;
        $user = Auth::user();

        $vendor->load(['countries', 'serviceCategories', 'serviceAttributes', 'trips']);

        $data = [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
            ],
            'vendor' => [
                'company' => $vendor->company_name,
                'siret' => $vendor->siret,
                'email' => $vendor->email,
                'phone' => $vendor->phone,
                'address' => $vendor->address,
                'city' => $vendor->city,
                'postal_code' => $vendor->postal_code,
                'country' => $vendor->country,
                'subscription_plan' => $vendor->subscription_plan,
                'max_destinations' => $vendor->max_destinations,
                'destinations_changes_this_month' => $vendor->destinations_changes_this_month,
                'created_at' => $vendor->created_at->format('Y-m-d H:i:s'),
            ],
            'countries' => $vendor->countries->pluck('name')->toArray(),
            'services' => [
                'categories' => $vendor->serviceCategories->pluck('name')->toArray(),
                'attributes' => $vendor->serviceAttributes->pluck('name')->toArray(),
            ],
            'trips' => $vendor->trips->map(function($trip) {
                return [
                    'title' => $trip->title,
                    'price' => $trip->price,
                    'created_at' => $trip->created_at->format('Y-m-d H:i:s'),
                ];
            })->toArray(),
            'payments' => Payment::where('payable_type', 'App\Models\Vendor')
                ->where('payable_id', $vendor->id)
                ->get()
                ->map(function($payment) {
                    return [
                        'amount' => $payment->amount,
                        'status' => $payment->status,
                        'date' => $payment->created_at->format('Y-m-d H:i:s'),
                    ];
                })
                ->toArray(),
            'plan_limits' => $vendor->plan_limits,
        ];

        $filename = 'vendor_data_' . $vendor->id . '_' . now()->format('Y-m-d') . '.json';

        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $filename, [
            'Content-Type' => 'application/json',
        ]);
    }
}