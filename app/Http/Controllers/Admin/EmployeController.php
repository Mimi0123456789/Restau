<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmployeCreatedMail;
use Illuminate\Support\Str;

class EmployeController extends Controller
{
    private function generatePassword(int $length = 10): string
    {
        $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lower = 'abcdefghijklmnopqrstuvwxyz';
        $digits = '0123456789';
        $symbols = '!@#$%&*';

        $all = $upper . $lower . $digits . $symbols;

        return collect([
            $upper[rand(0, strlen($upper) - 1)],
            $lower[rand(0, strlen($lower) - 1)],
            $digits[rand(0, strlen($digits) - 1)],
            $symbols[rand(0, strlen($symbols) - 1)],
            ...str_split(Str::random($length - 4)),
        ])->shuffle()->implode('');
    }

    public function index()
    {
        $employes = User::where('role_id', 3)
            ->orderBy('prenom')
            ->paginate(10);

        return view('dashboard.admin.employes.index', compact('employes'));
    }


    public function create()
    {
        return view('dashboard.admin.employes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'prenom' => 'required|string|max:255',
            'nom'    => 'required|string|max:255',
            'email'  => 'required|email|unique:users,email',
        ]);

        $password = $this->generatePassword();

        $employe = User::create([
            'prenom'          => $request->prenom,
            'nom'             => $request->nom,
            'email'           => $request->email,
            'password'        => Hash::make($password),
            'adresse_postale' => 'Vite et gourmand',
            'telephone'       => '0000000000',
            'ville'           => 'Bordeaux',
            'code_postal'     => '33000',
            'pays'            => 'France',
            'role_id'         => 3,
            'is_active'       => true,
        ]);

        Mail::to($employe->email)->send(
            new EmployeCreatedMail($employe)
        );

        return redirect()
            ->route('admin.employes.index')
            ->with('ok', 'Employé créé avec succès.');
    }

    public function toggle(User $employe)
    {
        // Sécurité : uniquement les employés
        abort_if($employe->role_id !== 3, 403);

        $employe->update([
            'is_active' => ! $employe->is_active,
        ]);

        return redirect()
            ->route('admin.employes.index')
            ->with('ok', 'Statut de l’employé mis à jour.');
    }


    public function update(Request $request, User $employe)
    {
        abort_if($employe->role_id !== 3, 403);

        $request->validate([
            'prenom' => 'required|string|max:255',
            'nom'    => 'required|string|max:255',
            'email'  => 'required|email|unique:users,email,' . $employe->id,
        ]);

        $employe->update([
            'prenom' => $request->prenom,
            'nom'    => $request->nom,
            'email'  => $request->email,
        ]);

        return redirect()
            ->route('admin.employes.index')
            ->with('ok', 'Employé modifié.');
    }

}
