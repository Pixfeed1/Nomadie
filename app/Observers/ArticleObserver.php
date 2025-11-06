<?php

namespace App\Observers;

use App\Models\Article;
use App\Jobs\CheckUserBadges;
use App\Jobs\CheckDoFollowStatus;

class ArticleObserver
{
    public function created(Article $article)
    {
        // Vérifier les badges après création
        CheckUserBadges::dispatch($article->user);
    }

    public function updated(Article $article)
    {
        // Si l'article passe en publié
        if ($article->isDirty('status') && $article->status === 'published') {
            CheckUserBadges::dispatch($article->user);
            CheckDoFollowStatus::dispatch($article->user);
        }
    }
}