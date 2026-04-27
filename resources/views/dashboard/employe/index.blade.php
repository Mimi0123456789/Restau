@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-6 pt-24 pb-12">
        <h1 class="text-3xl font-bold mb-4">Dashboard Employé</h1>

        <p class="text-gray-600 mb-10">
            Bienvenue dans l’espace employé.
        </p>

        <div class="row g-4">

            <div class="col-md-4">
                <a href="{{ route('admin.parametres.index') }}" class="text-decoration-none">
                    <div class="card shadow-sm dashboard-card h-100">
                        <div class="card-body text-center">
                            <div class="display-4 mb-3 text-secondary">🕒</div>
                            <h4 class="fw-bold">Horaires</h4>
                            <p class="text-muted">
                                Gestion des horaires
                            </p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="{{ route('menus.index') }}" class="text-decoration-none">
                    <div class="card shadow-sm dashboard-card h-100">
                        <div class="card-body text-center">
                            <div class="display-4 mb-3 text-secondary">📋</div>
                            <h4 class="fw-bold">Menus</h4>
                            <p class="text-muted">
                                Gestion des menus
                            </p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="{{ route('commandes.index') }}" class="text-decoration-none">
                    <div class="card shadow-sm dashboard-card h-100">
                        <div class="card-body text-center">
                            <div class="display-4 mb-3 text-secondary">🛒</div>
                            <h4 class="fw-bold">Commandes</h4>
                            <p class="text-muted">
                                Gestion des commandes
                            </p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="{{ route('avis.index') }}" class="text-decoration-none">
                    <div class="card shadow-sm dashboard-card h-100">
                        <div class="card-body text-center">
                            <div class="display-4 mb-3 text-info">💬</div>
                            <h4 class="fw-bold">Avis</h4>
                            <p class="text-muted">
                                Gestion des avis
                            </p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .dashboard-card {
            transition: all 0.2s ease-in-out;
            border-radius: 12px;
            cursor: pointer;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.15);
        }
    </style>
@endpush