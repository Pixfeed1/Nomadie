<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientEmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $verificationUrl;

    public function __construct(User $user)
    {
        $this->user = $user;
        // Génération de l'URL de vérification avec le token
        $this->verificationUrl = route('client.verify.email', [
            'token' => $user->email_verification_token
        ]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Vérifiez votre adresse email - ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.client.verification',
            with: [
                'user' => $this->user,
                'verificationUrl' => $this->verificationUrl,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}