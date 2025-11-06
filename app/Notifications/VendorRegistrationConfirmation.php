<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorRegistrationConfirmation extends Notification
{
    use Queueable;

    protected $vendor;

    public function __construct($vendor)
    {
        $this->vendor = $vendor;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = route('vendor.confirm', $this->vendor->confirmation_token);

        return (new MailMessage)
            ->subject('Confirmez votre inscription - Marketplace Voyages')
            ->greeting('Bonjour ' . $this->vendor->rep_firstname . ',')
            ->line('Merci de vous être inscrit sur notre plateforme.')
            ->line('Veuillez cliquer sur le bouton ci-dessous pour confirmer votre adresse email.')
            ->action('Confirmer mon email', $url)
            ->line('Si vous n\'avez pas créé de compte, ignorez cet email.')
            ->salutation('Cordialement, L\'équipe Marketplace Voyages');
    }
}