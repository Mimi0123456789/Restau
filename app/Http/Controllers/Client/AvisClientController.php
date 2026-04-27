<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Avis;
use App\Models\Commande;
use Illuminate\Http\Request;

class AvisClientController extends Controller
{
    public function store(Request $request, Commande $commande)
    {
        abort_if($commande->user_id !== auth()->id(), 403);

        if (!in_array(mb_strtolower($commande->statut), ['terminée', 'terminee', 'livrée', 'livree'])) {
            return redirect()
                ->route('client.commandes.show', $commande)
                ->withErrors([
                    'avis' => 'Vous ne pouvez laisser un avis qu’une fois la commande terminée.',
                ]);
        }

        $validated = $request->validate([
            'note' => ['required', 'integer', 'between:1,5'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        Avis::updateOrCreate(
            [
                'commande_id' => $commande->id,
            ],
            [
                'note' => $validated['note'],
                'description' => $validated['description'] ?? null,
                'statut' => 'en attente',
            ]
        );

        return redirect()
            ->route('client.commandes.show', $commande)
            ->with('ok', 'Votre avis a bien été enregistré.');
    }
}