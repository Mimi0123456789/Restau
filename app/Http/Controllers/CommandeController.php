<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmationCommandeMail;
use App\Models\Commande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\DemandeAvisMail;

class CommandeController extends Controller
{
    public const STATUTS = [
        'En attente',
        'Accepté',
        'En préparation',
        'En cours de livraison',
        'Livré',
        'En attente du retour de matériel',
        'Terminée',
        'Annulée',
    ];

    public function index(Request $request)
    {
        $query = Commande::with(['user', 'menus'])->latest();

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('client')) {
            $client = $request->client;

            $query->whereHas('user', function ($q) use ($client) {
                $q->where('nom', 'like', "%{$client}%")
                    ->orWhere('prenom', 'like', "%{$client}%")
                    ->orWhere('email', 'like', "%{$client}%");
            });
        }

        $commandes = $query->paginate(15)->withQueryString();

        return view('commandes.index', [
            'commandes' => $commandes,
            'statuts' => self::STATUTS,
        ]);
    }

    public function show(Commande $commande)
    {
        $commande->load(['user', 'menus', 'avis', 'statuts']);

        return view('commandes.show', [
            'commande' => $commande,
            'statuts' => self::STATUTS,
        ]);
    }

    public function updateStatut(Request $request, Commande $commande)
    {
        $validated = $request->validate([
            'statut' => ['required', 'in:' . implode(',', self::STATUTS)],
        ]);

        if ($commande->statut !== $validated['statut']) {
            $commande->update([
                'statut' => $validated['statut'],
            ]);

            $commande->statuts()->create([
                'statut' => $validated['statut'],
            ]);

            $commande->load(['user', 'menus']);

            Mail::to($commande->user->email)
                ->send(new ConfirmationCommandeMail($commande, 'statut'));
        }

        if ($validated['statut'] === 'Terminée') {
            Mail::to($commande->user->email)
                ->send(new DemandeAvisMail($commande));
        }

        return redirect()
            ->route('commandes.show', $commande)
            ->with('ok', 'Le statut de la commande a bien été mis à jour.');
    }
}