<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeClient extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $loginUrl;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->loginUrl = route('login');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenue ! Votre compte est maintenant actif - ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.client.welcome',
            with: [
                'user' => $this->user,
                'loginUrl' => $this->loginUrl,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}