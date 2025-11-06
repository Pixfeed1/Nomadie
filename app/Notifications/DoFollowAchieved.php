<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DoFollowAchieved extends Notification
{
    use Queueable;

    protected $articleCount;

    public function __construct($articleCount)
    {
        $this->articleCount = $articleCount;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🚀 Vos liens sont maintenant en DoFollow !')
            ->greeting('Excellente nouvelle ' . $notifiable->name . ' !')
            ->line('Vos articles ont atteint les critères de qualité requis.')
            ->line('Tous vos liens sont maintenant en DoFollow !')
            ->line('Vous avez ' . $this->articleCount . ' articles avec un score moyen supérieur à 78/100.')
            ->action('Voir mes articles', url('/writer/articles'))
            ->line('Votre SEO va décoller !');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'dofollow_achieved',
            'article_count' => $this->articleCount,
            'message' => 'Félicitations ! Vos liens sont maintenant en DoFollow',
            'url' => '/writer/articles'
        ];
    }
}
