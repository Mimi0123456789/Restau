<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Horaire;
use App\Models\Allergene;
use App\Models\Regime;
use App\Models\Theme;
use Illuminate\Http\Request;

class ParametresController extends Controller
{
    public function index()
    {
        $horaires = Horaire::orderByRaw("
            FIELD(jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche')
        ")->get();

        $allergenes = Allergene::orderBy('libelle')->get();
        $regimes = Regime::orderBy('libelle')->get();
        $themes = Theme::orderBy('libelle')->get();

        return view('dashboard.admin.parametres', compact(
            'horaires',
            'allergenes',
            'regimes',
            'themes'
        ));
    }

    public function storeHoraire(Request $request)
    {
        $request->validate([
            'jour' => 'required|string|max:50',
            'heure_ouverture' => 'required',
            'heure_fermeture' => 'required',
        ]);

        Horaire::create([
            'jour' => $request->jour,
            'heure_ouverture' => $request->heure_ouverture,
            'heure_fermeture' => $request->heure_fermeture,
        ]);

        return redirect()
            ->route('admin.parametres.index')
            ->with('ok', 'Horaire ajouté avec succès.');
    }

    public function updateHoraire(Request $request, Horaire $horaire)
    {
        $request->validate([
            'jour' => 'required|string|max:50',
            'heure_ouverture' => 'required',
            'heure_fermeture' => 'required',
        ]);

        $horaire->update([
            'jour' => $request->jour,
            'heure_ouverture' => $request->heure_ouverture,
            'heure_fermeture' => $request->heure_fermeture,
        ]);

        return redirect()
            ->route('admin.parametres.index')
            ->with('ok', 'Horaire modifié avec succès.');
    }

    public function destroyHoraire(Horaire $horaire)
    {
        $horaire->delete();

        return redirect()
            ->route('admin.parametres.index')
            ->with('ok', 'Horaire supprimé avec succès.');
    }

    public function storeAllergene(Request $request)
    {
        $request->validate([
            'libelle' => 'required|string|max:255|unique:allergenes,libelle',
        ]);

        Allergene::create([
            'libelle' => $request->libelle,
        ]);

        return redirect()
            ->route('admin.parametres.index')
            ->with('ok', 'Allergène ajouté avec succès.');
    }

    public function updateAllergene(Request $request, Allergene $allergene)
    {
        $request->validate([
            'libelle' => 'required|string|max:255|unique:allergenes,libelle,' . $allergene->id,
        ]);

        $allergene->update([
            'libelle' => $request->libelle,
        ]);

        return redirect()
            ->route('admin.parametres.index')
            ->with('ok', 'Allergène modifié avec succès.');
    }

    public function destroyAllergene(Allergene $allergene)
    {
        $allergene->delete();

        return redirect()
            ->route('admin.parametres.index')
            ->with('ok', 'Allergène supprimé avec succès.');
    }

    public function storeRegime(Request $request)
    {
        $request->validate([
            'libelle' => 'required|string|max:255|unique:regimes,libelle',
        ]);

        Regime::create([
            'libelle' => $request->libelle,
        ]);

        return redirect()
            ->route('admin.parametres.index')
            ->with('ok', 'Régime ajouté avec succès.');
    }

    public function updateRegime(Request $request, Regime $regime)
    {
        $request->validate([
            'libelle' => 'required|string|max:255|unique:regimes,libelle,' . $regime->id,
        ]);

        $regime->update([
            'libelle' => $request->libelle,
        ]);

        return redirect()
            ->route('admin.parametres.index')
            ->with('ok', 'Régime modifié avec succès.');
    }

    public function destroyRegime(Regime $regime)
    {
        $regime->delete();

        return redirect()
            ->route('admin.parametres.index')
            ->with('ok', 'Régime supprimé avec succès.');
    }

    public function storeTheme(Request $request)
    {
        $request->validate([
            'libelle' => 'required|string|max:255|unique:themes,libelle',
        ]);

        Theme::create([
            'libelle' => $request->libelle,
        ]);

        return redirect()
            ->route('admin.parametres.index')
            ->with('ok', 'Thème ajouté avec succès.');
    }

    public function updateTheme(Request $request, Theme $theme)
    {
        $request->validate([
            'libelle' => 'required|string|max:255|unique:themes,libelle,' . $theme->id,
        ]);

        $theme->update([
            'libelle' => $request->libelle,
        ]);

        return redirect()
            ->route('admin.parametres.index')
            ->with('ok', 'Thème modifié avec succès.');
    }

    public function destroyTheme(Theme $theme)
    {
        $theme->delete();

        return redirect()
            ->route('admin.parametres.index')
            ->with('ok', 'Thème supprimé avec succès.');
    }
}