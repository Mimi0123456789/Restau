@extends('layouts.app')

@section('content')
    @php
        $prixMenusTotal = $commande->menus->sum(fn ($menu) => (float) ($menu->pivot->prix_total ?? 0));
    @endphp

    <div class="container pt-24">
        <div class="mb-4">
            <a href="{{ route('commandes.index') }}" class="btn btn-outline-secondary">
                Retour aux commandes
            </a>
        </div>

        <h1 class="h3 mb-4">Commande #{{ $commande->id }}</h1>

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

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header fw-bold">Informations client</div>
                    <div class="card-body">
                        <p><strong>Nom :</strong> {{ $commande->user?->prenom }} {{ $commande->user?->nom }}</p>
                        <p><strong>Email :</strong> {{ $commande->user?->email }}</p>
                        <p><strong>Téléphone :</strong> {{ $commande->user?->telephone ?: 'Non renseigné' }}</p>
                        <p><strong>Dernière mise à jour :</strong> {{ $commande->updated_at?->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header fw-bold">Informations commande</div>
                    <div class="card-body">
                        <p><strong>Date commande :</strong> {{ $commande->date_commande?->format('d/m/Y') }}</p>
                        <p><strong>Date prestation :</strong> {{ $commande->date_prestation?->format('d/m/Y') }}</p>
                        <p><strong>Heure livraison :</strong> {{ $commande->heure_livraison }}</p>
                        <p disabled><strong>Nombre de personnes :</strong> {{ $commande->nombre_personne }}</p>
                        <p><strong>Prix menus :</strong> {{ number_format($prixMenusTotal, 2, ',', ' ') }} €</p>
                        <p><strong>Prix livraison :</strong> {{ number_format($commande->prix_livraison, 2, ',', ' ') }} €</p>
                        <p><strong>Total :</strong> {{ number_format($prixMenusTotal + $commande->prix_livraison, 2, ',', ' ') }} €</p>
                        <p><strong>Prêt matériel :</strong> {{ $commande->pret_materiel ? 'Oui' : 'Non' }}</p>
                        <p><strong>Matériel restitué :</strong> {{ $commande->restitution_materiel ? 'Oui' : 'Non' }}</p>
                        <p><strong>Statut actuel :</strong> {{ $commande->statut }}</p>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header fw-bold">Menus commandés</div>
                    <div class="card-body">
                        @forelse($commande->menus as $menu)
                            <div class="border rounded p-3 mb-3">
                                <div class="fw-bold">{{ $menu->titre }}</div>
                                <div>{{ $menu->description }}</div>
                                <div>Quantité : {{ $menu->pivot->quantite }}</div>
                                <div>Prix unitaire : {{ number_format($menu->pivot->prix_unitaire, 2, ',', ' ') }} €</div>
                                <div>Total : {{ number_format($menu->pivot->prix_total, 2, ',', ' ') }} €</div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Aucun menu associé à cette commande.</p>
                        @endforelse
                    </div>
                </div>

                @if($commande->avis)
                    <div class="card">
                        <div class="card-header fw-bold">Avis client</div>
                        <div class="card-body">
                            <p><strong>Note :</strong> {{ $commande->avis->note }}/5</p>
                            <p><strong>Description :</strong> {{ $commande->avis->commentaire ?: '—' }}</p>
                            <p><strong>Statut avis :</strong> {{ $commande->avis->statut }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header fw-bold">Mise à jour du statut</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('commandes.statut', $commande) }}">
                            @csrf
                            @method('PATCH')

                            <div class="mb-3">
                                <label class="form-label">Statut</label>
                                <select name="statut" class="form-select">
                                    @foreach($statuts as $statut)
                                        <option value="{{ $statut }}" @selected($commande->statut === $statut)>
                                            {{ $statut }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button class="btn btn-primary w-100">Enregistrer</button>
                        </form>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header fw-bold">Historique des statuts</div>
                    <div class="card-body">
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
            </div>
        </div>
    </div>
@endsection