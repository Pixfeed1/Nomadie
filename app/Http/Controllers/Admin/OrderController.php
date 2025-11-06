<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        // Dans une vraie application, nous récupérerions les données depuis la base de données
        // Pour la démo, nous utilisons des données simulées
        
        return view('admin.orders.index');
    }
    
    public function show($id)
    {
        // Pour la démo, nous simulons la récupération des détails d'une commande
        
        return view('admin.orders.show', ['id' => $id]);
    }
    
    public function updateStatus(Request $request, $id)
    {
        // Simuler la mise à jour du statut
        
        return redirect()->route('admin.orders.show', $id)->with('success', 'Le statut de la commande a été mis à jour avec succès.');
    }
}