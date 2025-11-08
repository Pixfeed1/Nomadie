<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::query()->where('role', 'customer');

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtrage par statut
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('suspended_at');
            } elseif ($request->status === 'suspended') {
                $query->whereNotNull('suspended_at');
            }
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        // Stats
        $stats = [
            'total' => User::where('role', 'customer')->count(),
            'active' => User::where('role', 'customer')->whereNull('suspended_at')->count(),
            'suspended' => User::where('role', 'customer')->whereNotNull('suspended_at')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['bookings.trip', 'articles']);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Suspend a user.
     */
    public function suspend(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $user->update([
            'suspended_at' => now(),
            'suspension_reason' => $request->reason
        ]);

        return back()->with('success', 'Utilisateur suspendu avec succès.');
    }

    /**
     * Activate a user.
     */
    public function activate(User $user)
    {
        $user->update([
            'suspended_at' => null,
            'suspension_reason' => null
        ]);

        return back()->with('success', 'Utilisateur réactivé avec succès.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
}
