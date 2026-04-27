@extends('layouts.app')

@section('title', 'Mes commandes')

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-12">
        <div class="mb-4">
            <a href="{{ route('dashboard.client') }}" class="btn btn-outline-secondary">
                Retour à mon espace
            </a>
        </div>

        <h1 class="text-3xl font-bold mb-6">Mes commandes</h1>

        @if($commandes->isEmpty())
            <div class="bg-white rounded-xl shadow p-6 text-gray-600">
                Vous n’avez encore passé aucune commande.
            </div>
        @else
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <table class="table mb-0 align-middle">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Date commande</th>
                        <th>Date prestation</th>
                        <th>Statut</th>
                        <th class="text-end">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($commandes as $commande)
                        <tr>
                            <td>{{ $commande->id }}</td>
                            <td>{{ \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($commande->date_prestation)->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $commande->statut }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('client.commandes.show', $commande) }}" class="btn btn-sm btn-outline-primary">
                                    Voir
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div class="p-3">
                    {{ $commandes->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection