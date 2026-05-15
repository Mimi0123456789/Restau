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

        if ($commande->avis()->exists()) {
            return redirect()
                ->route('client.commandes.show', $commande)
                ->withErrors([
                    'avis' => 'Vous avez déjà déposé un avis pour cette commande.',
                ]);
        }

        $validated = $request->validate([
            'note' => ['required', 'integer', 'between:1,5'],
            'commentaire' => ['nullable', 'string', 'max:1000'],
        ]);

        Avis::create([
            'commande_id' => $commande->id,
            'note' => $validated['note'],
            'commentaire' => $validated['commentaire'] ?? null,
            'statut' => 'en attente',
        ]);

        return redirect()
            ->route('client.commandes.show', $commande)
            ->with('ok', 'Votre avis a bien été enregistré.');
    }
}