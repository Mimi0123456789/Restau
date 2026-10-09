@extends('layouts.app')

@section('title', 'Détail commande')

@section('content')
    @php
        $prixMenusTotal = $commande->menus->sum(fn ($menu) => (float) ($menu->pivot->prix_total ?? 0));
        $prixUnitaireMenu = $commande->menus->isNotEmpty()
            ? (float) ($commande->menus->first()->prix_par_personne ?? 0)
            : 0;
    @endphp

    <div class="max-w-5xl mx-auto px-6 py-12">
        <div class="mb-4">
            <a href="{{ route('client.commandes.index') }}" class="btn btn-outline-secondary">
                Retour aux commandes
            </a>
        </div>

        <h1 class="text-3xl font-bold mb-6">Commande #{{ $commande->id }}</h1>

        @if(session('ok'))
            <div class="alert alert-success">{{ session('ok') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="m-0 ps-3">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-semibold mb-3">Informations</h2>
                <p><strong>Date commande :</strong> {{ $commande->date_commande?->format('d/m/Y') }}</p>
                <p><strong>Date prestation :</strong> {{ $commande->date_prestation?->format('d/m/Y') }}</p>
                <p><strong>Heure livraison :</strong> {{ $commande->heure_livraison }}</p>
                <p><strong>Nombre de personnes :</strong> {{ $commande->nombre_personne }}</p>
                <p><strong>Prix du menu :</strong> {{ number_format($prixUnitaireMenu, 2, ',', ' ') }} €</p>
                <p><strong>Prix livraison :</strong> {{ number_format($commande->prix_livraison, 2, ',', ' ') }} €</p>
                <p><strong>Total :</strong> {{ number_format($prixMenusTotal + $commande->prix_livraison, 2, ',', ' ') }} €</p>
                <p><strong>Prêt matériel :</strong> {{ $commande->pret_materiel ? 'Oui' : 'Non' }}</p>
                <p><strong>Matériel restitué :</strong> {{ $commande->restitution_materiel ? 'Oui' : 'Non' }}</p>
                <p><strong>Statut :</strong> {{ $commande->statut }}</p>

                @if($commande->statut === 'En attente')
                    <div class="mt-4 d-flex gap-2 flex-wrap">
                        <a href="{{ route('client.commandes.edit', $commande) }}" class="btn btn-outline-primary">
                            Modifier la commande
                        </a>

                        <form method="POST"
                              action="{{ route('client.commandes.cancel', $commande) }}"
                              onsubmit="return confirm('Voulez-vous vraiment annuler cette commande ?')">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-outline-danger">
                                Annuler la commande
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-semibold mb-3">Historique du suivi</h2>

                @if($commande->statuts->isNotEmpty())
                    <ul class="list-group">
                        @foreach($commande->statuts as $historique)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ $historique->statut }}</span>
                                <span class="text-muted">
                                    {{ $historique->created_at?->format('d/m/Y H:i') }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted mb-0">Aucun historique disponible.</p>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Menus commandés</h2>

            @if($commande->menus->isNotEmpty())
                <div class="space-y-3">
                    @foreach($commande->menus as $menu)
                        <div class="border rounded-lg p-4">
                            <div class="font-semibold">{{ $menu->titre }}</div>
                            <div class="text-sm text-gray-600">{{ $menu->description }}</div>
                            <div class="text-sm text-gray-600 mt-1">
                                Quantité : {{ $menu->pivot->quantite }} |
                                Prix unitaire :
                                {{ number_format($menu->prix_par_personne, 2, ',', ' ') }} € |
                                Total :
                                {{ number_format($menu->pivot->prix_total, 2, ',', ' ') }} €
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted mb-0">Aucun menu associé à cette commande.</p>
            @endif
        </div>

        @if(in_array($commande->statut, ['Livré', 'Terminée']))
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Laisser un avis</h2>

                <form method="POST" action="{{ route('client.commandes.avis.store', $commande) }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <select name="note" class="form-select" required>
                            <option value="">Choisir une note</option>
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ old('note', optional($commande->avis)->note) == $i ? 'selected' : '' }}>
                                    {{ $i }}/5
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Commentaire</label>
                        <textarea name="commentaire" class="form-control" rows="4">{{ old('commentaire', optional($commande->avis)->commentaire) }}</textarea>
                    </div>

                    <button class="btn btn-primary">Envoyer mon avis</button>
                </form>
            </div>
        @endif
    </div>
@endsection