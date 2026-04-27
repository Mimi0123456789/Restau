<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\BienvenueMail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'prenom' => ['required', 'string', 'max:50'],
            'nom' => ['required', 'string', 'max:50'],
            'telephone' => ['required', 'string', 'max:20'],
            'adresse_postale' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                Password::min(10)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols(),
            ],
        ], [
            'password.min' => 'Le mot de passe doit contenir au moins 10 caractères.',
            'password.mixed' => 'Le mot de passe doit contenir au moins une majuscule et une minuscule.',
            'password.numbers' => 'Le mot de passe doit contenir au moins un chiffre.',
            'password.symbols' => 'Le mot de passe doit contenir au moins un caractère spécial.',
        ]);

        $user = User::create([
            'prenom' => $request->prenom,
            'nom' => $request->nom,
            'telephone' => $request->telephone,
            'adresse_postale' => $request->adresse_postale,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 2,
            'pays' => 'France',
            'is_active' => 1,
        ]);

        event(new Registered($user));

        Mail::to($user->email)->send(new BienvenueMail($user));

        Auth::login($user);

        return redirect(route('home', absolute: false));
    }
}