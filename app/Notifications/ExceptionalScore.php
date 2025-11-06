<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Article;

class ExceptionalScore extends Notification
{
    use Queueable;

    protected $article;
    protected $score;

    public function __construct(Article $article, $score)
    {
        $this->article = $article;
        $this->score = $score;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'exceptional_score',
            'article_id' => $this->article->id,
            'article_title' => $this->article->title,
            'score' => $this->score,
            'message' => 'Score exceptionnel de ' . $this->score . '/100 pour "' . $this->article->title . '"',
            'url' => '/writer/articles/' . $this->article->id . '/edit'
        ];
    }
}