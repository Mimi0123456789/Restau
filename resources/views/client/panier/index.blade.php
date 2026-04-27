@extends('layouts.app')

@section('title', 'Mon panier')

@section('content')
    <div class="max-w-5xl mx-auto px-6 py-12">
        <h1 class="text-3xl font-bold mb-6">Mon panier</h1>

        @if(session('ok'))
            <div class="alert alert-success">{{ session('ok') }}</div>
        @endif

        @if($menus->isEmpty())
            <div class="bg-white rounded-xl shadow p-6 text-gray-600">
                Votre panier est vide.
            </div>
        @else
            <div class="bg-white rounded-xl shadow p-6 mb-6">
                <div class="space-y-4">
                    @foreach($menus as $menu)
                        <div class="border rounded-lg p-4 flex justify-between items-center gap-4 flex-wrap">
                            <div>
                                <div class="font-semibold">{{ $menu->titre }}</div>
                                <div class="text-sm text-gray-600">
                                    {{ number_format($menu->prix_par_personne, 2, ',', ' ') }} € / pers.
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('panier.update', $menu) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number"
                                           name="quantite"
                                           value="{{ $panier[$menu->id]['quantite'] ?? 1 }}"
                                           min="1"
                                           class="form-control"
                                           style="width:90px;">
                                    <button class="btn btn-outline-secondary btn-sm">Maj</button>
                                </form>

                                <form method="POST" action="{{ route('panier.remove', $menu) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm">Retirer</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 text-end">
                    <p class="text-lg font-semibold">
                        Total menus : {{ number_format($totalMenus, 2, ',', ' ') }} €
                    </p>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('panier.checkout.form') }}" class="btn btn-primary">
                    Continuer la commande
                </a>
            </div>
        @endif
    </div>
@endsection