<?php

namespace App\Mail;

use App\Models\Commande;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DemandeAvisMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Commande $commande)
    {
    }

    public function build()
    {
        return $this->subject(
                'Votre avis nous intéresse - Commande #' . $this->commande->id
            )
            ->view('emails.commandes.demande-avis', [
                'commande' => $this->commande,
                'user' => $this->commande->user,
            ]);
    }
}