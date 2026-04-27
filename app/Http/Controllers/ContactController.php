<?php
namespace App\Http\Controllers;

use App\Mail\ContactMessageMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'titre' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:5000'],
            'email' => ['required', 'email', 'max:255'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
            'description.required' => 'La description est obligatoire.',
            'email.required' => 'L’adresse mail est obligatoire.',
            'email.email' => 'L’adresse mail n’est pas valide.',
        ]);

        Mail::to(config('mail.contact_address', env('MAIL_FROM_ADDRESS')))
            ->send(new ContactMessageMail(
                titre: $data['titre'],
                description: $data['description'],
                email: $data['email'],
            ));

        return redirect()
            ->route('contact.create')
            ->with('success', 'Votre demande a bien été envoyée. Nous vous répondrons rapidement.');
    }
}
