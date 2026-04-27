<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Commande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PanierController extends Controller
{
    public function index()
    {
        $panier = session()->get('panier', []);

        $menus = Menu::whereIn('id', array_keys($panier))
            ->with(['theme', 'regime'])
            ->get();

        $totalMenus = 0;

        foreach ($menus as $menu) {
            $quantite = $panier[$menu->id]['quantite'] ?? 1;
            $totalMenus += $menu->prix_par_personne * $quantite;
        }

        return view('client.panier.index', compact('menus', 'panier', 'totalMenus'));
    }

    public function add(Menu $menu)
    {
        $panier = session()->get('panier', []);

        if (isset($panier[$menu->id])) {
            $panier[$menu->id]['quantite']++;
        } else {
            $panier[$menu->id] = [
                'quantite' => 1,
            ];
        }

        session()->put('panier', $panier);

        return redirect()->back()->with('ok', 'Menu ajouté au panier.');
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'quantite' => 'required|integer|min:1',
        ]);

        $panier = session()->get('panier', []);

        if (isset($panier[$menu->id])) {
            $panier[$menu->id]['quantite'] = $validated['quantite'];
            session()->put('panier', $panier);
        }

        return redirect()->route('panier.index')->with('ok', 'Panier mis à jour.');
    }

    public function remove(Menu $menu)
    {
        $panier = session()->get('panier', []);

        unset($panier[$menu->id]);

        session()->put('panier', $panier);

        return redirect()->route('panier.index')->with('ok', 'Menu retiré du panier.');
    }

    public function checkoutForm()
    {
        $panier = session()->get('panier', []);

        if (empty($panier)) {
            return redirect()->route('panier.index')->with('ok', 'Votre panier est vide.');
        }

        $menus = Menu::whereIn('id', array_keys($panier))
            ->with(['theme', 'regime'])
            ->get();

        $user = auth()->user();

        $nombrePersonneMinimum = max($menus->pluck('nombre_personne_minimum')->toArray());
        $prixLivraisonDefaut = $this->calculPrixLivraison($user);

        return view('client.panier.checkout', compact(
            'menus',
            'panier',
            'user',
            'nombrePersonneMinimum',
            'prixLivraisonDefaut'
        ));
    }

    public function checkout(Request $request)
    {
        $panier = session()->get('panier', []);

        if (empty($panier)) {
            return redirect()->route('panier.index')->with('ok', 'Votre panier est vide.');
        }

        $menus = Menu::whereIn('id', array_keys($panier))->get();

        if ($menus->isEmpty()) {
            return redirect()->route('panier.index')->withErrors([
                'panier' => 'Aucun menu valide dans le panier.',
            ]);
        }

        $nombrePersonneMinimum = max($menus->pluck('nombre_personne_minimum')->toArray());

        $validated = $request->validate([
            'date_prestation' => 'required|date|after_or_equal:today',
            'heure_livraison' => 'required|date_format:H:i',
            'nombre_personne' => 'required|integer|min:' . $nombrePersonneMinimum,
            'pret_materiel' => 'nullable|boolean',
            'restitution_materiel' => 'nullable|boolean',
        ], [
            'nombre_personne.min' => "Le nombre de personnes doit être d'au moins {$nombrePersonneMinimum}.",
        ]);

        $user = auth()->user();
        $prixLivraison = $this->calculPrixLivraison($user);

        $syncData = [];
        $totalMenus = 0;

        foreach ($menus as $menu) {
            $quantite = $panier[$menu->id]['quantite'] ?? 1;

            $prixUnitaire = $menu->prix_par_personne * $validated['nombre_personne'];

            if ($validated['nombre_personne'] >= ($menu->nombre_personne_minimum + 5)) {
                $prixUnitaire = round($prixUnitaire * 0.90, 2);
            }

            $prixTotal = $prixUnitaire * $quantite;
            $totalMenus += $prixTotal;

            $syncData[$menu->id] = [
                'quantite' => $quantite,
                'prix_unitaire' => $prixUnitaire,
                'prix_total' => $prixTotal,
            ];
        }

        DB::transaction(function () use ($validated, $user, $prixLivraison, $totalMenus, $menus, $panier, $syncData) {
            $commande = Commande::create([
                'user_id' => $user->id,
                'date_commande' => Carbon::today(),
                'date_prestation' => $validated['date_prestation'],
                'heure_livraison' => $validated['heure_livraison'],
                'prix_menu' => $totalMenus,
                'nombre_personne' => $validated['nombre_personne'],
                'prix_livraison' => $prixLivraison,
                'statut' => 'En attente',
                'pret_materiel' => request()->boolean('pret_materiel'),
                'restitution_materiel' => request()->boolean('restitution_materiel'),
            ]);

            $commande->menus()->sync($syncData);

            app(\App\Services\StatsExportService::class)->exportCommande($commande);

            $commande = Commande::create([
                'user_id' => $user->id,
                'date_commande' => Carbon::today(),
                'date_prestation' => $validated['date_prestation'],
                'heure_livraison' => $validated['heure_livraison'],
                'prix_menu' => $totalMenus,
                'nombre_personne' => $validated['nombre_personne'],
                'prix_livraison' => $prixLivraison,
                'statut' => 'En attente',
                'pret_materiel' => request()->boolean('pret_materiel'),
                'restitution_materiel' => request()->boolean('restitution_materiel'),
            ]);

            $commande->statuts()->create([
                'statut' => 'En attente',
            ]);

            foreach ($menus as $menu) {
                $quantite = $panier[$menu->id]['quantite'] ?? 1;

                if ($menu->quantite_restante >= $quantite) {
                    $menu->decrement('quantite_restante', $quantite);
                }
            }

            session()->forget('panier');
            session()->flash('commande_id', $commande->id);
        });

        return redirect()
            ->route('client.commandes.show', session('commande_id'))
            ->with('ok', 'Votre commande a bien été créée.');
    }

    private function calculPrixLivraison($user): float
    {
        return mb_strtolower(trim((string) $user->ville)) === 'bordeaux' ? 0 : 5;
    }
}