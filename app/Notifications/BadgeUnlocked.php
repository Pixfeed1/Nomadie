<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\Badge;

class BadgeUnlocked extends Notification
{
    use Queueable;

    protected $badge;

    public function __construct(Badge $badge)
    {
        $this->badge = $badge;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🎉 Nouveau badge débloqué : ' . $this->badge->name)
            ->greeting('Félicitations ' . $notifiable->name . ' !')
            ->line('Vous avez débloqué le badge "' . $this->badge->name . '"')
            ->line($this->badge->description)
            ->action('Voir mes badges', url('/writer/badges'))
            ->line('Continuez comme ça !');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'badge_unlocked',
            'badge_id' => $this->badge->id,
            'badge_name' => $this->badge->name,
            'badge_icon' => $this->badge->icon,
            'message' => 'Vous avez débloqué le badge "' . $this->badge->name . '"',
            'url' => '/writer/badges'
        ];
    }
}