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
            // Notifier l'utilisateur du nouveau badge de manière asynchrone
            // On wrappe dans un try/catch au cas où le dispatch échoue
            if (!$userBadge->notified_at) {
                try {
                    // Dispatcher la notification de manière asynchrone pour ne pas bloquer le process
                    dispatch(function() use ($userBadge) {
                        $userBadge->notifyUser();
                    })->afterResponse();
                } catch (\Exception $e) {
                    \Log::error("Failed to dispatch badge notification", [
                        'user_id' => $userBadge->user_id,
                        'badge_id' => $userBadge->badge_id,
                        'error' => $e->getMessage()
                    ]);
                }
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
        try {
            // Vérifier que l'utilisateur et le badge existent
            if (!$this->user || !$this->badge) {
                throw new \Exception("User or Badge not loaded");
            }

            // Envoyer la notification (si SMTP échoue, on log mais on ne crash pas)
            $this->user->notify(new BadgeUnlocked($this->badge));

            $this->notified_at = now();
            $this->save();

            \Log::info("Badge notification sent successfully", [
                'user_id' => $this->user_id,
                'badge_id' => $this->badge_id,
                'badge_code' => $this->badge->code,
                'badge_name' => $this->badge->name
            ]);
        } catch (\Symfony\Component\Mailer\Exception\TransportException $e) {
            // Erreur SMTP spécifique - on log et on continue
            \Log::warning("Badge notification email failed (SMTP error) - notification saved in database", [
                'user_id' => $this->user_id,
                'badge_id' => $this->badge_id,
                'badge_code' => $this->badge->code ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            // Marquer comme notifié quand même (la notification est dans la DB)
            $this->notified_at = now();
            $this->save();
        } catch (\Exception $e) {
            // Erreur générique
            \Log::error("Failed to send badge notification", [
                'user_id' => $this->user_id,
                'badge_id' => $this->badge_id,
                'badge_code' => $this->badge->code ?? 'unknown',
                'error' => $e->getMessage(),
                'type' => get_class($e)
            ]);

            // Même en cas d'erreur, on marque comme notifié pour éviter les boucles
            $this->notified_at = now();
            $this->save();
        }
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