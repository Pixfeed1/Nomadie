<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\BadgeUnlocked;

class UserBadge extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'badge_id',
        'progress_data',
        'progress_percentage',
        'unlocked_at',
        'notified_at',
        'is_featured'
    ];

    protected $casts = [
        'progress_data' => 'array',
        'progress_percentage' => 'integer',
        'unlocked_at' => 'datetime',
        'notified_at' => 'datetime',
        'is_featured' => 'boolean'
    ];

    /**
     * Boot
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($userBadge) {
            // Notifier l'utilisateur du nouveau badge
            if (!$userBadge->notified_at) {
                $userBadge->notifyUser();
            }
        });
    }

    /**
     * Relations
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }

    /**
     * Methods
     */
    public function notifyUser()
    {
        // Envoyer une notification (à implémenter)
        // $this->user->notify(new BadgeUnlocked($this->badge));
        
        $this->notified_at = now();
        $this->save();
    }

    public function updateProgress($data, $percentage = null)
    {
        $this->progress_data = array_merge($this->progress_data ?? [], $data);
        
        if ($percentage !== null) {
            $this->progress_percentage = min(100, max(0, $percentage));
        }
        
        $this->save();
        
        return $this;
    }

    public function feature()
    {
        // Retirer le featured des autres badges de cet utilisateur
        static::where('user_id', $this->user_id)
              ->where('id', '!=', $this->id)
              ->update(['is_featured' => false]);
        
        $this->is_featured = true;
        $this->save();
        
        return $this;
    }
}