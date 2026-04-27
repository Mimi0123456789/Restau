@extends('layouts.app')

@section('content')
    <div class="container pt-24">
        <div class="mb-4">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                Retour au tableau de bord
            </a>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 m-0">Gestion des commandes</h1>
        </div>

        @if(session('ok'))
            <div class="alert alert-success">{{ session('ok') }}</div>
        @endif

        <form method="GET" action="{{ route('commandes.index') }}" class="card mb-4">
            <div class="card-body row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Client</label>
                    <input type="text" name="client" class="form-control" value="{{ request('client') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous</option>
                        @foreach($statuts as $statut)
                            <option value="{{ $statut }}" @selected(request('statut') === $statut)>
                                {{ ucfirst($statut) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <button class="btn btn-primary">Filtrer</button>
                    <a href="{{ route('commandes.index') }}" class="btn btn-outline-secondary">Réinitialiser</a>
                </div>
            </div>
        </form>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Client</th>
                                <th>Date prestation</th>
                                <th>Personnes</th>
                                <th>Total</th>
                                <th>Statut</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($commandes as $commande)
                                <tr>
                                    <td>{{ $commande->id }}</td>
                                    <td>
                                        {{ $commande->user?->prenom }} {{ $commande->user?->nom }}<br>
                                        <small class="text-muted">{{ $commande->user?->email }}</small>
                                    </td>
                                    <td>{{ $commande->date_prestation?->format('d/m/Y') }}</td>
                                    <td>{{ $commande->nombre_personne }}</td>
                                    <td>{{ number_format($commande->prix_menu + $commande->prix_livraison, 2, ',', ' ') }} €</td>
                                    <td><span class="badge bg-secondary">{{ $commande->statut }}</span></td>
                                    <td class="text-end">
                                        <a href="{{ route('commandes.show', $commande) }}" class="btn btn-sm btn-outline-primary">
                                            Voir
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Aucune commande</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($commandes->hasPages())
                <div class="card-footer">
                    {{ $commandes->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection