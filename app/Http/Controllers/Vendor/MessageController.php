<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $vendorId = Auth::id();
        
        // Récupérer les conversations groupées
        $conversations = Message::forUser($vendorId)
            ->notArchived()
            ->latestInConversation()
            ->with(['sender', 'recipient', 'trip'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $unreadCount = Message::where('recipient_id', $vendorId)
            ->unread()
            ->count();

        return view('vendor.messages.index', compact('conversations', 'unreadCount'));
    }

    public function show($conversationId)
    {
        $vendorId = Auth::id();
        
        // Récupérer tous les messages de la conversation
        $messages = Message::conversation($conversationId)
            ->with(['sender', 'recipient', 'trip'])
            ->get();

        // Vérifier que le vendor fait partie de cette conversation
        if (!$messages->first() || 
            ($messages->first()->sender_id != $vendorId && 
             $messages->first()->recipient_id != $vendorId)) {
            abort(403, 'Accès non autorisé à cette conversation');
        }

        // Marquer comme lus
        Message::where('conversation_id', $conversationId)
            ->where('recipient_id', $vendorId)
            ->unread()
            ->each(function($message) {
                $message->markAsRead();
            });

        $otherParticipant = $messages->first()->getOtherParticipant($vendorId);
        $trip = $messages->first()->trip;

        return view('vendor.messages.show', compact('messages', 'conversationId', 'otherParticipant', 'trip'));
    }

    public function reply(Request $request, $conversationId)
    {
        $request->validate([
            'content' => 'required|string|max:5000'
        ]);

        $vendorId = Auth::id();
        
        // Vérifier que la conversation existe et que le vendor peut répondre
        $existingMessage = Message::where('conversation_id', $conversationId)
            ->where(function($q) use ($vendorId) {
                $q->where('sender_id', $vendorId)
                  ->orWhere('recipient_id', $vendorId);
            })
            ->firstOrFail();

        // Identifier le destinataire (le client)
        $recipientId = $existingMessage->sender_id == $vendorId 
            ? $existingMessage->recipient_id 
            : $existingMessage->sender_id;

        // Créer la réponse
        Message::create([
            'sender_id' => $vendorId,
            'sender_type' => 'vendor',
            'recipient_id' => $recipientId,
            'recipient_type' => 'customer',
            'conversation_id' => $conversationId,
            'subject' => $existingMessage->subject,
            'content' => $request->content,
            'trip_id' => $existingMessage->trip_id,
            'is_read' => false
        ]);

        return redirect()->route('vendor.messages.show', $conversationId)
            ->with('success', 'Message envoyé');
    }

    public function archive($conversationId)
    {
        Message::where('conversation_id', $conversationId)
            ->where('recipient_id', Auth::id())
            ->update(['is_archived' => true, 'archived_at' => now()]);

        return redirect()->route('vendor.messages.index')
            ->with('success', 'Conversation archivée');
    }
}