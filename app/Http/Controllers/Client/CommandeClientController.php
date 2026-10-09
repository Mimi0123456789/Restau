<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Mail\ConfirmationCommandeMail;
use App\Models\Commande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'quantites' => ['nullable', 'array'],
            'quantites.*' => ['required', 'integer', 'min:1'],
            'pret_materiel' => ['nullable', 'boolean'],
            'restitution_materiel' => ['nullable', 'boolean'],
        ], [
            'date_prestation.after_or_equal' => 'La date de prestation doit être aujourd’hui ou une date future.',
            'heure_livraison.date_format' => 'L’heure de livraison doit être au format HH:MM.',
        ]);

        $commande->load('menus');

        $quantites = $request->input('quantites', []);

        foreach ($commande->menus as $menu) {
            $menuId = $menu->id;
            $ancienneQuantite = (int) ($menu->pivot->quantite ?? 0);
            $nouvelleQuantite = (int) ($quantites[$menuId] ?? $ancienneQuantite);

            if ($nouvelleQuantite < $menu->nombre_personne_minimum) {
                return redirect()->back()->withErrors([
                    "quantites.$menuId" => "La quantité du menu \"{$menu->titre}\" doit être d'au moins {$menu->nombre_personne_minimum}.",
                ]);
            }

            $difference = $nouvelleQuantite - $ancienneQuantite;

            if ($difference > 0 && $menu->quantite_restante < $difference) {
                return redirect()->back()->withErrors([
                    "quantites.$menuId" => "Il ne reste que {$menu->quantite_restante} exemplaire(s) du menu \"{$menu->titre}\".",
                ]);
            }
        }

        DB::transaction(function () use ($commande, $request, $validated, $quantites) {
            $syncData = [];
            $totalMenus = 0;

            foreach ($commande->menus as $menu) {
                $menuId = $menu->id;
                $ancienneQuantite = (int) ($menu->pivot->quantite ?? 0);
                $nouvelleQuantite = (int) ($quantites[$menuId] ?? $ancienneQuantite);
                $difference = $nouvelleQuantite - $ancienneQuantite;

                if ($difference > 0) {
                    $menu->decrement('quantite_restante', $difference);
                } elseif ($difference < 0) {
                    $menu->increment('quantite_restante', abs($difference));
                }

                $prixUnitaire = round($menu->prix_par_personne * $validated['nombre_personne'], 2);

                if ($validated['nombre_personne'] >= ($menu->nombre_personne_minimum + 5)) {
                    $prixUnitaire = round($prixUnitaire * 0.90, 2);
                }

                $prixTotal = round($prixUnitaire * $nouvelleQuantite, 2);
                $totalMenus = round($totalMenus + $prixTotal, 2);

                $syncData[$menuId] = [
                    'quantite' => $nouvelleQuantite,
                    'prix_unitaire' => $prixUnitaire,
                    'prix_total' => $prixTotal,
                ];
            }

            $commande->update([
                'date_prestation' => $validated['date_prestation'],
                'heure_livraison' => $validated['heure_livraison'] . ':00',
                'nombre_personne' => $validated['nombre_personne'],
                'prix_menu' => $totalMenus,
                'pret_materiel' => $request->boolean('pret_materiel'),
                'restitution_materiel' => $request->boolean('restitution_materiel'),
            ]);

            $commande->menus()->sync($syncData);
        });

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

        DB::transaction(function () use ($commande) {
            $commande->update([
                'statut' => 'Annulée',
            ]);

            foreach ($commande->menus as $menu) {
                $quantite = (int) ($menu->pivot->quantite ?? 0);

                if ($quantite > 0) {
                    $menu->increment('quantite_restante', $quantite);
                }
            }

            $commande->statuts()->create([
                'statut' => 'Annulée',
            ]);
        });

        return redirect()
            ->route('client.commandes.index')
            ->with('ok', 'Votre commande a bien été annulée.');
    }
}