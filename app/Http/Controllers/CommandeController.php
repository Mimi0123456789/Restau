<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use Illuminate\Http\Request;

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
            app(\App\Services\StatsExportService::class)
            ->exportCommande($commande->fresh());

            $commande->statuts()->create([
                'statut' => $validated['statut'],
            ]);
        }

        return redirect()
            ->route('commandes.show', $commande)
            ->with('ok', 'Le statut de la commande a bien été mis à jour.');
    }
}