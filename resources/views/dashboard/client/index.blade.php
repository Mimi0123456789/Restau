@extends('layouts.app')

@section('title', 'Mon Compte')

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-12">
        <h1 class="text-3xl font-bold mb-6">Mon Espace Client</h1>

        <p class="text-gray-600 mb-8">
            Bienvenue dans votre espace personnel.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('client.commandes.index') }}" class="block bg-white rounded-xl shadow p-6 hover:shadow-md transition">
                <div class="text-3xl mb-3">📦</div>
                <h2 class="text-xl font-semibold mb-2">Mes commandes</h2>
                <p class="text-gray-600 text-sm">Consultez l’historique de vos commandes.</p>
            </a>

            <a href="{{ route('client.commandes.index') }}" class="block bg-white rounded-xl shadow p-6 hover:shadow-md transition">
                <div class="text-3xl mb-3">🚚</div>
                <h2 class="text-xl font-semibold mb-2">Suivi des commandes</h2>
                <p class="text-gray-600 text-sm">Vérifiez l’état d’avancement de vos commandes.</p>
            </a>

            <a href="{{ route('panier.index') }}" class="block bg-white rounded-xl shadow p-6 hover:shadow-md transition">
                <div class="text-3xl mb-3">🛒</div>
                <h2 class="text-xl font-semibold mb-2">Mon panier</h2>
                <p class="text-gray-600 text-sm">Finalisez vos sélections de menus.</p>
            </a>
        </div>
    </div>
@endsection