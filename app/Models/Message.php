<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'sender_type', // customer, vendor, admin
        'recipient_id',
        'recipient_type', // customer, vendor
        'subject',
        'content',
        'attachment',
        'attachment_name',
        'attachment_type',
        'attachment_size',
        'is_read',
        'read_at',
        'booking_id',
        'trip_id',
        'conversation_id', // pour grouper les messages d'une même conversation
        'is_archived',
        'archived_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_archived' => 'boolean',
        'read_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    // Relations
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    // Scopes pour filtrer les messages
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('sender_id', $userId)
              ->orWhere('recipient_id', $userId);
        });
    }

    public function scopeConversation($query, $conversationId)
    {
        return $query->where('conversation_id', $conversationId)
                     ->orderBy('created_at', 'asc');
    }

    public function scopeNotArchived($query)
    {
        return $query->where('is_archived', false);
    }

    // Méthodes utilitaires
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    public function archive()
    {
        $this->update([
            'is_archived' => true,
            'archived_at' => now(),
        ]);
    }

    public function getOtherParticipant($userId)
    {
        return $this->sender_id == $userId 
            ? $this->recipient 
            : $this->sender;
    }

    // Vérifier si le vendor peut envoyer un message
    public function canVendorSendMessage($vendorId, $customerId, $conversationId)
    {
        // Le vendor peut répondre si une conversation existe déjà
        // (donc si le client a déjà initié le contact)
        return self::where('conversation_id', $conversationId)
                   ->where(function($q) use ($customerId, $vendorId) {
                       $q->where(['sender_id' => $customerId, 'recipient_id' => $vendorId])
                         ->orWhere(['sender_id' => $vendorId, 'recipient_id' => $customerId]);
                   })
                   ->exists();
    }

    // Générer un ID de conversation unique
    public static function generateConversationId($userId1, $userId2, $tripId = null)
    {
        $ids = [$userId1, $userId2];
        sort($ids);
        $base = implode('-', $ids);
        
        return $tripId ? "{$base}-trip-{$tripId}" : $base;
    }

    // Obtenir le dernier message d'une conversation
    public function scopeLatestInConversation($query)
    {
        return $query->whereIn('id', function($subquery) {
            $subquery->selectRaw('MAX(id)')
                     ->from('messages')
                     ->groupBy('conversation_id');
        });
    }

    // Vérifier qui a initié la conversation
    public function getConversationInitiator()
    {
        $firstMessage = self::where('conversation_id', $this->conversation_id)
                            ->orderBy('created_at', 'asc')
                            ->first();
        
        return $firstMessage ? $firstMessage->sender : null;
    }
}