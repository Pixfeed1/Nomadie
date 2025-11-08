<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\ContentBrief;

class BriefAssigned extends Notification
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
            ->subject('📝 Nouveau brief assigné : ' . $this->brief->title)
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line('Un nouveau brief vous a été assigné.')
            ->line('**Titre :** ' . $this->brief->title)
            ->line('**Type :** ' . $this->brief->getTypeLabel())
            ->line('**Priorité :** ' . ucfirst($this->brief->priority))
            ->when($this->brief->deadline, function ($mail) {
                return $mail->line('**Deadline :** ' . $this->brief->deadline->format('d/m/Y'));
            })
            ->line('**Mots minimum :** ' . ($this->brief->min_words ?? 'N/A'))
            ->line('**Score cible :** ' . ($this->brief->target_score ?? 'N/A') . '/100')
            ->action('Voir le brief', url('/writer/briefs/' . $this->brief->id))
            ->line('Bon courage pour la rédaction ! 💪');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'brief_assigned',
            'brief_id' => $this->brief->id,
            'brief_title' => $this->brief->title,
            'brief_type' => $this->brief->type,
            'priority' => $this->brief->priority,
            'deadline' => $this->brief->deadline?->toDateString(),
            'message' => 'Nouveau brief assigné : ' . $this->brief->title,
            'url' => '/writer/briefs/' . $this->brief->id
        ];
    }
}
