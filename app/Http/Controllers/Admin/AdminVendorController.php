<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;

class AdminVendorController extends Controller
{
    /**
     * Affiche la liste de tous les vendeurs
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $vendors = Vendor::orderBy('created_at', 'desc')->get();
        return view('admin.vendors.index', compact('vendors'));
    }

    /**
     * Affiche les détails d'un vendeur spécifique
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $vendor = Vendor::findOrFail($id);
        return view('admin.vendors.show', compact('vendor'));
    }

    /**
     * Affiche la liste des vendeurs en attente d'approbation
     *
     * @return \Illuminate\View\View
     */
    public function pending()
    {
        $pendingVendors = Vendor::where('status', 'pending')->orderBy('created_at', 'desc')->get();
        return view('admin.vendors.pending', compact('pendingVendors'));
    }

    /**
     * Affiche la liste des vendeurs approuvés/actifs
     *
     * @return \Illuminate\View\View
     */
    public function approved()
    {
        $approvedVendors = Vendor::where('status', 'active')->orderBy('created_at', 'desc')->get();
        return view('admin.vendors.approved', compact('approvedVendors'));
    }

    /**
     * Affiche la liste des vendeurs rejetés
     *
     * @return \Illuminate\View\View
     */
    public function rejected()
    {
        $rejectedVendors = Vendor::where('status', 'rejected')->orderBy('created_at', 'desc')->get();
        return view('admin.vendors.rejected', compact('rejectedVendors'));
    }

    /**
     * Affiche la liste des vendeurs suspendus
     *
     * @return \Illuminate\View\View
     */
    public function suspended()
    {
        $suspendedVendors = Vendor::where('status', 'suspended')->orderBy('created_at', 'desc')->get();
        return view('admin.vendors.suspended', compact('suspendedVendors'));
    }

    /**
     * Approuve un vendeur en attente
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->status = 'active';
        $vendor->save();

        // TODO: Send email notification
        // Mail::to($vendor->email)->send(new VendorApproved($vendor));

        return redirect()->route('admin.vendors.pending')
            ->with('success', 'Le vendeur a été approuvé avec succès.');
    }

    /**
     * Rejette un vendeur en attente
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->status = 'rejected';
        $vendor->save();

        // TODO: Send email notification
        // Mail::to($vendor->email)->send(new VendorRejected($vendor));

        return redirect()->route('admin.vendors.pending')
            ->with('success', 'Le vendeur a été rejeté.');
    }

    /**
     * Suspend un vendeur actif
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function suspend($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->status = 'suspended';
        $vendor->save();

        return redirect()->route('admin.vendors.show', $id)
            ->with('success', 'Le vendeur a été suspendu.');
    }

    /**
     * Réactive un vendeur suspendu
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activate($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->status = 'active';
        $vendor->save();

        return redirect()->route('admin.vendors.show', $id)
            ->with('success', 'Le vendeur a été réactivé.');
    }

    /**
     * Supprime un vendeur
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $vendor = Vendor::findOrFail($id);

        // Soft delete si le model utilise SoftDeletes, sinon delete permanent
        $vendor->delete();

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Le vendeur a été supprimé avec succès.');
    }
}
