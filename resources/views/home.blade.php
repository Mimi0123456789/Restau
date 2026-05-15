{{-- resources/views/home.blade.php --}}
@extends('layouts.app')

@section('title', 'Vite & Gourmand')

@section('content')

    {{-- HERO --}}
    <section class="bg-gradient-to-br from-primary to-blue-700 text-white h-[600px]">
        <div class="max-w-7xl mx-auto px-6 h-full flex items-center">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center w-full">
                <div>
                    <h1 class="text-5xl font-bold mb-6">Excellence Culinaire & Service Professionnel</h1>
                    <p class="text-xl mb-8 text-blue-100">
                        « Vite & Gourmand » est une entreprise constituée de deux personnes, Julie et José. Elle existe
                        depuis 25 ans à Bordeaux, et propose leurs prestations pour tout événement (simple repas
                        comme Noel ou encore Pâques) au travers d’un menu en constante évolution.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#menus" class="bg-secondary px-8 py-3 rounded-lg font-semibold hover:bg-yellow-600">
                            Découvrir nos menus
                        </a>
                        <a href="#contact" class="border-2 border-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-primary">
                            Nous contacter
                        </a>
                    </div>
                </div>

                <div class="h-96 rounded-lg overflow-hidden">
                    <img class="w-full h-full object-cover"
                         src="https://storage.googleapis.com/uxpilot-auth.appspot.com/815db2cf5f-3ab910e53d62b1718328.png"
                         alt="Chef cuisine">
                </div>
            </div>
        </div>
    </section>

    {{-- ABOUT --}}
    <section id="about" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h2 class="text-4xl font-bold mb-4">Notre Entreprise</h2>
            <p class="text-xl text-gray-600 mb-16">
                Depuis 25 ans, Julie et José vous accompagnent pour tous vos événements.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <i class="fa-solid fa-award text-primary text-3xl mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">Excellence</h3>
                    <p class="text-gray-600">Produits de qualité & savoir-faire reconnu</p>
                </div>

                <div>
                    <i class="fa-solid fa-users text-secondary text-3xl mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">Équipe professionnelle</h3>
                    <p class="text-gray-600">Chefs expérimentés & personnel qualifié</p>
                </div>

                <div>
                    <i class="fa-solid fa-clock text-accent text-3xl mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">Ponctualité</h3>
                    <p class="text-gray-600">Respect des délais et du service</p>
                </div>
            </div>
        </div>
    </section>

    {{-- MENUS --}}
    <section id="menus" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-4xl font-bold text-center mb-12">Nos Menus</h2>

            {{-- FILTRES --}}
            <div class="bg-white rounded-xl shadow p-6 mb-10">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4 items-end">
                    <div>
                        <label for="theme_id" class="block text-sm font-medium text-gray-700 mb-2">Thème</label>
                        <select id="theme_id" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                            <option value="">Tous les thèmes</option>
                            @foreach($themes as $theme)
                                <option value="{{ $theme->id }}">{{ $theme->libelle }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="regime_id" class="block text-sm font-medium text-gray-700 mb-2">Régime</label>
                        <select id="regime_id" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                            <option value="">Tous les régimes</option>
                            @foreach($regimes as $regime)
                                <option value="{{ $regime->id }}">{{ $regime->libelle }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="prix_max" class="block text-sm font-medium text-gray-700 mb-2">
                            Prix maximum
                        </label>
                        <input
                            id="prix_max"
                            type="number"
                            step="0.01"
                            min="0"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2"
                            placeholder="Ex : 50"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Fourchette de prix
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <input
                                id="prix_min_range"
                                type="number"
                                step="0.01"
                                min="0"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2"
                                placeholder="Min"
                            >
                            <input
                                id="prix_max_range"
                                type="number"
                                step="0.01"
                                min="0"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2"
                                placeholder="Max"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="nombre_personne_minimum" class="block text-sm font-medium text-gray-700 mb-2">
                            Nb pers. minimum
                        </label>
                        <input
                            id="nombre_personne_minimum"
                            type="number"
                            min="1"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2"
                            placeholder="Ex : 20"
                        >
                    </div>
                </div>

                <div class="flex gap-3 mt-4">
                    <button
                        type="button"
                        id="reset-filters"
                        class="border border-gray-300 px-5 py-2 rounded-lg font-semibold text-gray-700 hover:bg-gray-100"
                    >
                        Réinitialiser
                    </button>

                    <span id="menus-count" class="text-sm text-gray-500 self-center"></span>
                </div>
            </div>

            {{-- MENUS LISTE --}}
            <div id="menus-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                @forelse($menus as $menu)
                    <div
                        class="menu-card bg-white rounded-lg shadow overflow-hidden"
                        data-theme-id="{{ $menu->theme_id }}"
                        data-regime-id="{{ $menu->regime_id }}"
                        data-prix="{{ $menu->prix_par_personne }}"
                        data-nombre-personne-minimum="{{ $menu->nombre_personne_minimum }}"
                    >
                        @php
                            $platsAvecPhoto = $menu->plats->filter(fn($plat) => !empty($plat->photo));
                        @endphp

                        @if($platsAvecPhoto->isNotEmpty())
                            <div id="carouselMenu{{ $menu->id }}" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner h-64">
                                    @foreach($platsAvecPhoto as $index => $plat)
                                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }} h-64">
                                            <img src="{{ $plat->photo_url }}"
                                                 class="d-block w-100 h-100 object-fit-cover"
                                                 alt="{{ $plat->titre_plat }}">
                                            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded px-2 py-1">
                                                <small>{{ $plat->titre_plat }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @if($platsAvecPhoto->count() > 1)
                                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselMenu{{ $menu->id }}" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon"></span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#carouselMenu{{ $menu->id }}" data-bs-slide="next">
                                        <span class="carousel-control-next-icon"></span>
                                    </button>
                                @endif
                            </div>
                        @else
                            <div class="h-64 bg-gray-200 flex items-center justify-center text-gray-500">
                                Aucune photo disponible
                            </div>
                        @endif

                        <div class="p-6">
                            <div class="flex justify-between items-start gap-4 mb-2">
                                <h3 class="text-xl font-semibold">{{ $menu->titre }}</h3>
                                <span class="text-2xl font-bold text-primary whitespace-nowrap">
                                    {{ number_format($menu->prix_par_personne, 2, ',', ' ') }}€
                                </span>
                            </div>

                            <p class="text-gray-600 mb-3">
                                {{ $menu->description }}
                            </p>

                            <div class="text-sm text-gray-500 space-y-1">
                                <p><strong>Thème :</strong> {{ $menu->theme->libelle ?? 'Non renseigné' }}</p>
                                <p><strong>Régime :</strong> {{ $menu->regime->libelle ?? 'Non renseigné' }}</p>
                                <p><strong>Minimum :</strong> {{ $menu->nombre_personne_minimum }} personne(s)</p>
                            </div>

                            @if($menu->plats->isNotEmpty())
                                <div class="mt-4">
                                    <p class="text-sm font-semibold text-gray-700 mb-2">Plats inclus :</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($menu->plats as $plat)
                                            <span class="inline-block bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded-full">
                                                {{ $plat->titre_plat }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @auth
                                @if(auth()->user()->role_id == 2)
                                    <form method="POST" action="{{ route('panier.add', $menu) }}" class="mt-4">
                                        @csrf
                                        <button type="submit" class="w-full bg-primary text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700">
                                            Ajouter au panier
                                        </button>
                                    </form>
                                @endif
                            @else
                                <div class="mt-4">
                                    <a href="{{ route('login') }}" class="block text-center bg-primary text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700">
                                        Connectez-vous pour commander
                                    </a>
                                </div>
                            @endauth
                        </div>
                    </div>
                @empty
                    <div id="empty-initial-state" class="col-span-full text-center text-gray-500">
                        Aucun menu disponible.
                    </div>
                @endforelse
            </div>

            <div id="empty-filter-state" class="hidden text-center text-gray-500 mt-8">
                Aucun menu ne correspond aux filtres sélectionnés.
            </div>

            <br><br>

            <div class="max-w-7xl mx-auto px-6 text-center">
                <h2 class="text-4xl font-bold mb-4">Ils parlent de nous !</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                    @forelse($avis ?? [] as $unAvis)
                        <div class="bg-white rounded-xl shadow p-6 text-left border border-gray-100">
                            <div class="flex items-center justify-between gap-4 mb-3">
                                <div>
                                    <p class="font-semibold text-gray-900">
                                        {{ $unAvis->commande->user->prenom ?? 'Client' }}
                                        {{ strtoupper(substr($unAvis->commande->user->nom ?? '', 0, 1)) }}.
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ $unAvis->created_at?->format('d/m/Y') }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fa-solid fa-star {{ $i <= (int) $unAvis->note ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                    @endfor
                                </div>
                            </div>

                            <p class="text-gray-600">
                                {{ $unAvis->commentaire }}
                            </p>
                        </div>
                    @empty
                        <div class="col-span-full text-center text-gray-500">
                            Aucun avis validé pour le moment.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const themeSelect = document.getElementById('theme_id');
            const regimeSelect = document.getElementById('regime_id');
            const prixMaxInput = document.getElementById('prix_max');
            const prixMinRangeInput = document.getElementById('prix_min_range');
            const prixMaxRangeInput = document.getElementById('prix_max_range');
            const nombrePersonneMinimumInput = document.getElementById('nombre_personne_minimum');
            const resetButton = document.getElementById('reset-filters');
            const cards = Array.from(document.querySelectorAll('.menu-card'));
            const emptyFilterState = document.getElementById('empty-filter-state');
            const menusCount = document.getElementById('menus-count');

            const parseNumber = (value) => {
                if (value === '' || value === null || value === undefined) {
                    return null;
                }

                const parsed = Number(value);
                return Number.isNaN(parsed) ? null : parsed;
            };

            const applyFilters = () => {
                const themeId = themeSelect.value;
                const regimeId = regimeSelect.value;
                const prixMax = parseNumber(prixMaxInput.value);
                const prixMinRange = parseNumber(prixMinRangeInput.value);
                const prixMaxRange = parseNumber(prixMaxRangeInput.value);
                const nombrePersonneMinimum = parseNumber(nombrePersonneMinimumInput.value);

                let visibleCount = 0;

                cards.forEach((card) => {
                    const cardThemeId = card.dataset.themeId;
                    const cardRegimeId = card.dataset.regimeId;
                    const cardPrix = parseNumber(card.dataset.prix);
                    const cardNombreMinimum = parseNumber(card.dataset.nombrePersonneMinimum);

                    let visible = true;

                    if (themeId && cardThemeId !== themeId) {
                        visible = false;
                    }

                    if (regimeId && cardRegimeId !== regimeId) {
                        visible = false;
                    }

                    if (prixMax !== null && cardPrix > prixMax) {
                        visible = false;
                    }

                    if (prixMinRange !== null && cardPrix < prixMinRange) {
                        visible = false;
                    }

                    if (prixMaxRange !== null && cardPrix > prixMaxRange) {
                        visible = false;
                    }

                    if (nombrePersonneMinimum !== null && cardNombreMinimum < nombrePersonneMinimum) {
                        visible = false;
                    }

                    card.classList.toggle('hidden', !visible);

                    if (visible) {
                        visibleCount++;
                    }
                });

                emptyFilterState.classList.toggle('hidden', visibleCount > 0);
                menusCount.textContent = `${visibleCount} menu(x) affiché(s)`;
            };

            [
                themeSelect,
                regimeSelect,
                prixMaxInput,
                prixMinRangeInput,
                prixMaxRangeInput,
                nombrePersonneMinimumInput
            ].forEach((element) => {
                element.addEventListener('input', applyFilters);
                element.addEventListener('change', applyFilters);
            });

            resetButton.addEventListener('click', () => {
                themeSelect.value = '';
                regimeSelect.value = '';
                prixMaxInput.value = '';
                prixMinRangeInput.value = '';
                prixMaxRangeInput.value = '';
                nombrePersonneMinimumInput.value = '';
                applyFilters();
            });

            applyFilters();
        });
    </script>

@endsection