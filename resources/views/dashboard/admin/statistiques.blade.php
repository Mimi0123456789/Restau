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
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header fw-bold">Commandes par menu</div>
                    <div class="card-body">
                        <div id="commandesParMenuChart"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header fw-bold">Chiffre d’affaires par menu</div>
                    <div class="card-body">
                        <div id="caParMenuChart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    const commandesParMenuLabels = @json($commandesParMenuLabels);
    const commandesParMenuData = @json($commandesParMenuData);

    const caParMenuLabels = @json($caParMenuLabels);
    const caParMenuData = @json($caParMenuData);

    new ApexCharts(document.querySelector("#commandesParMenuChart"), {
        chart: {
            type: 'bar',
            height: 350
        },
        series: [{
            name: 'Commandes',
            data: commandesParMenuData
        }],
        xaxis: {
            categories: commandesParMenuLabels
        }
    }).render();

    new ApexCharts(document.querySelector("#caParMenuChart"), {
        chart: {
            type: 'donut',
            height: 350
        },
        labels: caParMenuLabels,
        series: caParMenuData
    }).render();
</script>
@endpush