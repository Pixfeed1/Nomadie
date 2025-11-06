<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VendorMessagesController extends Controller
{
    /**
     * Liste de toutes les conversations non archivées
     */
    public function index()
    {
        $conversations = Message::select(
                'messages.*',
                DB::raw('(SELECT COUNT(*) FROM messages m2 WHERE m2.conversation_id = messages.conversation_id 
                         AND m2.recipient_id = ' . Auth::id() . ' AND m2.is_read = 0) as unread_count')
            )
            ->whereIn('messages.id', function($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('messages')
                    ->where(function($q) {
                        $q->where('sender_id', Auth::id())
                          ->orWhere('recipient_id', Auth::id());
                    })
                    ->where('is_archived', false)
                    ->groupBy('conversation_id');
            })
            ->with(['sender', 'recipient', 'trip'])
            ->orderBy('created_at', 'desc')
            ->get();

        $unreadCount = Message::where('recipient_id', Auth::id())
            ->where('is_read', false)
            ->where('is_archived', false)
            ->count();

        $archivedCount = Message::select('conversation_id')
            ->where(function($q) {
                $q->where('sender_id', Auth::id())
                  ->orWhere('recipient_id', Auth::id());
            })
            ->where('is_archived', true)
            ->distinct()
            ->count('conversation_id');

        return view('vendor.messages.index', compact('conversations', 'unreadCount', 'archivedCount'));
    }

    /**
     * Liste des messages non lus uniquement
     */
    public function unread()
    {
        $conversations = Message::select(
                'messages.*',
                DB::raw('(SELECT COUNT(*) FROM messages m2 WHERE m2.conversation_id = messages.conversation_id
                         AND m2.recipient_id = ' . Auth::id() . ' AND m2.is_read = 0) as unread_count')
            )
            ->whereIn('messages.id', function($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('messages')
                    ->where('recipient_id', Auth::id())
                    ->where('is_read', false)
                    ->where('is_archived', false)
                    ->groupBy('conversation_id');
            })
            ->with(['sender', 'recipient', 'trip'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $unreadCount = Message::where('recipient_id', Auth::id())
            ->where('is_read', false)
            ->count();
        
        return view('vendor.messages.index', compact('conversations', 'unreadCount'))
            ->with('filter', 'unread');
    }

    /**
     * Liste des conversations archivées
     */
    public function archived()
    {
        $conversations = Message::select(
                'messages.*',
                DB::raw('(SELECT COUNT(*) FROM messages m2 WHERE m2.conversation_id = messages.conversation_id
                         AND m2.recipient_id = ' . Auth::id() . ' AND m2.is_read = 0) as unread_count')
            )
            ->whereIn('messages.id', function($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('messages')
                    ->where(function($q) {
                        $q->where('sender_id', Auth::id())
                          ->orWhere('recipient_id', Auth::id());
                    })
                    ->where('is_archived', true)
                    ->groupBy('conversation_id');
            })
            ->with(['sender', 'recipient', 'trip'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $unreadCount = Message::where('recipient_id', Auth::id())
            ->where('is_read', false)
            ->count();
        
        return view('vendor.messages.archived', compact('conversations', 'unreadCount'));
    }

    /**
     * Afficher une conversation spécifique
     */
    public function show($tripSlug)
    {
        // Récupérer le trip par son slug
        $trip = Trip::where('slug', $tripSlug)->firstOrFail();
        
        // Récupérer les messages pour ce trip où le vendor est concerné
        $messages = Message::where('trip_id', $trip->id)
            ->where(function($query) {
                $query->where('sender_id', Auth::id())
                      ->orWhere('recipient_id', Auth::id());
            })
            ->with(['sender', 'recipient', 'trip'])
            ->orderBy('created_at', 'asc')
            ->get();

        if ($messages->isEmpty()) {
            abort(404, 'Conversation non trouvée');
        }

        // Récupérer le conversationId depuis le premier message
        $conversationId = $messages->first()->conversation_id;

        // Marquer comme lus
        Message::where('conversation_id', $conversationId)
            ->where('recipient_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        $otherParticipant = $messages->first()->sender_id == Auth::id() 
            ? $messages->first()->recipient 
            : $messages->first()->sender;

        // Vérifier si archivée
        $isArchived = Message::where('conversation_id', $conversationId)
            ->where(function($q) {
                $q->where('sender_id', Auth::id())
                  ->orWhere('recipient_id', Auth::id());
            })
            ->where('is_archived', true)
            ->exists();

        return view('vendor.messages.show', compact(
            'messages', 
            'conversationId',
            'trip',
            'tripSlug',
            'otherParticipant',
            'isArchived'
        ));
    }

    /**
     * Répondre à un message
     */
    public function reply(Request $request, $tripSlug)
    {
        $request->validate([
            'content' => 'required|string|min:2|max:5000',
            'attachment' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png,doc,docx'
        ]);

        // Récupérer le trip par son slug
        $trip = Trip::where('slug', $tripSlug)->firstOrFail();

        // Récupérer le dernier message de cette conversation
        $lastMessage = Message::where('trip_id', $trip->id)
            ->where(function($query) {
                $query->where('sender_id', Auth::id())
                      ->orWhere('recipient_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$lastMessage) {
            abort(403, 'Conversation non trouvée');
        }

        $conversationId = $lastMessage->conversation_id;
        $recipientId = $lastMessage->sender_id == Auth::id() 
            ? $lastMessage->recipient_id 
            : $lastMessage->sender_id;

        $attachmentPath = null;
        $attachmentName = null;

        // Gestion sécurisée des pièces jointes
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            
            $mimeType = $file->getMimeType();
            $allowedMimes = [
                'application/pdf',
                'image/jpeg',
                'image/png',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];
            
            if (!in_array($mimeType, $allowedMimes)) {
                return back()->withErrors(['attachment' => 'Type de fichier non autorisé']);
            }
            
            $fileName = Str::random(32) . '.' . $file->getClientOriginalExtension();
            $attachmentPath = $file->storeAs('messages/attachments', $fileName, 'private');
            $attachmentName = $file->getClientOriginalName();
        }

        Message::create([
            'sender_id' => Auth::id(),
            'sender_type' => 'vendor',
            'recipient_id' => $recipientId,
            'recipient_type' => 'customer',
            'conversation_id' => $conversationId,
            'trip_id' => $trip->id,
            'subject' => $lastMessage->subject,
            'content' => $request->content,
            'attachment' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'is_read' => false,
            'is_archived' => false  // Désarchiver automatiquement
        ]);

        // Désarchiver toute la conversation si elle était archivée
        Message::where('conversation_id', $conversationId)
            ->update(['is_archived' => false]);

        return redirect()->route('vendor.messages.show', $tripSlug)
            ->with('success', 'Message envoyé');
    }

    /**
     * Télécharger une pièce jointe
     */
    public function download($messageId)
    {
        $message = Message::findOrFail($messageId);
        
        if ($message->sender_id !== Auth::id() && $message->recipient_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }
        
        if (!$message->attachment || !Storage::disk('private')->exists($message->attachment)) {
            abort(404, 'Fichier non trouvé');
        }
        
        return Storage::disk('private')->download(
            $message->attachment,
            $message->attachment_name
        );
    }

    /**
     * Archiver une conversation
     */
    public function archive($tripSlug)
    {
        // Récupérer le trip par son slug
        $trip = Trip::where('slug', $tripSlug)->firstOrFail();

        // Récupérer la conversation_id pour ce trip
        $conversationId = Message::where('trip_id', $trip->id)
            ->where(function($query) {
                $query->where('sender_id', Auth::id())
                      ->orWhere('recipient_id', Auth::id());
            })
            ->value('conversation_id');

        if ($conversationId) {
            Message::where('conversation_id', $conversationId)
                ->where(function($query) {
                    $query->where('sender_id', Auth::id())
                          ->orWhere('recipient_id', Auth::id());
                })
                ->update(['is_archived' => true]);
        }

        return redirect()->route('vendor.messages.index')
            ->with('success', 'Conversation archivée');
    }

    /**
     * Désarchiver une conversation
     */
    public function unarchive($tripSlug)
    {
        // Récupérer le trip par son slug
        $trip = Trip::where('slug', $tripSlug)->firstOrFail();

        // Récupérer la conversation_id pour ce trip
        $conversationId = Message::where('trip_id', $trip->id)
            ->where(function($query) {
                $query->where('sender_id', Auth::id())
                      ->orWhere('recipient_id', Auth::id());
            })
            ->value('conversation_id');

        if ($conversationId) {
            Message::where('conversation_id', $conversationId)
                ->where(function($query) {
                    $query->where('sender_id', Auth::id())
                          ->orWhere('recipient_id', Auth::id());
                })
                ->update(['is_archived' => false]);
        }

        return redirect()->route('vendor.messages.show', $tripSlug)
            ->with('success', 'Conversation restaurée');
    }

    /**
     * Marquer comme non lu
     */
    public function markAsUnread($tripSlug)
    {
        // Récupérer le trip par son slug
        $trip = Trip::where('slug', $tripSlug)->firstOrFail();

        // Récupérer la conversation_id pour ce trip
        $conversationId = Message::where('trip_id', $trip->id)
            ->where(function($query) {
                $query->where('sender_id', Auth::id())
                      ->orWhere('recipient_id', Auth::id());
            })
            ->value('conversation_id');

        if ($conversationId) {
            Message::where('conversation_id', $conversationId)
                ->where('recipient_id', Auth::id())
                ->update([
                    'is_read' => false,
                    'read_at' => null
                ]);
        }

        return redirect()->route('vendor.messages.index')
            ->with('success', 'Conversation marquée comme non lue');
    }

    /**
     * Marquer tous les messages comme lus
     */
    public function markAllAsRead()
    {
        Message::where('recipient_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return back()->with('success', 'Tous les messages ont été marqués comme lus');
    }

    /**
     * Rechercher dans les conversations
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        if (empty($query)) {
            return redirect()->route('vendor.messages.index');
        }

        $conversations = Message::select(
                'messages.*',
                DB::raw('(SELECT COUNT(*) FROM messages m2 WHERE m2.conversation_id = messages.conversation_id
                         AND m2.recipient_id = ' . Auth::id() . ' AND m2.is_read = 0) as unread_count')
            )
            ->whereIn('messages.id', function($q) use ($query) {
                $q->select(DB::raw('MAX(id)'))
                    ->from('messages')
                    ->where(function($subQ) {
                        $subQ->where('sender_id', Auth::id())
                             ->orWhere('recipient_id', Auth::id());
                    })
                    ->where(function($searchQ) use ($query) {
                        $searchQ->where('content', 'like', "%{$query}%")
                                ->orWhere('subject', 'like', "%{$query}%");
                    })
                    ->groupBy('conversation_id');
            })
            ->with(['sender', 'recipient', 'trip'])
            ->orderBy('created_at', 'desc')
            ->get();

        $unreadCount = Message::where('recipient_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return view('vendor.messages.index', compact('conversations', 'unreadCount', 'query'))
            ->with('filter', 'search');
    }

    /**
     * Supprimer une conversation (soft delete)
     */
    public function delete($tripSlug)
    {
        // Récupérer le trip par son slug
        $trip = Trip::where('slug', $tripSlug)->firstOrFail();

        // Récupérer la conversation_id pour ce trip
        $conversationId = Message::where('trip_id', $trip->id)
            ->where(function($query) {
                $query->where('sender_id', Auth::id())
                      ->orWhere('recipient_id', Auth::id());
            })
            ->value('conversation_id');

        if ($conversationId) {
            Message::where('conversation_id', $conversationId)
                ->where(function($query) {
                    $query->where('sender_id', Auth::id())
                          ->orWhere('recipient_id', Auth::id());
                })
                ->delete(); // Utilise le soft delete de Laravel
        }

        return redirect()->route('vendor.messages.index')
            ->with('success', 'Conversation supprimée');
    }
}