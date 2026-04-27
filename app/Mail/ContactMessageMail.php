<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $titre,
        public string $description,
        public string $email,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouveau message de contact : ' . $this->titre,
            replyTo: [$this->email],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-message',
        );
    }
}