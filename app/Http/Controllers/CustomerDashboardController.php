<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Trip;
use App\Models\Booking;
use App\Models\Favorite;
use App\Models\Review;
use App\Models\Message;
use App\Models\Vendor;

class CustomerDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role !== 'customer') {
                return redirect()->route('vendor.dashboard.index');
            }
            return $next($request);
        });
    }

    /**
     * Dashboard principal du client
     */
    public function index()
    {
        $user = Auth::user();
        
        // Statistiques principales
        $stats = $this->getCustomerStats($user);
        
        // Réservations à venir
        $upcomingBookings = $this->getUpcomingBookings($user);
        
        // Favoris récents
        $recentFavorites = $this->getRecentFavorites($user);
        
        // Messages non lus
        $unreadMessages = $this->getUnreadMessages($user);
        
        // Avis à donner
        $pendingReviews = $this->getPendingReviews($user);
        
        // Recommandations personnalisées
        $recommendations = $this->getRecommendations($user);
        
        // Activité récente
        $recentActivity = $this->getRecentActivity($user);
        
        // Complétude du profil
        $profileCompletion = $this->getProfileCompletion($user);

        return view('customer.index', compact(
            'user',
            'stats',
            'upcomingBookings',
            'recentFavorites',
            'unreadMessages',
            'pendingReviews',
            'recommendations',
            'recentActivity',
            'profileCompletion'
        ));
    }

    /**
     * Liste des réservations
     */
    public function bookings()
    {
        $user = Auth::user();
        
        // Récupérer toutes les données nécessaires pour le dashboard
        $stats = $this->getCustomerStats($user);
        $upcomingBookings = $this->getUpcomingBookings($user);
        $recentFavorites = $this->getRecentFavorites($user);
        $unreadMessages = $this->getUnreadMessages($user);
        $pendingReviews = $this->getPendingReviews($user);
        $recommendations = $this->getRecommendations($user);
        $recentActivity = $this->getRecentActivity($user);
        $profileCompletion = $this->getProfileCompletion($user);
        
        // Récupérer les réservations paginées
        $bookings = Booking::where('user_id', $user->id)
            ->with(['trip', 'availability', 'vendor'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('customer.bookings', compact(
            'bookings', 
            'user', 
            'stats',
            'upcomingBookings',
            'recentFavorites',
            'unreadMessages',
            'pendingReviews',
            'recommendations',
            'recentActivity',
            'profileCompletion'
        ));
    }

    /**
     * Détail d'une réservation
     */
    public function bookingDetail($id)
    {
        $booking = Booking::where('user_id', Auth::id())
            ->with(['trip', 'availability', 'vendor'])
            ->findOrFail($id);
        
        return view('customer.booking-detail', compact('booking'));
    }

    /**
     * Annuler une réservation
     */
    public function cancelBooking(Request $request, $id)
    {
        $booking = Booking::where('user_id', Auth::id())
            ->findOrFail($id);
        
        if ($booking->canBeCancelled()) {
            $booking->cancel($request->input('reason', 'Annulé par le client'));
            return back()->with('success', 'Réservation annulée avec succès');
        }
        
        return back()->with('error', 'Cette réservation ne peut pas être annulée');
    }

    /**
     * Liste des favoris
     */
    public function favorites()
    {
        $user = Auth::user();
        $favorites = Favorite::where('user_id', $user->id)
            ->with(['trip', 'trip.vendor', 'trip.destination'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        return view('customer.favorites', compact('favorites'));
    }

    /**
     * Basculer un favori
     */
    public function toggleFavorite($tripId)
    {
        $user = Auth::user();
        $favorite = Favorite::where('user_id', $user->id)
            ->where('trip_id', $tripId)
            ->first();
        
        if ($favorite) {
            $favorite->delete();
            return response()->json([
                'status' => 'removed',
                'message' => 'Retiré des favoris'
            ]);
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'trip_id' => $tripId
            ]);
            return response()->json([
                'status' => 'added',
                'message' => 'Ajouté aux favoris'
            ]);
        }
    }

    /**
     * Afficher le formulaire pour un nouveau message
     */
    public function newMessage(Request $request)
    {
        $vendorId = $request->query('vendor_id');
        $tripId = $request->query('trip_id');
        
        if (!$vendorId || !$tripId) {
            return redirect()->route('home')->with('error', 'Paramètres manquants');
        }
        
        $trip = Trip::findOrFail($tripId);
        $vendor = Vendor::where('user_id', $vendorId)->firstOrFail();
        
        // Vérifier si une conversation existe déjà
        $existingConversation = Message::where(function($query) use ($vendorId) {
                $query->where('sender_id', Auth::id())
                      ->where('recipient_id', $vendorId);
            })
            ->orWhere(function($query) use ($vendorId) {
                $query->where('sender_id', $vendorId)
                      ->where('recipient_id', Auth::id());
            })
            ->where('trip_id', $tripId)
            ->first();
        
        if ($existingConversation) {
            // Si conversation existe, rediriger vers celle-ci avec le slug du trip
            return redirect()->route('customer.messages.show', $trip->slug);
        }
        
        return view('customer.messages.new', compact('vendor', 'trip'));
    }

    /**
     * Envoyer un nouveau message
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'trip_id' => 'required|exists:trips,id',
            'content' => 'required|string|max:5000'
        ]);
        
        $trip = Trip::findOrFail($request->trip_id);
        
        // Vérifier que le destinataire est bien le vendor du trip
        if ($trip->vendor->user_id != $request->recipient_id) {
            return back()->with('error', 'Destinataire invalide');
        }
        
        $conversationId = Message::generateConversationId(
            Auth::id(),
            $request->recipient_id,
            $request->trip_id
        );
        
        Message::create([
            'sender_id' => Auth::id(),
            'sender_type' => 'customer',
            'recipient_id' => $request->recipient_id,
            'recipient_type' => 'vendor',
            'conversation_id' => $conversationId,
            'trip_id' => $request->trip_id,
            'subject' => 'Question sur : ' . $trip->title,
            'content' => $request->content,
            'is_read' => false
        ]);
        
        // Notification email au vendor (optionnel)
        // Mail::to($trip->vendor->user->email)->send(new NewMessageNotification($message));
        
        return redirect()->route('customer.messages.show', $trip->slug)
            ->with('success', 'Message envoyé avec succès');
    }

    /**
     * Liste des conversations (messages)
     */
    public function messages()
    {
        $conversations = Message::select('conversation_id', 'trip_id', 'sender_id', 'recipient_id', 'content', 'is_read', 'created_at')
            ->where(function($query) {
                $query->where('sender_id', Auth::id())
                      ->orWhere('recipient_id', Auth::id());
            })
            ->whereIn('id', function($query) {
                $query->selectRaw('MAX(id)')
                      ->from('messages')
                      ->where(function($q) {
                          $q->where('sender_id', Auth::id())
                            ->orWhere('recipient_id', Auth::id());
                      })
                      ->groupBy('conversation_id');
            })
            ->orderBy('created_at', 'desc')
            ->get();
        
        $unreadCount = Message::where('recipient_id', Auth::id())
            ->where('is_read', false)
            ->count();
        
        return view('customer.messages.index', compact('conversations', 'unreadCount'));
    }

    /**
     * Afficher une conversation (mise à jour avec slug)
     */
    public function showConversation($tripSlug)
    {
        // Récupérer le trip par son slug
        $trip = Trip::where('slug', $tripSlug)->firstOrFail();
        
        // Récupérer les messages liés à ce trip où l'utilisateur est participant
        $messages = Message::where('trip_id', $trip->id)
            ->where(function($query) use ($trip) {
                $query->where(function($q) use ($trip) {
                    $q->where('sender_id', Auth::id())
                      ->where('recipient_id', $trip->vendor->user_id);
                })->orWhere(function($q) use ($trip) {
                    $q->where('sender_id', $trip->vendor->user_id)
                      ->where('recipient_id', Auth::id());
                });
            })
            ->orderBy('created_at', 'asc')
            ->get();
        
        if ($messages->isEmpty()) {
            abort(404);
        }
        
        // Marquer comme lus
        Message::where('trip_id', $trip->id)
            ->where('recipient_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
        
        // Identifier l'autre participant (le vendor)
        $otherParticipant = $trip->vendor->user ?? null;
        
        return view('customer.messages.show', compact('messages', 'trip', 'otherParticipant', 'tripSlug'));
    }

    /**
     * Détail d'un message (ancienne méthode conservée pour compatibilité)
     */
    public function messageDetail($id)
    {
        $message = Message::where(function($query) {
                $query->where('recipient_id', Auth::id())
                      ->orWhere('sender_id', Auth::id());
            })
            ->findOrFail($id);
        
        // Marquer comme lu si c'est le destinataire
        if ($message->recipient_id == Auth::id()) {
            $message->markAsRead();
        }
        
        // Récupérer la conversation complète
        $conversation = Message::where(function($query) use ($message) {
                $query->where('sender_id', $message->sender_id)
                      ->where('recipient_id', $message->recipient_id);
            })
            ->orWhere(function($query) use ($message) {
                $query->where('sender_id', $message->recipient_id)
                      ->where('recipient_id', $message->sender_id);
            })
            ->orderBy('created_at', 'asc')
            ->get();
        
        return view('customer.message-detail', compact('message', 'conversation'));
    }

    /**
     * Répondre dans une conversation (mise à jour avec slug)
     */
    public function replyMessage(Request $request, $tripSlug)
    {
        $request->validate([
            'content' => 'required|string|max:5000'
        ]);
        
        // Récupérer le trip par son slug
        $trip = Trip::where('slug', $tripSlug)->firstOrFail();
        
        // Déterminer le destinataire (le vendor du trip)
        $recipientId = $trip->vendor->user_id;
        
        // Générer le conversation_id
        $conversationId = Message::generateConversationId(
            Auth::id(),
            $recipientId,
            $trip->id
        );
        
        Message::create([
            'sender_id' => Auth::id(),
            'sender_type' => 'customer',
            'recipient_id' => $recipientId,
            'recipient_type' => 'vendor',
            'conversation_id' => $conversationId,
            'trip_id' => $trip->id,
            'subject' => 'Re: ' . $trip->title,
            'content' => $request->content,
            'is_read' => false
        ]);
        
        return redirect()->route('customer.messages.show', $tripSlug)
            ->with('success', 'Message envoyé');
    }

    /**
     * Avis
     */
    public function reviews()
    {
        $reviews = Review::where('user_id', Auth::id())
            ->with(['trip', 'trip.vendor'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('customer.reviews', compact('reviews'));
    }

    /**
     * Créer un avis
     */
    public function createReview($bookingId)
    {
        $booking = Booking::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->with(['trip', 'trip.vendor'])
            ->findOrFail($bookingId);
        
        // Vérifier qu'il n'y a pas déjà un avis
        $existingReview = Review::where('user_id', Auth::id())
            ->where('trip_id', $booking->trip_id)
            ->first();
        
        if ($existingReview) {
            return redirect()->route('customer.reviews')
                ->with('error', 'Vous avez déjà laissé un avis pour ce voyage');
        }
        
        return view('customer.create-review', compact('booking'));
    }

    /**
     * Enregistrer un avis
     */
    public function storeReview(Request $request, $bookingId)
    {
        $booking = Booking::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->findOrFail($bookingId);
        
        $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
            'content' => 'required|string|min:10|max:1000'
        ]);
        
        Review::create([
            'user_id' => Auth::id(),
            'trip_id' => $booking->trip_id,
            'booking_id' => $booking->id,
            'rating' => $request->rating,
            'content' => $request->content,
            'travel_date' => $booking->availability->start_date,
            'user_name' => Auth::user()->firstname . ' ' . Auth::user()->lastname
        ]);
        
        return redirect()->route('customer.reviews')
            ->with('success', 'Merci pour votre avis !');
    }

    /**
     * Profil
     */
    public function profile()
    {
        $user = Auth::user();
        $profileCompletion = $this->getProfileCompletion($user);
        
        return view('customer.profile', compact('user', 'profileCompletion'));
    }

    /**
     * Mettre à jour le profil
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'pseudo' => 'nullable|string|max:255|unique:users,pseudo,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100'
        ]);
        
        $user->update($request->only([
            'firstname', 'lastname', 'pseudo', 'email',
            'phone', 'bio', 'address', 'city', 'country'
        ]));
        
        // Mettre à jour le champ name
        $user->update([
            'name' => $request->firstname . ' ' . $request->lastname
        ]);
        
        return back()->with('success', 'Profil mis à jour avec succès');
    }

    /**
     * Mettre à jour l'avatar
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        $user = Auth::user();
        
        // Supprimer l'ancien avatar s'il existe
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        // Enregistrer le nouveau
        $filename = 'avatar_' . $user->id . '_' . time() . '.' . $request->file('avatar')->getClientOriginalExtension();
        $path = $request->file('avatar')->storeAs('avatars', $filename, 'public');
        
        $user->update(['avatar' => $path]);
        
        return back()->with('success', 'Avatar mis à jour avec succès');
    }

    /**
     * Paramètres
     */
    public function settings()
    {
        $user = Auth::user();
        return view('customer.settings', compact('user'));
    }

    /**
     * Mettre à jour les paramètres
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'newsletter' => 'boolean',
            'notifications_email' => 'boolean',
            'notifications_sms' => 'boolean'
        ]);
        
        $user->update([
            'newsletter' => $request->boolean('newsletter'),
            'notifications_email' => $request->boolean('notifications_email', true),
            'notifications_sms' => $request->boolean('notifications_sms', false)
        ]);
        
        return back()->with('success', 'Paramètres mis à jour avec succès');
    }

    /**
     * Mettre à jour le mot de passe
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed|different:current_password'
        ]);
        
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect']);
        }
        
        Auth::user()->update([
            'password' => Hash::make($request->password)
        ]);
        
        return back()->with('success', 'Mot de passe mis à jour avec succès');
    }

    /**
     * Supprimer le compte
     */
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required',
            'confirmation' => 'required|in:DELETE'
        ]);
        
        if (!Hash::check($request->password, Auth::user()->password)) {
            return back()->withErrors(['password' => 'Mot de passe incorrect']);
        }
        
        $user = Auth::user();
        
        // Supprimer les données associées
        Booking::where('user_id', $user->id)->delete();
        Favorite::where('user_id', $user->id)->delete();
        Review::where('user_id', $user->id)->delete();
        Message::where('sender_id', $user->id)->orWhere('recipient_id', $user->id)->delete();
        
        // Supprimer l'avatar
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        // Déconnexion et suppression
        Auth::logout();
        $user->delete();
        
        return redirect()->route('home')
            ->with('success', 'Votre compte a été supprimé définitivement');
    }

    /**
     * Statistiques du client (méthode privée)
     */
    private function getCustomerStats($user)
    {
        return [
            'total_bookings' => Booking::where('user_id', $user->id)->count(),
            'upcoming_bookings' => Booking::where('user_id', $user->id)
                ->where('status', 'confirmed')
                ->whereHas('availability', function($q) {
                    $q->where('start_date', '>=', now());
                })
                ->count(),
            'past_bookings' => Booking::where('user_id', $user->id)
                ->where('status', 'completed')
                ->count(),
            'total_spent' => Booking::where('user_id', $user->id)
                ->where('status', '!=', 'cancelled')
                ->sum('total_price'),
            'favorites_count' => Favorite::where('user_id', $user->id)->count(),
            'reviews_given' => Review::where('user_id', $user->id)->count(),
            'unread_messages' => Message::where('recipient_id', $user->id)
                ->where('is_read', false)
                ->count(),
            'member_since_days' => $user->created_at->diffInDays(now())
        ];
    }

    /**
     * Réservations à venir (méthode privée)
     */
    private function getUpcomingBookings($user)
    {
        return Booking::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->whereHas('availability', function($q) {
                $q->where('start_date', '>=', now());
            })
            ->with(['trip', 'trip.vendor', 'availability'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function($booking) {
                return [
                    'id' => $booking->id,
                    'trip_title' => $booking->trip->title ?? 'N/A',
                    'vendor_name' => $booking->trip->vendor->company_name ?? 'N/A',
                    'start_date' => $booking->availability->start_date ?? null,
                    'end_date' => $booking->availability->end_date ?? null,
                    'total_price' => $booking->total_price,
                    'participants' => $booking->number_of_travelers ?? 1,
                    'status' => $booking->status,
                    'trip_image' => $booking->trip->main_image ?? null
                ];
            });
    }

    /**
     * Favoris récents (méthode privée)
     */
    private function getRecentFavorites($user)
    {
        return Favorite::where('user_id', $user->id)
            ->with(['trip', 'trip.vendor', 'trip.destination'])
            ->latest()
            ->take(4)
            ->get()
            ->map(function($favorite) {
                return [
                    'id' => $favorite->id,
                    'trip_id' => $favorite->trip->id,
                    'trip_title' => $favorite->trip->title,
                    'trip_slug' => $favorite->trip->slug,
                    'vendor_name' => $favorite->trip->vendor->company_name ?? 'N/A',
                    'destination' => $favorite->trip->destination->name ?? 'N/A',
                    'price' => $favorite->trip->price,
                    'pricing_mode' => $favorite->trip->pricing_mode,
                    'trip_image' => $favorite->trip->main_image ?? null,
                    'added_at' => $favorite->created_at
                ];
            });
    }

    /**
     * Messages non lus (méthode privée)
     */
    private function getUnreadMessages($user)
    {
        return Message::where('recipient_id', $user->id)
            ->where('is_read', false)
            ->with(['sender', 'trip'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function($message) {
                return [
                    'id' => $message->id,
                    'sender_name' => $message->sender->name ?? 'N/A',
                    'subject' => $message->subject ?? 'Sans objet',
                    'preview' => \Str::limit($message->content, 100),
                    'sent_at' => $message->created_at,
                    'conversation_id' => $message->conversation_id ?? $message->id,
                    'trip_slug' => $message->trip->slug ?? null
                ];
            });
    }

    /**
     * Avis en attente (méthode privée)
     */
    private function getPendingReviews($user)
    {
        return Booking::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereDoesntHave('review')
            ->whereHas('availability', function($q) {
                $q->where('end_date', '<', now())
                    ->where('end_date', '>', now()->subDays(30));
            })
            ->with(['trip', 'trip.vendor'])
            ->take(3)
            ->get()
            ->map(function($booking) {
                return [
                    'booking_id' => $booking->id,
                    'trip_id' => $booking->trip->id,
                    'trip_title' => $booking->trip->title,
                    'vendor_name' => $booking->trip->vendor->company_name ?? 'N/A',
                    'end_date' => $booking->availability->end_date,
                    'days_ago' => $booking->availability->end_date->diffInDays(now())
                ];
            });
    }

    /**
     * Recommandations personnalisées (méthode privée)
     */
    private function getRecommendations($user)
    {
        // Logique simplifiée : récupérer des voyages similaires aux favoris
        $favoriteDestinations = Favorite::where('user_id', $user->id)
            ->with('trip.destination')
            ->get()
            ->pluck('trip.destination_id')
            ->unique()
            ->take(3);

        $recommendations = Trip::where('status', 'active')
            ->whereIn('destination_id', $favoriteDestinations)
            ->whereNotIn('id', Favorite::where('user_id', $user->id)->pluck('trip_id'))
            ->with(['vendor', 'destination'])
            ->inRandomOrder()
            ->take(4)
            ->get();

        // Si pas assez de recommandations, compléter avec des voyages populaires
        if ($recommendations->count() < 4) {
            $additionalTrips = Trip::where('status', 'active')
                ->whereNotIn('id', $recommendations->pluck('id'))
                ->whereNotIn('id', Favorite::where('user_id', $user->id)->pluck('trip_id'))
                ->orderBy('views_count', 'desc')
                ->take(4 - $recommendations->count())
                ->get();
            
            $recommendations = $recommendations->concat($additionalTrips);
        }

        return $recommendations->map(function($trip) {
            return [
                'id' => $trip->id,
                'title' => $trip->title,
                'slug' => $trip->slug,
                'vendor_name' => $trip->vendor->company_name ?? 'N/A',
                'destination' => $trip->destination->name ?? 'N/A',
                'type' => $trip->trip_type ?? 'N/A',
                'price' => $trip->price,
                'pricing_mode' => $trip->pricing_mode,
                'image' => $trip->main_image ?? null,
                'rating' => $trip->average_rating ?? 0,
                'reviews_count' => $trip->reviews()->count()
            ];
        });
    }

    /**
     * Activité récente (méthode privée)
     */
    private function getRecentActivity($user)
    {
        $activities = collect();

        // Dernières réservations
        $recentBookings = Booking::where('user_id', $user->id)
            ->with('trip')
            ->latest()
            ->take(3)
            ->get();

        foreach ($recentBookings as $booking) {
            $activities->push([
                'type' => 'booking',
                'title' => 'Réservation effectuée',
                'description' => $booking->trip->title ?? 'N/A',
                'date' => $booking->created_at,
                'icon' => 'fa-shopping-cart',
                'color' => 'primary'
            ]);
        }

        // Derniers favoris
        $recentFavorites = Favorite::where('user_id', $user->id)
            ->with('trip')
            ->latest()
            ->take(2)
            ->get();

        foreach ($recentFavorites as $favorite) {
            $activities->push([
                'type' => 'favorite',
                'title' => 'Ajouté aux favoris',
                'description' => $favorite->trip->title ?? 'N/A',
                'date' => $favorite->created_at,
                'icon' => 'fa-heart',
                'color' => 'danger'
            ]);
        }

        // Derniers avis
        $recentReviews = Review::where('user_id', $user->id)
            ->with('trip')
            ->latest()
            ->take(2)
            ->get();

        foreach ($recentReviews as $review) {
            $activities->push([
                'type' => 'review',
                'title' => 'Avis déposé',
                'description' => $review->trip->title ?? 'N/A',
                'date' => $review->created_at,
                'icon' => 'fa-star',
                'color' => 'warning'
            ]);
        }

        return $activities->sortByDesc('date')->take(5);
    }

    /**
     * Complétude du profil (méthode privée)
     */
    private function getProfileCompletion($user)
    {
        $fields = [
            'firstname' => !empty($user->firstname),
            'lastname' => !empty($user->lastname),
            'avatar' => !empty($user->avatar),
            'phone' => !empty($user->phone),
            'bio' => !empty($user->bio),
            'address' => !empty($user->address),
            'city' => !empty($user->city),
            'country' => !empty($user->country)
        ];

        $completed = collect($fields)->filter()->count();
        $total = count($fields);
        $percentage = round(($completed / $total) * 100);

        $fieldLabels = [
            'firstname' => 'Prénom',
            'lastname' => 'Nom',
            'avatar' => 'Photo de profil',
            'phone' => 'Téléphone',
            'bio' => 'Biographie',
            'address' => 'Adresse',
            'city' => 'Ville',
            'country' => 'Pays'
        ];

        $missingFields = collect($fields)
            ->filter(function($value) {
                return !$value;
            })
            ->keys()
            ->map(function($field) use ($fieldLabels) {
                return $fieldLabels[$field] ?? $field;
            })
            ->toArray();

        return [
            'percentage' => $percentage,
            'completed' => $completed,
            'total' => $total,
            'missing_fields' => $missingFields
        ];
    }
}