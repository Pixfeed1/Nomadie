<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor; // Assurez-vous que cette ligne est ajoutée pour importer le modèle Vendor

class VendorController extends Controller
{
    /**
     * Affiche la liste de tous les vendeurs
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Si vous souhaitez passer des données à votre vue
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
        
        // Si vous avez un système de notification par email, vous pourriez l'ajouter ici
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
        
        // Si vous avez un système de notification par email, vous pourriez l'ajouter ici
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
}