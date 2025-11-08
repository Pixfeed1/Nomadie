<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\ContentBrief;

class RevisionRequested extends Notification
{
    use Queueable;

    protected $brief;
    protected $notes;

    public function __construct(ContentBrief $brief, $notes = null)
    {
        $this->brief = $brief;
        $this->notes = $notes;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('🔄 Révision demandée : ' . $this->brief->title)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('L\'équipe admin a demandé une révision pour le brief "' . $this->brief->title . '".')
            ->line('**Titre :** ' . $this->brief->title);

        if ($this->notes) {
            $mail->line('**Modifications demandées :**')
                 ->line($this->notes);
        }

        return $mail
            ->when($this->brief->deadline, function ($mail) {
                return $mail->line('**Deadline :** ' . $this->brief->deadline->format('d/m/Y'));
            })
            ->action('Voir le brief', url('/writer/briefs/' . $this->brief->id))
            ->line('Merci de prendre en compte ces modifications. 🙏');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'revision_requested',
            'brief_id' => $this->brief->id,
            'brief_title' => $this->brief->title,
            'notes' => $this->notes,
            'message' => 'Révision demandée pour : ' . $this->brief->title,
            'url' => '/writer/briefs/' . $this->brief->id
        ];
    }
}
