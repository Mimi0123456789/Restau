<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $employe;
    public string $password;

    public function __construct(User $employe, string $password)
    {
        $this->employe = $employe;
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject('Création de votre compte employé')
            ->view('emails.employe-created')
            ->with([
                'employe' => $this->employe,
                'password' => $this->password,
            ]);
    }
}