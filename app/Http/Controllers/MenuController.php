<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Plat;
use App\Models\Regime;
use App\Models\Allergene;
use App\Models\Theme;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with(['theme', 'regime', 'plats.allergenes'])
            ->orderBy('titre')
            ->paginate(10);

        $themes = Theme::orderBy('libelle')->get();

        $regimes = Regime::orderBy('libelle')->get();

        $plats = Plat::with('allergenes')
            ->orderBy('titre_plat')
            ->get();

        $allergenes = Allergene::orderBy('libelle')->get();

        return view('menus.index', compact(
            'menus',
            'themes',
            'regimes',
            'plats',
            'allergenes'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:50',
            'nombre_personne_minimum' => 'required|integer|min:1',
            'prix_par_personne' => 'required|numeric|min:0',
            'theme_id' => 'required|exists:themes,id',
            'regime_id' => 'required|exists:regimes,id',
            'description' => 'required|string',
            'quantite_restante' => 'required|integer|min:0',
            'plats' => 'nullable|array',
            'plats.*' => 'exists:plats,id',
        ]);

        $menu = Menu::create([
            'titre' => $validated['titre'],
            'nombre_personne_minimum' => $validated['nombre_personne_minimum'],
            'prix_par_personne' => $validated['prix_par_personne'],
            'theme_id' => $validated['theme_id'],
            'regime_id' => $validated['regime_id'],
            'description' => $validated['description'],
            'quantite_restante' => $validated['quantite_restante'],
        ]);

        $menu->plats()->sync($validated['plats'] ?? []);

        return redirect()
            ->route('menus.index')
            ->with('ok', 'Menu créé avec succès.');
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:50',
            'nombre_personne_minimum' => 'required|integer|min:1',
            'prix_par_personne' => 'required|numeric|min:0',
            'theme_id' => 'required|exists:themes,id',
            'regime_id' => 'required|exists:regimes,id',
            'description' => 'required|string',
            'quantite_restante' => 'required|integer|min:0',
            'plats' => 'nullable|array',
            'plats.*' => 'exists:plats,id',
        ]);

        $menu->update([
            'titre' => $validated['titre'],
            'nombre_personne_minimum' => $validated['nombre_personne_minimum'],
            'prix_par_personne' => $validated['prix_par_personne'],
            'theme_id' => $validated['theme_id'],
            'regime_id' => $validated['regime_id'],
            'description' => $validated['description'],
            'quantite_restante' => $validated['quantite_restante'],
        ]);

        $menu->plats()->sync($validated['plats'] ?? []);

        return redirect()
            ->route('menus.index')
            ->with('ok', 'Menu modifié avec succès.');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();

        return redirect()
            ->route('menus.index')
            ->with('ok', 'Menu supprimé avec succès.');
    }
}