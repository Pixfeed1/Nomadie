<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\TripAvailability;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TripAvailabilityController extends Controller
{
    /**
     * Afficher la liste des disponibilités d'un voyage
     */
    public function index(Trip $trip)
    {
        // Vérifier que le voyage appartient au vendor
        $this->checkTripOwnership($trip);
        
        $vendor = Auth::user()->vendor;
        
        // Récupérer les disponibilités avec statistiques
        $availabilities = $trip->availabilities()
            ->withCount(['bookings' => function ($query) {
                $query->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_COMPLETED]);
            }])
            ->with(['bookings' => function ($query) {
                $query->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_COMPLETED])
                      ->select('id', 'trip_availability_id', 'status', 'total_amount', 'number_of_travelers');
            }])
            ->orderBy('start_date', 'desc')
            ->paginate(20);
        
        // Calculer les statistiques globales
        $confirmedBookings = Booking::whereHas('availability', function ($query) use ($trip) {
            $query->where('trip_id', $trip->id);
        })->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_COMPLETED]);
        
        $stats = [
            'total_availabilities' => $trip->availabilities()->count(),
            'upcoming' => $trip->availabilities()->upcoming()->count(),
            'past' => $trip->availabilities()->past()->count(),
            'total_capacity' => $trip->availabilities()->sum('total_spots'),
            'total_booked' => $trip->availabilities()->sum('booked_spots'),
            'revenue' => $confirmedBookings->sum('total_amount'),
            'bookings_count' => $confirmedBookings->count(),
            'average_fill_rate' => $this->calculateAverageFillRate($trip),
            'average_price' => $trip->availabilities()->avg('adult_price') ?? 0,
        ];
        
        return view('vendor.trips.availabilities.index', compact('trip', 'availabilities', 'stats', 'vendor'));
    }
    
    /**
     * Afficher le formulaire de création d'une disponibilité
     */
    public function create(Trip $trip)
    {
        $this->checkTripOwnership($trip);
        
        $vendor = Auth::user()->vendor;
        
        // Suggestions de prix basées sur les disponibilités existantes
        $priceSuggestions = [
            'min' => $trip->availabilities()->min('adult_price') ?? $trip->price,
            'max' => $trip->availabilities()->max('adult_price') ?? $trip->price,
            'avg' => $trip->availabilities()->avg('adult_price') ?? $trip->price,
            'last' => $trip->availabilities()->latest()->value('adult_price') ?? $trip->price
        ];
        
        return view('vendor.trips.availabilities.create', compact('trip', 'vendor', 'priceSuggestions'));
    }
    
    /**
     * Enregistrer une nouvelle disponibilité
     */
    public function store(Request $request, Trip $trip)
    {
        $this->checkTripOwnership($trip);
        
        // Validation selon le mode de tarification
        $priceRules = $this->getPriceValidationRules($trip->pricing_mode);
        
        $validated = $request->validate(array_merge([
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_spots' => 'required|integer|min:1|max:200',
            'min_participants' => 'required|integer|min:1|lte:total_spots',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
            'discount_ends_at' => 'nullable|required_with:discount_percentage|date|after:today',
            'is_guaranteed' => 'boolean',
            'notes' => 'nullable|string|max:500',
            'create_multiple' => 'boolean',
            'frequency' => 'required_if:create_multiple,1|in:daily,weekly,monthly',
            'occurrences' => 'required_if:create_multiple,1|integer|min:1|max:52'
        ], $priceRules), [
            'start_date.required' => 'La date de début est requise.',
            'start_date.after' => 'La date de début doit être dans le futur.',
            'end_date.required' => 'La date de fin est requise.',
            'end_date.after_or_equal' => 'La date de fin doit être après ou égale à la date de début.',
            'total_spots.required' => 'Le nombre de places est requis.',
            'min_participants.required' => 'Le nombre minimum de participants est requis.',
            'min_participants.lte' => 'Le minimum ne peut pas dépasser le nombre total de places.',
            'adult_price.required' => 'Le prix adulte est requis.',
            'property_price.required' => 'Le prix par nuit est requis.',
            'discount_ends_at.after' => 'La date de fin de promotion doit être dans le futur.',
        ]);
        
        // Vérifier que les dates correspondent à la durée du voyage
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $duration = $startDate->diffInDays($endDate) + 1;
        
        if ($duration !== $trip->duration) {
            return back()
                ->withInput()
                ->withErrors(['end_date' => "La durée calculée est de {$duration} jour(s), mais doit correspondre à {$trip->duration} jour(s)."]);
        }
        
        try {
            DB::beginTransaction();
            
            // Préparer les données de prix selon le mode
            $priceData = $this->preparePriceData($trip->pricing_mode, $validated);
            
            // Créer une ou plusieurs disponibilités
            if ($request->boolean('create_multiple')) {
                $availabilities = $this->createMultipleAvailabilities($trip, array_merge($validated, $priceData));
                $message = count($availabilities) . ' disponibilités créées avec succès.';
            } else {
                $availability = $trip->availabilities()->create(array_merge([
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                    'total_spots' => $validated['total_spots'],
                    'min_participants' => $validated['min_participants'],
                    'discount_percentage' => $validated['discount_percentage'] ?? 0,
                    'discount_ends_at' => $validated['discount_ends_at'] ?? null,
                    'is_guaranteed' => $validated['is_guaranteed'] ?? false,
                    'status' => 'available',
                    'notes' => $validated['notes'] ?? null,
                    'booked_spots' => 0,
                    'available_spots' => $validated['total_spots']
                ], $priceData));
                
                $message = 'Disponibilité créée avec succès.';
            }
            
            DB::commit();
            
            Log::info('Trip availability created', [
                'trip_id' => $trip->id,
                'vendor_id' => Auth::user()->vendor->id,
                'multiple' => $request->boolean('create_multiple')
            ]);
            
            return redirect()
                ->route('vendor.trips.availabilities.index', $trip)
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to create trip availability', [
                'trip_id' => $trip->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création: ' . $e->getMessage());
        }
    }
    
    /**
     * Afficher le formulaire d'édition d'une disponibilité
     */
    public function edit(Trip $trip, TripAvailability $availability)
    {
        $this->checkTripOwnership($trip);
        $this->checkAvailabilityOwnership($availability);
        
        $vendor = Auth::user()->vendor;
        
        // Vérifier qu'on ne peut pas éditer une disponibilité passée
        if ($availability->start_date < now()) {
            return redirect()
                ->route('vendor.trips.availabilities.index', $trip)
                ->with('error', 'Impossible de modifier une disponibilité passée.');
        }
        
        // Vérifier les réservations confirmées
        $hasConfirmedBookings = $availability->bookings()
            ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_COMPLETED])
            ->exists();
        
        $bookingsCount = $availability->bookings()
            ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_COMPLETED])
            ->count();
        
        $priceSuggestions = [
            'min' => $trip->availabilities()->min('adult_price') ?? $trip->price,
            'max' => $trip->availabilities()->max('adult_price') ?? $trip->price,
            'avg' => $trip->availabilities()->avg('adult_price') ?? $trip->price,
        ];
        
        return view('vendor.trips.availabilities.edit', compact(
            'trip', 
            'availability', 
            'vendor', 
            'priceSuggestions', 
            'hasConfirmedBookings',
            'bookingsCount'
        ));
    }
    
    /**
     * Mettre à jour une disponibilité
     */
    public function update(Request $request, Trip $trip, TripAvailability $availability)
    {
        $this->checkTripOwnership($trip);
        $this->checkAvailabilityOwnership($availability);
        
        // Vérifier les réservations existantes
        $hasBookings = $availability->bookings()
            ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_COMPLETED])
            ->exists();
        
        // Règles de validation selon le mode de tarification
        $priceRules = $this->getPriceValidationRules($trip->pricing_mode);
        
        $rules = array_merge([
            'total_spots' => 'required|integer|min:' . ($hasBookings ? $availability->booked_spots : 1) . '|max:200',
            'min_participants' => 'required|integer|min:1|lte:total_spots',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
            'discount_ends_at' => 'nullable|required_with:discount_percentage|date|after:today',
            'is_guaranteed' => 'boolean',
            'notes' => 'nullable|string|max:500',
            'status' => 'required|in:available,guaranteed,full,cancelled'
        ], $priceRules);
        
        // Si pas de réservations, on peut modifier les dates
        if (!$hasBookings) {
            $rules['start_date'] = 'required|date|after:today';
            $rules['end_date'] = 'required|date|after_or_equal:start_date';
        }
        
        $validated = $request->validate($rules);
        
        try {
            // Préparer les données de prix
            $priceData = $this->preparePriceData($trip->pricing_mode, $validated);
            
            $updateData = array_merge([
                'total_spots' => $validated['total_spots'],
                'min_participants' => $validated['min_participants'],
                'discount_percentage' => $validated['discount_percentage'] ?? 0,
                'discount_ends_at' => $validated['discount_ends_at'] ?? null,
                'is_guaranteed' => $validated['is_guaranteed'] ?? false,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null
            ], $priceData);
            
            // Ajouter les dates si modifiables
            if (!$hasBookings) {
                $updateData['start_date'] = $validated['start_date'];
                $updateData['end_date'] = $validated['end_date'];
            }
            
            // Recalculer les places disponibles
            $updateData['available_spots'] = $validated['total_spots'] - $availability->booked_spots;
            
            $availability->update($updateData);
            
            // Vérifier si le statut doit être mis à jour automatiquement
            if ($availability->available_spots === 0 && $availability->status !== 'full') {
                $availability->update(['status' => 'full']);
            } elseif ($availability->available_spots > 0 && $availability->status === 'full') {
                $availability->update(['status' => 'available']);
            }
            
            Log::info('Trip availability updated', [
                'availability_id' => $availability->id,
                'trip_id' => $trip->id,
                'has_bookings' => $hasBookings
            ]);
            
            return redirect()
                ->route('vendor.trips.availabilities.index', $trip)
                ->with('success', 'Disponibilité mise à jour avec succès.');
                
        } catch (\Exception $e) {
            Log::error('Failed to update trip availability', [
                'availability_id' => $availability->id,
                'error' => $e->getMessage()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour.');
        }
    }
    
    /**
     * Supprimer une disponibilité
     */
    public function destroy(Trip $trip, TripAvailability $availability)
    {
        $this->checkTripOwnership($trip);
        $this->checkAvailabilityOwnership($availability);
        
        // Vérifier qu'il n'y a pas de réservations
        if ($availability->bookings()->exists()) {
            return back()->with('error', 'Impossible de supprimer une disponibilité avec des réservations.');
        }
        
        try {
            $availability->delete();
            
            Log::info('Trip availability deleted', [
                'availability_id' => $availability->id,
                'trip_id' => $trip->id
            ]);
            
            return redirect()
                ->route('vendor.trips.availabilities.index', $trip)
                ->with('success', 'Disponibilité supprimée avec succès.');
                
        } catch (\Exception $e) {
            Log::error('Failed to delete trip availability', [
                'availability_id' => $availability->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Impossible de supprimer cette disponibilité.');
        }
    }
    
    /**
     * Dupliquer une disponibilité
     */
    public function duplicate(Trip $trip, TripAvailability $availability)
    {
        $this->checkTripOwnership($trip);
        
        try {
            $newAvailability = $availability->replicate();
            $newAvailability->booked_spots = 0;
            $newAvailability->available_spots = $newAvailability->total_spots;
            $newAvailability->status = 'available';
            $newAvailability->save();
            
            return redirect()
                ->route('vendor.trips.availabilities.edit', [$trip, $newAvailability])
                ->with('success', 'Disponibilité dupliquée avec succès. Modifiez les dates.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Impossible de dupliquer cette disponibilité.');
        }
    }
    
    /**
     * Créer plusieurs disponibilités à la fois
     */
    private function createMultipleAvailabilities(Trip $trip, array $data)
    {
        $availabilities = [];
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        
        for ($i = 0; $i < $data['occurrences']; $i++) {
            $availability = $trip->availabilities()->create([
                'start_date' => $startDate->copy(),
                'end_date' => $endDate->copy(),
                'total_spots' => $data['total_spots'],
                'min_participants' => $data['min_participants'],
                'adult_price' => $data['adult_price'] ?? null,
                'child_price' => $data['child_price'] ?? null,
                'property_price' => $data['property_price'] ?? null,
                'discount_percentage' => $data['discount_percentage'] ?? 0,
                'discount_ends_at' => $data['discount_ends_at'] ?? null,
                'is_guaranteed' => $data['is_guaranteed'] ?? false,
                'status' => 'available',
                'notes' => $data['notes'] ?? null,
                'booked_spots' => 0,
                'available_spots' => $data['total_spots']
            ]);
            
            $availabilities[] = $availability;
            
            // Incrémenter les dates selon la fréquence
            switch ($data['frequency']) {
                case 'daily':
                    $startDate->addDay();
                    $endDate->addDay();
                    break;
                case 'weekly':
                    $startDate->addWeek();
                    $endDate->addWeek();
                    break;
                case 'monthly':
                    $startDate->addMonth();
                    $endDate->addMonth();
                    break;
            }
        }
        
        return $availabilities;
    }
    
    /**
     * Export des disponibilités en CSV
     */
    public function export(Trip $trip)
    {
        $this->checkTripOwnership($trip);
        
        $availabilities = $trip->availabilities()
            ->withCount(['bookings' => function ($query) {
                $query->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_COMPLETED]);
            }])
            ->orderBy('start_date')
            ->get();
        
        $csvData = [];
        $csvData[] = ['Date début', 'Date fin', 'Places totales', 'Places réservées', 'Places disponibles', 'Prix adulte', 'Prix enfant', 'Réduction %', 'Statut', 'Revenus'];
        
        foreach ($availabilities as $availability) {
            $revenue = $availability->bookings()
                ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_COMPLETED])
                ->sum('total_amount');
                
            $csvData[] = [
                $availability->start_date->format('d/m/Y'),
                $availability->end_date->format('d/m/Y'),
                $availability->total_spots,
                $availability->booked_spots,
                $availability->available_spots,
                $availability->adult_price ?? $availability->property_price ?? '-',
                $availability->child_price ?? '-',
                $availability->discount_percentage > 0 ? $availability->discount_percentage . '%' : '-',
                $this->getStatusText($availability->status),
                number_format($revenue, 2, ',', ' ') . ' €'
            ];
        }
        
        $filename = 'disponibilites-' . $trip->slug . '-' . date('Y-m-d') . '.csv';
        
        return response()->streamDownload(function() use ($csvData) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM pour Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            foreach ($csvData as $row) {
                fputcsv($file, $row, ';');
            }
            
            fclose($file);
        }, $filename);
    }
    
    /**
     * Obtenir les règles de validation pour les prix selon le mode
     */
    private function getPriceValidationRules($pricingMode)
    {
        switch ($pricingMode) {
            case 'per_night_property':
                return [
                    'property_price' => 'required|numeric|min:0',
                ];
            case 'per_person_per_day':
            case 'per_person_activity':
                return [
                    'adult_price' => 'required|numeric|min:0',
                    'child_price' => 'nullable|numeric|min:0',
                ];
            default:
                return [
                    'adult_price' => 'required|numeric|min:0',
                    'child_price' => 'nullable|numeric|min:0',
                ];
        }
    }
    
    /**
     * Préparer les données de prix selon le mode
     */
    private function preparePriceData($pricingMode, $validated)
    {
        switch ($pricingMode) {
            case 'per_night_property':
                return [
                    'property_price' => $validated['property_price'],
                    'adult_price' => null,
                    'child_price' => null,
                ];
            case 'per_person_per_day':
            case 'per_person_activity':
                return [
                    'adult_price' => $validated['adult_price'],
                    'child_price' => $validated['child_price'] ?? null,
                    'property_price' => null,
                ];
            default:
                return [
                    'adult_price' => $validated['adult_price'] ?? null,
                    'child_price' => $validated['child_price'] ?? null,
                    'property_price' => null,
                ];
        }
    }
    
    /**
     * Calculer le taux de remplissage moyen
     */
    private function calculateAverageFillRate($trip)
    {
        $rate = $trip->availabilities()
            ->where('total_spots', '>', 0)
            ->whereNotNull('booked_spots')
            ->selectRaw('AVG(CAST(booked_spots AS DECIMAL(10,2)) * 100.0 / CAST(total_spots AS DECIMAL(10,2))) as rate')
            ->value('rate');
            
        return round($rate ?? 0, 2);
    }
    
    /**
     * Vérifier que le trip appartient au vendor connecté
     */
    protected function checkTripOwnership(Trip $trip)
    {
        if ($trip->vendor_id !== Auth::user()->vendor->id) {
            abort(403, 'Vous n\'êtes pas autorisé à accéder à ce voyage.');
        }
    }
    
    /**
     * Vérifier que la disponibilité appartient au vendor connecté
     */
    protected function checkAvailabilityOwnership(TripAvailability $availability)
    {
        if ($availability->trip->vendor_id !== Auth::user()->vendor->id) {
            abort(403, 'Vous n\'êtes pas autorisé à accéder à cette disponibilité.');
        }
    }
    
    /**
     * Obtenir le texte du statut
     */
    protected function getStatusText($status)
    {
        return match($status) {
            'available' => 'Disponible',
            'guaranteed' => 'Garanti',
            'full' => 'Complet',
            'cancelled' => 'Annulé',
            default => $status
        };
    }
}