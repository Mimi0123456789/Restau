<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmployeCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Employe Created Mail',
        );
    }

    /*public function content(): Content
    {
        return new Content(
            view: 'emails.employe-created',
        );
    }*/

    public function attachments(): array
    {
        return [];
    }

    public function build()
    {
        return $this->subject('Création de votre compte employé')
            ->view('emails.employe-created');
    }

}
