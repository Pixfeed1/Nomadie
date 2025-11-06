<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Affiche la liste des paiements du vendeur
     */
    public function index(Request $request)
    {
        $vendor = Auth::user()->vendor;
        
        // Récupérer tous les paiements liés aux voyages du vendeur
        // Utiliser whereHasMorph car Payment a une relation polymorphique 'payable'
        $payments = Payment::where('payable_type', Booking::class)
            ->whereHasMorph('payable', [Booking::class], function($query) use ($vendor) {
                $query->whereHas('trip', function($q) use ($vendor) {
                    $q->where('vendor_id', $vendor->id);
                });
            })
            ->with(['payable' => function($query) {
                $query->with(['trip', 'user']);
            }])
            ->latest()
            ->paginate(20);
        
        // Calculer les statistiques à partir des paiements paginés
        $totalQuery = Payment::where('payable_type', Booking::class)
            ->whereHasMorph('payable', [Booking::class], function($query) use ($vendor) {
                $query->whereHas('trip', function($q) use ($vendor) {
                    $q->where('vendor_id', $vendor->id);
                });
            });
        
        // Statistiques
        $stats = [
            'total_revenue' => $totalQuery->where('status', 'succeeded')->sum('amount') / 100, // Convertir de centimes en euros
            'total_commission' => 0, // À calculer selon votre logique
            'net_revenue' => 0, // À calculer selon votre logique
            'pending_payments' => (clone $totalQuery)->where('status', 'pending')->count()
        ];
        
        // Calculer la commission selon le plan du vendor
        $commissionRate = $this->getCommissionRate($vendor->subscription_plan);
        $stats['total_commission'] = ($stats['total_revenue'] * $commissionRate) / 100;
        $stats['net_revenue'] = $stats['total_revenue'] - $stats['total_commission'];
        
        return view('vendor.payments.index', compact('payments', 'stats', 'vendor'));
    }

    /**
     * Affiche les détails d'un paiement
     */
    public function show($id)
    {
        $vendor = Auth::user()->vendor;
        
        $payment = Payment::where('payable_type', Booking::class)
            ->whereHasMorph('payable', [Booking::class], function($query) use ($vendor) {
                $query->whereHas('trip', function($q) use ($vendor) {
                    $q->where('vendor_id', $vendor->id);
                });
            })
            ->with(['payable' => function($query) {
                $query->with(['trip', 'user']);
            }])
            ->findOrFail($id);
        
        return view('vendor.payments.show', compact('payment'));
    }

    /**
     * Export des paiements en CSV
     */
    public function exportCsv()
    {
        $vendor = Auth::user()->vendor;
        
        $payments = Payment::where('payable_type', Booking::class)
            ->whereHasMorph('payable', [Booking::class], function($query) use ($vendor) {
                $query->whereHas('trip', function($q) use ($vendor) {
                    $q->where('vendor_id', $vendor->id);
                });
            })
            ->with(['payable' => function($query) {
                $query->with(['trip', 'user']);
            }])
            ->get();
        
        // Headers CSV
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="payments_' . date('Y-m-d') . '.csv"',
        ];
        
        $callback = function() use ($payments, $vendor) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM pour Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // En-têtes
            fputcsv($file, ['Date', 'Voyage', 'Client', 'Montant', 'Commission', 'Net', 'Statut'], ';');
            
            // Données
            $commissionRate = $this->getCommissionRate($vendor->subscription_plan);
            
            foreach ($payments as $payment) {
                $booking = $payment->payable;
                $amount = $payment->amount / 100; // Convertir de centimes en euros
                $commission = ($amount * $commissionRate) / 100;
                $net = $amount - $commission;
                
                fputcsv($file, [
                    $payment->created_at->format('d/m/Y'),
                    $booking->trip->title ?? 'N/A',
                    $booking->user->name ?? 'N/A',
                    number_format($amount, 2, ',', ' ') . ' €',
                    number_format($commission, 2, ',', ' ') . ' €',
                    number_format($net, 2, ',', ' ') . ' €',
                    $this->getStatusText($payment->status)
                ], ';');
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export des paiements en PDF
     */
    public function exportPdf()
    {
        $vendor = Auth::user()->vendor;
        
        $payments = Payment::where('payable_type', Booking::class)
            ->whereHasMorph('payable', [Booking::class], function($query) use ($vendor) {
                $query->whereHas('trip', function($q) use ($vendor) {
                    $q->where('vendor_id', $vendor->id);
                });
            })
            ->with(['payable' => function($query) {
                $query->with(['trip', 'user']);
            }])
            ->get();
        
        // Logique d'export PDF
        // À implémenter avec une librairie PDF comme DomPDF ou TCPDF
        
        return response()->download('payments.pdf');
    }

    /**
     * Filtre les paiements
     */
    public function filter(Request $request)
    {
        $vendor = Auth::user()->vendor;
        
        $query = Payment::where('payable_type', Booking::class)
            ->whereHasMorph('payable', [Booking::class], function($q) use ($vendor) {
                $q->whereHas('trip', function($tripQuery) use ($vendor) {
                    $tripQuery->where('vendor_id', $vendor->id);
                });
            });
        
        // Appliquer les filtres
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->has('amount_min') && $request->amount_min) {
            $query->where('amount', '>=', $request->amount_min * 100); // Convertir en centimes
        }
        
        if ($request->has('amount_max') && $request->amount_max) {
            $query->where('amount', '<=', $request->amount_max * 100); // Convertir en centimes
        }
        
        $payments = $query->with(['payable' => function($query) {
                $query->with(['trip', 'user']);
            }])
            ->latest()
            ->paginate(20);
        
        // Recalculer les statistiques pour les résultats filtrés
        $stats = [
            'total_revenue' => (clone $query)->where('status', 'succeeded')->sum('amount') / 100,
            'pending_payments' => (clone $query)->where('status', 'pending')->count()
        ];
        
        $commissionRate = $this->getCommissionRate($vendor->subscription_plan);
        $stats['total_commission'] = ($stats['total_revenue'] * $commissionRate) / 100;
        $stats['net_revenue'] = $stats['total_revenue'] - $stats['total_commission'];
        
        return view('vendor.payments.index', compact('payments', 'stats', 'vendor'));
    }

    /**
     * Affiche la liste des factures
     */
    public function invoices()
    {
        $vendor = Auth::user()->vendor;
        
        $invoices = Invoice::where('vendor_id', $vendor->id)
            ->latest()
            ->paginate(20);
        
        return view('vendor.payments.invoices', compact('invoices'));
    }

    /**
     * Télécharge une facture
     */
    public function downloadInvoice($id)
    {
        $vendor = Auth::user()->vendor;
        
        $invoice = Invoice::where('vendor_id', $vendor->id)
            ->findOrFail($id);
        
        // Logique de téléchargement de facture
        // À implémenter selon votre système de stockage
        
        return response()->download($invoice->file_path);
    }
    
    /**
     * Obtient le taux de commission selon le plan d'abonnement
     */
    private function getCommissionRate($plan)
    {
        $rates = [
            'free' => 20,
            'essential' => 10,
            'pro' => 5
        ];

        return $rates[$plan] ?? 20;
    }
    
    /**
     * Convertit le statut en texte lisible
     */
    private function getStatusText($status)
    {
        $statuses = [
            'pending' => 'En attente',
            'processing' => 'En cours',
            'succeeded' => 'Réussi',
            'failed' => 'Échoué',
            'canceled' => 'Annulé',
            'refunded' => 'Remboursé',
            'partial_refund' => 'Remb. partiel'
        ];

        return $statuses[$status] ?? ucfirst($status);
    }
}