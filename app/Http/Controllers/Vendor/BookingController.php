<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Trip;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Affiche la liste des réservations du vendeur
     */
    public function index(Request $request)
    {
        $vendor = Auth::user()->vendor;
        
        // Récupérer toutes les réservations pour les voyages du vendeur
        $bookings = Booking::whereHas('trip', function($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
            })
            ->with(['trip', 'user'])
            ->latest()
            ->paginate(20)->withQueryString();
        
        return view('vendor.bookings.index', compact('bookings'));
    }

    /**
     * Affiche les détails d'une réservation
     */
    public function show($id)
    {
        $vendor = Auth::user()->vendor;
        
        $booking = Booking::whereHas('trip', function($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
            })
            ->with(['trip', 'user'])
            ->findOrFail($id);
        
        return view('vendor.bookings.show', compact('booking'));
    }

    /**
     * Met à jour le statut d'une réservation
     */
    public function updateStatus(Request $request, $id)
    {
        $vendor = Auth::user()->vendor;
        
        $booking = Booking::whereHas('trip', function($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
            })
            ->findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled'
        ]);
        
        $booking->status = $request->status;
        $booking->save();
        
        return redirect()->back()->with('success', 'Statut de la réservation mis à jour.');
    }

    /**
     * Export des réservations en CSV
     */
    public function exportCsv()
    {
        $vendor = Auth::user()->vendor;

        $bookings = Booking::whereHas('trip', function($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
            })
            ->with(['trip', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Générer le fichier CSV
        $filename = 'reservations_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'r+');

        // En-têtes CSV
        fputcsv($handle, [
            'ID',
            'Référence',
            'Voyage',
            'Client',
            'Email',
            'Date départ',
            'Date retour',
            'Adultes',
            'Enfants',
            'Montant total',
            'Statut',
            'Statut paiement',
            'Date réservation',
        ]);

        // Données des réservations
        foreach ($bookings as $booking) {
            fputcsv($handle, [
                $booking->id,
                $booking->reference ?? 'N/A',
                $booking->trip->title ?? 'N/A',
                $booking->user->firstname . ' ' . $booking->user->lastname,
                $booking->user->email,
                $booking->start_date ? $booking->start_date->format('d/m/Y') : 'N/A',
                $booking->end_date ? $booking->end_date->format('d/m/Y') : 'N/A',
                $booking->adults ?? 0,
                $booking->children ?? 0,
                $booking->total_price ? number_format($booking->total_price, 2, ',', ' ') . ' €' : 'N/A',
                $booking->status ?? 'N/A',
                $booking->payment_status ?? 'N/A',
                $booking->created_at->format('d/m/Y H:i'),
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export des réservations en PDF
     */
    public function exportPdf()
    {
        $vendor = Auth::user()->vendor;

        $bookings = Booking::whereHas('trip', function($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
            })
            ->with(['trip', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculer les statistiques
        $stats = [
            'total' => $bookings->count(),
            'confirmed' => $bookings->where('status', 'confirmed')->count(),
            'pending' => $bookings->where('status', 'pending')->count(),
            'cancelled' => $bookings->where('status', 'cancelled')->count(),
            'revenue' => $bookings->where('payment_status', 'paid')->sum('total_price'),
        ];

        // Retourner une vue HTML formatée pour l'impression/PDF
        return view('vendor.bookings.export-pdf', compact('bookings', 'vendor', 'stats'));
    }

    /**
     * Filtre les réservations
     */
    public function filter(Request $request)
    {
        $vendor = Auth::user()->vendor;
        
        $query = Booking::whereHas('trip', function($q) use ($vendor) {
            $q->where('vendor_id', $vendor->id);
        });
        
        // Appliquer les filtres
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('trip_id')) {
            $query->where('trip_id', $request->trip_id);
        }
        
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $bookings = $query->with(['trip', 'user'])->paginate(20)->withQueryString();

        return view('vendor.bookings.index', compact('bookings'));
    }
}