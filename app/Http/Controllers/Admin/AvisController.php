<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Avis;
use Illuminate\Http\Request;

class AvisController extends Controller
{
    public function index()
    {
        $avis = Avis::with(['commande.user'])
            ->latest()
            ->paginate(10);

        return view('dashboard.admin.avis.index', compact('avis'));
    }

    public function update(Request $request, Avis $avi)
    {
        $validated = $request->validate([
            'statut' => ['required', 'in:en attente,valide,refuse'],
        ]);

        $avi->update([
            'statut' => $validated['statut'],
        ]);

        return redirect()
            ->route('avis.index')
            ->with('ok', 'Le statut de l’avis a bien été mis à jour.');
    }
}