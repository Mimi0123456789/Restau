@extends('layouts.app')

@section('title', 'Modifier ma commande')

@section('content')
    <div class="max-w-3xl mx-auto px-6 py-12">
        <div class="mb-4">
            <a href="{{ route('client.commandes.show', $commande) }}" class="btn btn-outline-secondary">
                Retour à la commande
            </a>
        </div>

        <h1 class="text-3xl font-bold mb-6">Modifier la commande #{{ $commande->id }}</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="m-0 ps-3">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow p-6">
            <form method="POST" action="{{ route('client.commandes.update', $commande) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Date de prestation</label>
                    <input
                        type="date"
                        name="date_prestation"
                        class="form-control"
                        value="{{ old('date_prestation', optional($commande->date_prestation)->format('Y-m-d')) }}"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Heure de livraison</label>
                    <input
                        type="time"
                        name="heure_livraison"
                        class="form-control"
                        value="{{ old('heure_livraison', $commande->heure_livraison) }}"
                        required
                    >
                </div>

                    <div class="mb-3">
                        <label class="form-label">Nombre de personnes</label>
                        <input
                            type="number"
                            name="quantites[{{ $menu->id }}]"
                            class="form-control"
                            value="{{ old('quantites.' . $menu->id, $menu->pivot->quantite) }}"
                            min="{{ $menu->nombre_personne_minimum }}"
                            required
                        >
                    </div>

                <div class="form-check mb-2">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="pret_materiel"
                        value="1"
                        id="pret_materiel"
                        {{ old('pret_materiel', $commande->pret_materiel) ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="pret_materiel">
                        Prêt de matériel
                    </label>
                </div>

                <div class="form-check mb-4">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="restitution_materiel"
                        value="1"
                        id="restitution_materiel"
                        {{ old('restitution_materiel', $commande->restitution_materiel) ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="restitution_materiel">
                        Matériel restitué
                    </label>
                </div>

                <div class="alert alert-info">
                    Les quantités peuvent être ajustées sans modifier la liste des menus commandés.
                </div>

                <button class="btn btn-primary">Enregistrer les modifications</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const totalInput = document.getElementById('nombre_personne');
            if (!totalInput) return;

            const updateTotal = () => {
                const inputs = document.querySelectorAll('input[name^="quantites["]');
                let total = 0;

                inputs.forEach(input => {
                    const value = parseInt(input.value || 0, 10);
                    if (!Number.isNaN(value)) {
                        total += value;
                    }
                });

                totalInput.value = total;
                const hidden = document.querySelector('input[name="nombre_personne"]');
                if (hidden) hidden.value = total;
            };

            document.querySelectorAll('input[name^="quantites["]').forEach(input => {
                input.addEventListener('input', updateTotal);
            });

            updateTotal();
        });
    </script>
@endsection