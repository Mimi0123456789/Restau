@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-6 pt-24 pb-12">
        <div class="mb-4">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>
                Retour au tableau de bord
            </a>
        </div>

        <h1 class="text-3xl font-bold mb-3">📊 Comptabilité & Statistiques</h1>
        <p class="text-gray-600 mb-8">
            Visualisation du nombre de commandes par menu, comparaison des menus et suivi du chiffre d’affaires.
        </p>

        @if(session('ok'))
            <div class="alert alert-success">{{ session('ok') }}</div>
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.statistiques') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="menu_id" class="form-label">Filtrer par menu</label>
                        <select name="menu_id" id="menu_id" class="form-select">
                            <option value="">Tous les menus</option>
                            @foreach($menus as $menu)
                                <option value="{{ $menu->id }}" @selected((string)$menuId === (string)$menu->id)>
                                    {{ $menu->titre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="date_debut" class="form-label">Date début</label>
                        <input
                            type="date"
                            name="date_debut"
                            id="date_debut"
                            class="form-control"
                            value="{{ $dateDebut }}"
                        >
                    </div>

                    <div class="col-md-3">
                        <label for="date_fin" class="form-label">Date fin</label>
                        <input
                            type="date"
                            name="date_fin"
                            id="date_fin"
                            class="form-control"
                            value="{{ $dateFin }}"
                        >
                    </div>

                    <div class="col-md-2 d-grid">
                        <button class="btn btn-primary">Appliquer</button>
                    </div>

                    <div class="col-12">
                        <a href="{{ route('admin.statistiques') }}" class="btn btn-outline-secondary btn-sm">
                            Réinitialiser les filtres
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header fw-bold">
                        Nombre de commandes par menu
                    </div>
                    <div class="card-body p-0">
                        @if($grafanaUrl)
                        <p class="text-muted">
    URL Grafana : {{ $grafanaUrl }}
</p>
                            <iframe
                                src="{{ $grafanaUrl }}"
                                width="100%"
                                height="520"
                                frameborder="0"
                                class="rounded-bottom"
                            ></iframe>
                        @else
                            <div class="p-4 text-muted">
                                Grafana n’est pas encore configuré.  
                                Renseigne <code>GRAFANA_URL</code> et <code>GRAFANA_DASHBOARD_UID</code> dans le fichier <code>.env</code>.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection