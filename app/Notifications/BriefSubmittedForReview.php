<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\ContentBrief;

class BriefSubmittedForReview extends Notification
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
            ->subject('📥 Brief soumis pour review : ' . $this->brief->title)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Un brief a été soumis pour review par ' . $this->brief->assignedTo->name . '.')
            ->line('**Titre :** ' . $this->brief->title)
            ->line('**Rédacteur :** ' . $this->brief->assignedTo->name)
            ->when($this->brief->article, function ($mail) {
                return $mail->line('**Article :** ' . $this->brief->article->title)
                    ->line('**Nombre de mots :** ' . ($this->brief->article->word_count ?? 'N/A'));
            })
            ->when($this->brief->article && $this->brief->article->latestSeoAnalysis, function ($mail) {
                return $mail->line('**Score SEO :** ' . $this->brief->article->latestSeoAnalysis->overall_score . '/100');
            })
            ->when($this->brief->writer_notes, function ($mail) {
                return $mail->line('**Message du rédacteur :**')
                    ->line($this->brief->writer_notes);
            })
            ->action('Examiner le brief', url('/admin/briefs/' . $this->brief->id))
            ->line('Merci de valider ou demander des révisions.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'brief_submitted',
            'brief_id' => $this->brief->id,
            'brief_title' => $this->brief->title,
            'writer_name' => $this->brief->assignedTo->name,
            'article_id' => $this->brief->article_id,
            'message' => 'Brief soumis pour review : ' . $this->brief->title . ' par ' . $this->brief->assignedTo->name,
            'url' => '/admin/briefs/' . $this->brief->id
        ];
    }
}
