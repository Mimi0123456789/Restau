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
                    {{ $dateDebut ? \Carbon\Carbon::parse($dateDebut)->translatedFormat('d/m/Y') : 'début' }} → {{ $dateFin ? \Carbon\Carbon::parse($dateFin)->translatedFormat('d/m/Y') : 'aujourd’hui' }}
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
                        <label class="form-label">Filtrer par menus</label>
                        <div class="dropdown menu-dropdown">
                            <button
                                type="button"
                                class="btn btn-outline-secondary w-100 text-start d-flex justify-content-between align-items-center"
                                id="menuDropdownToggle"
                                aria-expanded="false"
                            >
                                <span id="menuDropdownLabel">Sélectionner des menus</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>

                            <div class="dropdown-menu w-100 shadow-sm border-0 mt-2 p-2" id="menuDropdownMenu" style="max-height: 260px; overflow-y: auto;">
                                <input
                                    type="text"
                                    id="menu_search"
                                    class="form-control form-control-sm mb-2"
                                    placeholder="Rechercher un menu"
                                >

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" value="" id="menu_all" {{ empty($menuIds) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="menu_all">Tous les menus</label>
                                </div>

                                @foreach($menus as $menu)
                                    <div class="form-check mb-2 menu-item" data-menu-name="{{ strtolower($menu->titre) }}">
                                        <input
                                            class="form-check-input menu-checkbox"
                                            type="checkbox"
                                            name="menu_ids[]"
                                            value="{{ $menu->id }}"
                                            id="menu_{{ $menu->id }}"
                                            @checked(in_array($menu->id, $menuIds ?? [], true))
                                        >
                                        <label class="form-check-label" for="menu_{{ $menu->id }}">
                                            {{ $menu->titre }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
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

    const menuAllCheckbox = document.getElementById('menu_all');
    const menuCheckboxes = document.querySelectorAll('.menu-checkbox');
    const menuSearchInput = document.getElementById('menu_search');
    const menuItems = document.querySelectorAll('.menu-item');
    const menuDropdownToggle = document.getElementById('menuDropdownToggle');
    const menuDropdownMenu = document.getElementById('menuDropdownMenu');
    const menuDropdownLabel = document.getElementById('menuDropdownLabel');

    const updateMenuDropdownLabel = () => {
        const checked = Array.from(menuCheckboxes).filter(item => item.checked);

        if (checked.length === 0) {
            menuDropdownLabel.textContent = 'Tous les menus';
            return;
        }

        if (checked.length === 1) {
            const item = checked[0].closest('.menu-item');
            const label = item ? item.textContent.trim() : '1 menu sélectionné';
            menuDropdownLabel.textContent = label;
            return;
        }

        menuDropdownLabel.textContent = `${checked.length} menus sélectionnés`;
    };

    if (menuDropdownToggle && menuDropdownMenu) {
        menuDropdownToggle.addEventListener('click', function () {
            const isOpen = menuDropdownMenu.classList.contains('show');
            menuDropdownMenu.classList.toggle('show', !isOpen);
            menuDropdownToggle.setAttribute('aria-expanded', String(!isOpen));
        });

        document.addEventListener('click', function (event) {
            if (!event.target.closest('.menu-dropdown')) {
                menuDropdownMenu.classList.remove('show');
                menuDropdownToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    if (menuAllCheckbox) {
        menuAllCheckbox.addEventListener('change', function () {
            menuCheckboxes.forEach(checkbox => {
                checkbox.checked = !this.checked;
            });
            updateMenuDropdownLabel();
        });
    }

    menuCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            if (menuAllCheckbox) {
                const anyChecked = Array.from(menuCheckboxes).some(item => item.checked);
                menuAllCheckbox.checked = !anyChecked;
            }
            updateMenuDropdownLabel();
        });
    });

    if (menuSearchInput) {
        menuSearchInput.addEventListener('input', function () {
            const search = this.value.trim().toLowerCase();

            menuItems.forEach(item => {
                const name = item.dataset.menuName || '';
                const visible = !search || name.includes(search);
                item.style.display = visible ? '' : 'none';
            });
        });
    }

    updateMenuDropdownLabel();

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