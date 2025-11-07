<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;

class OrderController extends Controller
{
    /**
     * Affiche la liste de toutes les commandes/réservations
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Récupérer toutes les réservations (orders) avec relations
        $bookings = Booking::with(['trip', 'user', 'vendor'])
            ->latest()
            ->paginate(20);

        return view('admin.orders.index', compact('bookings'));
    }

    /**
     * Affiche les détails d'une commande/réservation
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $booking = Booking::with(['trip', 'user', 'vendor', 'trip.destination'])
            ->findOrFail($id);

        return view('admin.orders.show', compact('booking'));
    }

    /**
     * Met à jour le statut d'une commande/réservation
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed'
        ]);

        $booking->status = $request->status;

        // Si le statut est "confirmed", mettre à jour confirmed_at
        if ($request->status === 'confirmed' && !$booking->confirmed_at) {
            $booking->confirmed_at = now();
        }

        // Si le statut est "cancelled", mettre à jour cancelled_at
        if ($request->status === 'cancelled' && !$booking->cancelled_at) {
            $booking->cancelled_at = now();
            if ($request->cancelled_reason) {
                $booking->cancelled_reason = $request->cancelled_reason;
            }
        }

        $booking->save();

        return redirect()->route('admin.orders.show', $id)
            ->with('success', 'Le statut de la commande a été mis à jour avec succès.');
    }

    /**
     * Met à jour le statut de paiement d'une commande
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePaymentStatus(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $request->validate([
            'payment_status' => 'required|in:pending,paid,refunded,failed'
        ]);

        $booking->payment_status = $request->payment_status;

        // Si le statut est "paid", mettre à jour paid_at
        if ($request->payment_status === 'paid' && !$booking->paid_at) {
            $booking->paid_at = now();
        }

        $booking->save();

        return redirect()->route('admin.orders.show', $id)
            ->with('success', 'Le statut de paiement a été mis à jour avec succès.');
    }
}