<?php

namespace App\Http\Controllers;

use App\Models\Avis;
use App\Models\Menu;
use App\Models\Regime;
use App\Models\Theme;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $menus = Menu::with(['theme', 'regime', 'plats'])
            ->where('quantite_restante', '>', 0)
            ->whereColumn('quantite_restante', '>=', 'nombre_personne_minimum')
            ->when($request->filled('theme_id'), function ($query) use ($request) {
                $query->where('theme_id', $request->theme_id);
            })
            ->when($request->filled('regime_id'), function ($query) use ($request) {
                $query->where('regime_id', $request->regime_id);
            })
            ->when($request->filled('prix_max'), function ($query) use ($request) {
                $query->where('prix_par_personne', '<=', $request->prix_max);
            })
            ->latest()
            ->get();

        $themes = Theme::orderBy('libelle')->get();
        $regimes = Regime::orderBy('libelle')->get();

        $avis = Avis::with(['commande.user'])
            ->where('statut', 'valide')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        return view('home', compact('menus', 'themes', 'regimes', 'avis'));
    }
}