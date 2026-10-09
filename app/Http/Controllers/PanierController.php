<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Commande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Mail\ConfirmationCommandeMail;
use Illuminate\Support\Facades\Mail;

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
        if ($menu->quantite_restante <= 0) {
            return redirect()->back()->withErrors([
                'panier' => 'Ce menu n\'est plus disponible.',
            ]);
        }

        $panier = session()->get('panier', []);
        $nouvelleQuantite = ($panier[$menu->id]['quantite'] ?? 0) + 1;

        if ($nouvelleQuantite > $menu->quantite_restante) {
            return redirect()->back()->withErrors([
                'panier' => "Il ne reste que {$menu->quantite_restante} exemplaire(s) de ce menu.",
            ]);
        }

        if (isset($panier[$menu->id])) {
            $panier[$menu->id]['quantite'] = $nouvelleQuantite;
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

        if ($validated['quantite'] < $menu->nombre_personne_minimum) {
            return redirect()->route('panier.index')->withErrors([
                'panier' => "La quantité doit être d'au moins {$menu->nombre_personne_minimum} pour ce menu.",
            ]);
        }

        if ($validated['quantite'] > $menu->quantite_restante) {
            return redirect()->route('panier.index')->withErrors([
                'panier' => "Il ne reste que {$menu->quantite_restante} exemplaire(s) de ce menu.",
            ]);
        }

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

        foreach ($menus as $menu) {
            $quantite = $panier[$menu->id]['quantite'] ?? 1;

            if ($quantite < $menu->nombre_personne_minimum) {
                return redirect()->route('panier.index')->withErrors([
                    'panier' => "La quantité du menu \"{$menu->titre}\" doit être d'au moins {$menu->nombre_personne_minimum}.",
                ]);
            }

            if ($menu->quantite_restante < $quantite) {
                return redirect()->route('panier.index')->withErrors([
                    'panier' => "Le menu \"{$menu->titre}\" n'est plus disponible en quantité suffisante. Il reste {$menu->quantite_restante} exemplaire(s).",
                ]);
            }
        }

        $nombrePersonneMinimum = max($menus->pluck('nombre_personne_minimum')->toArray());
        $totalQuantitePanier = array_sum(array_map(fn ($item) => (int) ($item['quantite'] ?? 0), $panier));

        $validated = $request->validate([
            'date_prestation' => 'required|date|after_or_equal:today',
            'heure_livraison' => 'required|date_format:H:i',
            'nombre_personne' => 'required|integer|min:1',
            'pret_materiel' => 'nullable|boolean',
            'restitution_materiel' => 'nullable|boolean',
        ]);

        $validated['nombre_personne'] = $totalQuantitePanier;

        $user = auth()->user();
        $prixLivraison = $this->calculPrixLivraison($user);

        $syncData = [];
        $totalMenus = 0;

        foreach ($menus as $menu) {
            $quantite = $panier[$menu->id]['quantite'] ?? 1;

            $prixUnitaire = round($menu->prix_par_personne, 2);

            if ($totalQuantitePanier >= ($menu->nombre_personne_minimum + 5)) {
                $prixUnitaire = round($prixUnitaire * 0.90, 2);
            }

            $prixTotal = round($prixUnitaire * $quantite, 2);
            $totalMenus = round($totalMenus + $prixTotal, 2);

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
                'pret_materiel' => $validated['pret_materiel'] ?? false,
                'restitution_materiel' => $validated['restitution_materiel'] ?? false,
            ]);

            $commande->menus()->sync($syncData);

            $commande->statuts()->create([
                'statut' => 'En attente',
            ]);

            foreach ($menus as $menu) {
                $quantite = $panier[$menu->id]['quantite'] ?? 1;

                if ($menu->quantite_restante >= $quantite) {
                    $menu->decrement('quantite_restante', $quantite);
                }
            }

            $commande->load(['user', 'menus']);

            Mail::to($commande->user->email)
                ->send(new ConfirmationCommandeMail($commande, 'creation'));

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