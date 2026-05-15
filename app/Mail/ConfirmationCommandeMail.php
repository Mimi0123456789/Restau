<?php

namespace App\Mail;

use App\Models\Commande;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmationCommandeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Commande $commande,
        public string $type = 'creation'
    ) {
    }

    public function build()
    {
        $sujet = match ($this->type) {
            'modification' => 'Modification de votre commande #' . $this->commande->id,
            'statut' => 'Mise à jour du statut de votre commande #' . $this->commande->id,
            default => 'Confirmation de votre commande #' . $this->commande->id,
        };

        return $this->subject($sujet)
            ->markdown('emails.commandes.confirmation', [
                'commande' => $this->commande,
                'type' => $this->type,
            ]);
    }
}