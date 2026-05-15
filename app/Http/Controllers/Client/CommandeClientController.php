<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Mail\ConfirmationCommandeMail;
use App\Models\Commande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


class CommandeClientController extends Controller
{
    public function index()
    {
        $commandes = Commande::with('menus')
            ->where('user_id', auth()->id())
            ->latest('id')
            ->paginate(10);

        return view('client.commandes.index', compact('commandes'));
    }

    public function show(Commande $commande)
    {
        abort_if($commande->user_id !== auth()->id(), 403);

        $commande->load(['menus', 'avis', 'statuts']);

        return view('client.commandes.show', compact('commande'));
    }

    public function edit(Commande $commande)
    {
        abort_if($commande->user_id !== auth()->id(), 403);

        if ($commande->statut !== 'En attente') {
            return redirect()
                ->route('client.commandes.show', $commande)
                ->withErrors([
                    'commande' => 'Cette commande ne peut plus être modifiée.',
                ]);
        }

        return view('client.commandes.edit', compact('commande'));
    }

    public function update(Request $request, Commande $commande)
    {
        abort_if($commande->user_id !== auth()->id(), 403);

        if ($commande->statut !== 'En attente') {
            return redirect()
                ->route('client.commandes.show', $commande)
                ->withErrors([
                    'commande' => 'Cette commande ne peut plus être modifiée.',
                ]);
        }

        $validated = $request->validate([
            'date_prestation' => ['required', 'date', 'after_or_equal:today'],
            'heure_livraison' => ['required', 'date_format:H:i'],
            'nombre_personne' => ['required', 'integer', 'min:1'],
            'pret_materiel' => ['nullable', 'boolean'],
            'restitution_materiel' => ['nullable', 'boolean'],
        ], [
            'date_prestation.after_or_equal' => 'La date de prestation doit être aujourd’hui ou une date future.',
            'heure_livraison.date_format' => 'L’heure de livraison doit être au format HH:MM.',
        ]);

        $commande->update([
            'date_prestation' => $validated['date_prestation'],
            'heure_livraison' => $validated['heure_livraison'] . ':00',
            'nombre_personne' => $validated['nombre_personne'],
            'pret_materiel' => $request->boolean('pret_materiel'),
            'restitution_materiel' => $request->boolean('restitution_materiel'),
        ]);

        $commande->load(['user', 'menus']);

        Mail::to($commande->user->email)
            ->send(new ConfirmationCommandeMail($commande, 'modification'));

        return redirect()
            ->route('client.commandes.show', $commande)
            ->with('ok', 'Votre commande a bien été modifiée.');
    }

    public function cancel(Commande $commande)
    {
        abort_if($commande->user_id !== auth()->id(), 403);

        if ($commande->statut !== 'En attente') {
            return redirect()
                ->route('client.commandes.show', $commande)
                ->withErrors([
                    'commande' => 'Cette commande ne peut plus être annulée.',
                ]);
        }

        $commande->update([
            'statut' => 'Annulée',
        ]);

        $commande->statuts()->create([
            'statut' => 'Annulée',
        ]);

        return redirect()
            ->route('client.commandes.index')
            ->with('ok', 'Votre commande a bien été annulée.');
    }
}