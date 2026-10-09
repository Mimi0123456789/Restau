@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-6 pt-24 pb-12">
        <div class="mb-4">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>
                Retour au tableau de bord
            </a>
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h1 class="text-3xl font-bold mb-1">📊 Pilotage commercial</h1>
                <p class="text-gray-600 mb-0">
                    Suivi du chiffre d’affaires, des commandes et de la performance des menus.
                </p>
            </div>
            <div class="text-muted small">
                Période :
                @if($dateDebut || $dateFin)
                    {{ $dateDebut ?: 'début' }} → {{ $dateFin ?: 'aujourd’hui' }}
                @else
                    tous les résultats
                @endif
            </div>
        </div>

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
                        <input type="date" name="date_debut" id="date_debut" class="form-control" value="{{ $dateDebut }}">
                    </div>

                    <div class="col-md-3">
                        <label for="date_fin" class="form-label">Date fin</label>
                        <input type="date" name="date_fin" id="date_fin" class="form-control" value="{{ $dateFin }}">
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

        <div class="row g-4 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase">Chiffre d’affaires</div>
                        <div class="display-6 fw-bold mt-2">{{ number_format($totalChiffreAffaires, 2, ',', ' ') }} €</div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase">Commandes</div>
                        <div class="display-6 fw-bold mt-2">{{ $totalCommandes }}</div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase">Panier moyen</div>
                        <div class="display-6 fw-bold mt-2">{{ number_format($panierMoyen, 2, ',', ' ') }} €</div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase">Livraison</div>
                        <div class="display-6 fw-bold mt-2">{{ number_format($totalLivraison, 2, ',', ' ') }} €</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card shadow-sm h-100">
                    <div class="card-header fw-bold">Évolution du chiffre d’affaires</div>
                    <div class="card-body">
                        <div id="dailyRevenueChart"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header fw-bold">Répartition des statuts</div>
                    <div class="card-body">
                        <div id="statusChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-7">
                <div class="card shadow-sm h-100">
                    <div class="card-header fw-bold">Performance par menu</div>
                    <div class="card-body">
                        <div id="commandesParMenuChart"></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-5">
                <div class="card shadow-sm h-100">
                    <div class="card-header fw-bold">CA par menu</div>
                    <div class="card-body">
                        <div id="caParMenuChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mt-4">
            <div class="card-header fw-bold">Commandes récentes</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Client</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentCommandes as $commande)
                                <tr>
                                    <td>{{ $commande->id }}</td>
                                    <td>
                                        {{ optional($commande->user)->prenom }} {{ optional($commande->user)->nom }}
                                        <div class="small text-muted">{{ optional($commande->user)->email }}</div>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($commande->date_commande)->translatedFormat('d/m/Y') }}</td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'En attente' => 'warning',
                                                'Accepté' => 'info',
                                                'En préparation' => 'primary',
                                                'En cours de livraison' => 'secondary',
                                                'Livré' => 'success',
                                                'En attente du retour de matériel' => 'dark',
                                                'Terminée' => 'success',
                                                'Annulée' => 'danger',
                                            ];
                                            $statusClass = $statusColors[$commande->statut] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">{{ $commande->statut }}</span>
                                    </td>
                                    <td>{{ number_format($commande->prix_menu + $commande->prix_livraison, 2, ',', ' ') }} €</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Aucune commande sur la période.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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

    const statusLabels = @json($statusLabels);
    const statusData = @json($statusData);

    const dailyRevenueLabels = @json($dailyRevenueLabels);
    const dailyRevenueData = @json($dailyRevenueData);

    new ApexCharts(document.querySelector('#dailyRevenueChart'), {
        chart: {
            type: 'line',
            height: 320,
            toolbar: { show: false }
        },
        series: [{
            name: 'CA journalier',
            data: dailyRevenueData
        }],
        xaxis: {
            categories: dailyRevenueLabels
        },
        yaxis: {
            labels: {
                formatter: function (value) {
                    return value.toFixed(2) + ' €';
                }
            }
        },
        tooltip: {
            y: {
                formatter: function (value) {
                    return value.toFixed(2) + ' €';
                }
            }
        }
    }).render();

    new ApexCharts(document.querySelector('#statusChart'), {
        chart: {
            type: 'donut',
            height: 300
        },
        labels: statusLabels,
        series: statusData,
        legend: {
            position: 'bottom'
        }
    }).render();

    new ApexCharts(document.querySelector('#commandesParMenuChart'), {
        chart: {
            type: 'bar',
            height: 330
        },
        series: [{
            name: 'Commandes',
            data: commandesParMenuData
        }],
        xaxis: {
            categories: commandesParMenuLabels
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '50%'
            }
        }
    }).render();

    new ApexCharts(document.querySelector('#caParMenuChart'), {
        chart: {
            type: 'donut',
            height: 330
        },
        labels: caParMenuLabels,
        series: caParMenuData
    }).render();
</script>
@endpush