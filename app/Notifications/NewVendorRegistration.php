<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewVendorRegistration extends Notification
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
        return (new MailMessage)
            ->subject('Nouvelle inscription vendeur - ' . $this->vendor->company_name)
            ->greeting('Bonjour Administrateur,')
            ->line('Un nouveau vendeur s\'est inscrit sur la plateforme.')
            ->line('**Informations du vendeur :**')
            ->line('Entreprise : ' . $this->vendor->company_name)
            ->line('Contact : ' . $this->vendor->rep_firstname . ' ' . $this->vendor->rep_lastname)
            ->line('Email : ' . $this->vendor->rep_email)
            ->line('Téléphone : ' . $this->vendor->phone)
            ->line('Abonnement : ' . ucfirst($this->vendor->subscription_plan))
            ->action('Voir le vendeur', url('/admin/vendors/' . $this->vendor->id))
            ->line('Ce vendeur est en attente de validation.')
            ->salutation('Système de notification automatique');
    }

    public function toArray($notifiable)
    {
        return [
            'vendor_id' => $this->vendor->id,
            'company_name' => $this->vendor->company_name,
            'email' => $this->vendor->rep_email,
            'subscription_plan' => $this->vendor->subscription_plan
        ];
    }
}