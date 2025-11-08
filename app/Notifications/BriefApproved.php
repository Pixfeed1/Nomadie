<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\ContentBrief;

class BriefApproved extends Notification
{
    use Queueable;

    protected $brief;

    public function __construct(ContentBrief $brief)
    {
        $this->brief = $brief;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('✅ Brief approuvé : ' . $this->brief->title)
            ->greeting('Félicitations ' . $notifiable->name . ' !')
            ->line('Votre brief "' . $this->brief->title . '" a été approuvé par l\'équipe admin !')
            ->line('**Titre :** ' . $this->brief->title)
            ->when($this->brief->article, function ($mail) {
                return $mail->line('**Article :** ' . $this->brief->article->title);
            })
            ->when($this->brief->article && $this->brief->article->latestSeoAnalysis, function ($mail) {
                return $mail->line('**Score SEO :** ' . $this->brief->article->latestSeoAnalysis->overall_score . '/100');
            })
            ->action('Voir mes briefs', url('/writer/briefs'))
            ->line('Excellent travail ! Continuez comme ça 🎉');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'brief_approved',
            'brief_id' => $this->brief->id,
            'brief_title' => $this->brief->title,
            'article_id' => $this->brief->article_id,
            'message' => 'Brief approuvé : ' . $this->brief->title,
            'url' => '/writer/briefs/' . $this->brief->id
        ];
    }
}
