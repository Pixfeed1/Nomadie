<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminMessageController extends Controller
{
    /**
     * Liste de toutes les conversations
     */
    public function index(Request $request)
    {
        // Grouper les messages par conversation_id
        $query = Message::with(['sender', 'recipient', 'trip', 'booking'])
            ->select('conversation_id',
                DB::raw('MAX(created_at) as last_message_at'),
                DB::raw('COUNT(*) as message_count'),
                DB::raw('SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread_count')
            )
            ->groupBy('conversation_id');

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('sender', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('recipient', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('unread_only') && $request->unread_only) {
            $query->having('unread_count', '>', 0);
        }

        $conversations = $query->orderBy('last_message_at', 'desc')
            ->paginate(20);

        // Pour chaque conversation, récupérer le dernier message complet
        $conversations->getCollection()->transform(function ($conv) {
            $lastMessage = Message::where('conversation_id', $conv->conversation_id)
                ->with(['sender', 'recipient', 'trip', 'booking'])
                ->latest()
                ->first();

            $conv->last_message = $lastMessage;
            return $conv;
        });

        // Stats globales
        $stats = [
            'total_conversations' => Message::distinct('conversation_id')->count('conversation_id'),
            'total_messages' => Message::count(),
            'unread_messages' => Message::where('is_read', false)->count(),
            'messages_today' => Message::whereDate('created_at', today())->count(),
        ];

        return view('admin.messages.index', compact('conversations', 'stats'));
    }

    /**
     * Afficher une conversation complète
     */
    public function show($conversationId)
    {
        $messages = Message::where('conversation_id', $conversationId)
            ->with(['sender', 'recipient', 'trip', 'booking'])
            ->orderBy('created_at', 'asc')
            ->get();

        if ($messages->isEmpty()) {
            abort(404, 'Conversation not found');
        }

        // Informations sur la conversation
        $firstMessage = $messages->first();
        $participants = [
            $firstMessage->sender,
            $firstMessage->recipient
        ];

        // Lié à quelle expérience/réservation?
        $trip = $firstMessage->trip;
        $booking = $firstMessage->booking;

        return view('admin.messages.show', compact('messages', 'participants', 'trip', 'booking', 'conversationId'));
    }

    /**
     * Marquer tous les messages d'une conversation comme lus
     */
    public function markAsRead($conversationId)
    {
        Message::where('conversation_id', $conversationId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return back()->with('success', 'Conversation marquée comme lue');
    }

    /**
     * Archiver une conversation
     */
    public function archive($conversationId)
    {
        Message::where('conversation_id', $conversationId)
            ->update([
                'is_archived' => true,
                'archived_at' => now()
            ]);

        return redirect()->route('admin.messages.index')->with('success', 'Conversation archivée');
    }

    /**
     * Recherche de conversations
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        $messages = Message::with(['sender', 'recipient', 'trip'])
            ->where(function($q) use ($query) {
                $q->where('subject', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%")
                  ->orWhereHas('sender', function($sq) use ($query) {
                      $sq->where('name', 'like', "%{$query}%");
                  })
                  ->orWhereHas('recipient', function($sq) use ($query) {
                      $sq->where('name', 'like', "%{$query}%");
                  });
            })
            ->latest()
            ->limit(50)
            ->get();

        return view('admin.messages.search', compact('messages', 'query'));
    }
}
