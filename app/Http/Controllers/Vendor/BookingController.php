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
            ->paginate(20);
        
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
            ->get();
        
        // Logique d'export CSV
        // À implémenter selon vos besoins
        
        return response()->download('bookings.csv');
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
            ->get();
        
        // Logique d'export PDF
        // À implémenter selon vos besoins
        
        return response()->download('bookings.pdf');
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
        
        $bookings = $query->with(['trip', 'user'])->paginate(20);
        
        return view('vendor.bookings.index', compact('bookings'));
    }
}