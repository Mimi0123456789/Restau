<?php

namespace App\Http\Controllers;

use App\Models\Plat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlatController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre_plat' => 'required|string|max:50',
            'photo' => 'nullable|image|max:2048',
            'allergenes' => 'nullable|array',
            'allergenes.*' => 'exists:allergenes,id',
        ]);

        $photoPath = null;

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('plats', 'public');
        }

        $plat = Plat::create([
            'titre_plat' => $validated['titre_plat'],
            'photo' => $photoPath,
        ]);

        $plat->allergenes()->sync($validated['allergenes'] ?? []);

        return redirect()
            ->route('menus.index')
            ->with('ok', 'Plat créé avec succès.');
    }

    public function update(Request $request, Plat $plat)
    {
        $validated = $request->validate([
            'titre_plat' => 'required|string|max:50',
            'photo' => 'nullable|image|max:2048',
            'allergenes' => 'nullable|array',
            'allergenes.*' => 'exists:allergenes,id',
        ]);

        $data = [
            'titre_plat' => $validated['titre_plat'],
        ];

        if ($request->hasFile('photo')) {
            if ($plat->photo && Storage::disk('public')->exists($plat->photo)) {
                Storage::disk('public')->delete($plat->photo);
            }

            $data['photo'] = $request->file('photo')->store('plats', 'public');
        }

        $plat->update($data);

        $plat->allergenes()->sync($validated['allergenes'] ?? []);

        return redirect()
            ->route('menus.index')
            ->with('ok', 'Plat modifié avec succès.');
    }

    public function destroy(Plat $plat)
    {
        if ($plat->photo && Storage::disk('public')->exists($plat->photo)) {
            Storage::disk('public')->delete($plat->photo);
        }

        $plat->delete();

        return redirect()
            ->route('menus.index')
            ->with('ok', 'Plat supprimé avec succès.');
    }
}