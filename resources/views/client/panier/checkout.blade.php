@extends('layouts.app')

@section('title', 'Validation de commande')

@section('content')
    <div class="max-w-5xl mx-auto px-6 py-12">
        <div class="mb-4">
            <a href="{{ route('panier.index') }}" class="btn btn-outline-secondary">
                Retour au panier
            </a>
        </div>

        <h1 class="text-3xl font-bold mb-6">Validation de la commande</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="m-0 ps-3">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Récapitulatif</h2>

                <div class="space-y-3 mb-4">
                    @foreach($menus as $menu)
                        <div class="border rounded-lg p-3 menu-line"
                             data-price="{{ $menu->prix_par_personne }}"
                             data-qty="{{ $panier[$menu->id]['quantite'] ?? 1 }}"
                             data-min="{{ $menu->nombre_personne_minimum }}">
                            <div class="font-semibold">{{ $menu->titre }}</div>
                            <div class="text-sm text-gray-600">
                                Quantité : {{ $panier[$menu->id]['quantite'] ?? 1 }}
                            </div>
                            <div class="text-sm text-gray-600">
                                Minimum menu : {{ $menu->nombre_personne_minimum }} personne(s)
                            </div>
                            <div class="text-sm text-gray-600">
                                Prix par personne : {{ number_format($menu->prix_par_personne, 2, ',', ' ') }} €
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="border-top pt-3">
                    <div><strong>Nombre minimum requis :</strong> {{ $nombrePersonneMinimum }}</div>
                    <div><strong>Prix livraison :</strong> <span id="prix-livraison-display">{{ number_format($prixLivraisonDefaut, 2, ',', ' ') }}</span> €</div>
                    <div class="text-end font-semibold mt-3">
                        Total estimé : <span id="total-estime">0,00</span> €
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Informations de prestation</h2>

                <div class="mb-4">
                    <h3 class="h6">Informations client</h3>
                    <p class="mb-1"><strong>Nom :</strong> {{ $user->prenom }} {{ $user->nom }}</p>
                    <p class="mb-1"><strong>Email :</strong> {{ $user->email }}</p>
                    <p class="mb-1"><strong>Téléphone :</strong> {{ $user->telephone ?: 'Non renseigné' }}</p>
                    <p class="mb-0">
                        <strong>Adresse :</strong>
                        {{ $user->adresse_postale ?: 'Non renseignée' }},
                        {{ $user->code_postal ?: '' }}
                        {{ $user->ville ?: '' }}
                    </p>
                    <small class="text-muted">
                        Ces informations proviennent de votre profil.
                    </small>
                </div>

                <form method="POST" action="{{ route('panier.checkout') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Date de prestation</label>
                        <input
                            type="date"
                            name="date_prestation"
                            class="form-control"
                            value="{{ old('date_prestation') }}"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Heure de livraison</label>
                        <input
                            type="time"
                            name="heure_livraison"
                            class="form-control"
                            value="{{ old('heure_livraison') }}"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nombre de personnes</label>
                        <input
                            type="number"
                            id="nombre_personne"
                            name="nombre_personne"
                            class="form-control"
                            value="{{ old('nombre_personne', $nombrePersonneMinimum) }}"
                            min="{{ $nombrePersonneMinimum }}"
                            required
                        >
                        <small class="text-muted">
                            Minimum requis : {{ $nombrePersonneMinimum }} personne(s)
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Prix de livraison</label>
                        <input
                            type="text"
                            class="form-control"
                            value="{{ number_format($prixLivraisonDefaut, 2, ',', ' ') }} €"
                            readonly
                        >
                    </div>

                    <div class="form-check mb-2">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="pret_materiel"
                            value="1"
                            id="pret_materiel"
                            {{ old('pret_materiel') ? 'checked' : '' }}
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
                            {{ old('restitution_materiel') ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="restitution_materiel">
                            Matériel restitué
                        </label>
                    </div>

                    <button class="btn btn-primary">Valider ma commande</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function calculerTotal() {
        const nombrePersonne = parseInt(document.getElementById('nombre_personne').value || 0, 10);
        const lignes = document.querySelectorAll('.menu-line');
        const prixLivraison = {{ json_encode($prixLivraisonDefaut) }};
        let total = 0;

        lignes.forEach(ligne => {
            const prixParPersonne = parseFloat(ligne.dataset.price || 0);
            const quantite = parseInt(ligne.dataset.qty || 1, 10);
            const minimum = parseInt(ligne.dataset.min || 1, 10);

            let prixUnitaire = prixParPersonne * nombrePersonne;

            if (nombrePersonne >= (minimum + 5)) {
                prixUnitaire = prixUnitaire * 0.9;
            }

            total += prixUnitaire * quantite;
        });

        total += prixLivraison;

        document.getElementById('total-estime').textContent =
            total.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('nombre_personne');
        if (input) {
            input.addEventListener('input', calculerTotal);
        }
        calculerTotal();
    });
</script>
@endpush