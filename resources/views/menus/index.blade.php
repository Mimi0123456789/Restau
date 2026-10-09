@extends('layouts.app')

@section('title', 'Gestion des menus')

@section('content')
    <div class="container pt-24">

        <div class="mb-4">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>
                Retour
            </a>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 m-0">🍽️ Gestion des menus et plats</h1>

            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createPlatModal" type="button">
                    ➕ Ajouter un plat
                </button>

                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createMenuModal" type="button">
                    ➕ Ajouter un menu
                </button>
            </div>
        </div>

        @if(session('ok'))
            <div class="alert alert-success">{{ session('ok') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="m-0 ps-3">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active"
                        data-bs-toggle="tab"
                        data-bs-target="#tab-menus"
                        type="button">
                    🍽️ Menus
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link"
                        data-bs-toggle="tab"
                        data-bs-target="#tab-plats"
                        type="button">
                    🍲 Plats
                </button>
            </li>
        </ul>

        <div class="tab-content">

            {{-- ================= TAB MENUS ================= --}}
            <div class="tab-pane fade show active" id="tab-menus">

                <div class="card mb-5">
                    <div class="card-header bg-white">
                        <h2 class="h5 mb-0">Menus</h2>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle mb-0">
                                <thead>
                                <tr>
                                    <th style="width: 60px;"></th>
                                    <th>Titre</th>
                                    <th>Min. pers.</th>
                                    <th>Prix / pers.</th>
                                    <th>Thème</th>
                                    <th>Régime</th>
                                    <th>Description</th>
                                    <th>Qté restante</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($menus as $menu)
                                    @php
                                        $allergenesMenu = $menu->plats
                                            ->flatMap(fn($plat) => $plat->allergenes)
                                            ->unique('id')
                                            ->pluck('libelle')
                                            ->values();
                                    @endphp

                                    <tr>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-secondary toggle-menu-details"
                                                    type="button"
                                                    data-target="platsMenu{{ $menu->id }}">
                                                <i class="fa-solid fa-chevron-down"></i>
                                            </button>
                                        </td>
                                        <td class="fw-semibold">{{ $menu->titre }}</td>
                                        <td>{{ $menu->nombre_personne_minimum }}</td>
                                        <td>{{ number_format($menu->prix_par_personne, 2, ',', ' ') }} €</td>
                                        <td>{{ $menu->theme->libelle ?? '-' }}</td>
                                        <td>{{ $menu->regime->libelle ?? '-' }}</td>
                                        <td style="max-width: 340px;">
                                            <div>
                                                {{ $menu->description }}
                                            </div>

                                            @if($allergenesMenu->isNotEmpty())
                                                <div class="small text-muted mt-1">
                                                    <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                                    Présence possible de :
                                                    <span class="fst-italic">{{ $allergenesMenu->implode(', ') }}</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $menu->quantite_restante }}</td>
                                        <td class="text-end">
                                            <button
                                                class="btn btn-sm btn-outline-secondary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editMenuModal_{{ $menu->id }}"
                                                type="button">
                                                Modifier
                                            </button>

                                            <form method="POST"
                                                  action="{{ route('menus.destroy', $menu) }}"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Supprimer ce menu ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" type="submit">
                                                    Supprimer
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <tr id="platsMenu{{ $menu->id }}" class="bg-light d-none">
                                        <td colspan="9">
                                            <div class="p-3">
                                                <div class="fw-semibold mb-2">Plats du menu</div>

                                                @if($menu->plats->isEmpty())
                                                    <div class="text-muted small">Aucun plat associé</div>
                                                @else
                                                    <div class="d-flex flex-column gap-2">
                                                        @foreach($menu->plats as $plat)
                                                            <div class="border rounded bg-white px-3 py-2">
                                                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                                                    <div>
                                                                        <div class="fw-semibold">{{ $plat->titre_plat }}</div>

                                                                        @if($plat->allergenes->isNotEmpty())
                                                                            <div class="small text-muted mt-1">
                                                                                <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                                                                Allergènes :
                                                                                {{ $plat->allergenes->pluck('libelle')->implode(', ') }}
                                                                            </div>
                                                                        @else
                                                                            <div class="small text-muted mt-1">
                                                                                Aucun allergène renseigné
                                                                            </div>
                                                                        @endif
                                                                    </div>

                                                                    <div>
                                                                        @if($plat->photo)
                                                                            <a href="{{ $plat->photo }}" target="_blank">
                                                                                <img src="{{ $plat->photo }}"
                                                                                     alt="{{ $plat->titre_plat }}"
                                                                                     style="width:60px; height:60px; object-fit:cover;"
                                                                                     class="rounded border">
                                                                            </a>
                                                                        @else
                                                                            <span class="badge bg-light text-secondary border">Sans photo</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            Aucun menu enregistré
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($menus->hasPages())
                        <div class="card-footer">
                            {{ $menus->links() }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- ================= TAB PLATS ================= --}}
            <div class="tab-pane fade" id="tab-plats">
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">Plats</h2>

                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createPlatModal" type="button">
                            ➕ Ajouter un plat
                        </button>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle mb-0">
                                <thead>
                                <tr>
                                    <th>Nom du plat</th>
                                    <th>Photo</th>
                                    <th>Allergènes</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($plats as $plat)
                                    <tr>
                                        <td class="fw-semibold">{{ $plat->titre_plat }}</td>
                                        <td>
                                            @if($plat->photo)
                                                <a href="{{ $plat->photo }}" target="_blank">
                                                    <img
                                                        src="{{ $plat->photo }}"
                                                        alt="{{ $plat->titre_plat }}"
                                                        style="width:60px;height:60px;object-fit:cover"
                                                        class="rounded border shadow-sm">
                                                </a>
                                            @else
                                                <span class="badge bg-light text-secondary border">Aucune</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($plat->allergenes->isNotEmpty())
                                                <span class="small text-muted">
                                                    {{ $plat->allergenes->pluck('libelle')->implode(', ') }}
                                                </span>
                                            @else
                                                <span class="small text-muted">Aucun</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <button
                                                class="btn btn-sm btn-outline-secondary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editPlatModal_{{ $plat->id }}"
                                                type="button">
                                                Modifier
                                            </button>

                                            <form method="POST"
                                                  action="{{ route('plats.destroy', $plat) }}"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Supprimer ce plat ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" type="submit">
                                                    Supprimer
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            Aucun plat enregistré
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= MODALE CRÉATION MENU ================= --}}
    <div class="modal fade" id="createMenuModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('menus.store') }}" class="modal-content">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Créer un menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Titre</label>
                            <input type="text" name="titre" class="form-control" value="{{ old('titre') }}" required maxlength="50">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Nb min. personnes</label>
                            <input type="number" name="nombre_personne_minimum" class="form-control" value="{{ old('nombre_personne_minimum') }}" min="1" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Prix / personne</label>
                            <input type="number" step="0.01" min="0" name="prix_par_personne" class="form-control" value="{{ old('prix_par_personne') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Thème</label>
                            <select name="theme_id" class="form-select" required>
                                <option value="">Choisir un thème</option>
                                @foreach($themes as $theme)
                                    <option value="{{ $theme->id }}" {{ old('theme_id') == $theme->id ? 'selected' : '' }}>
                                        {{ $theme->libelle }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Régime</label>
                            <select name="regime_id" class="form-select" required>
                                <option value="">Choisir un régime</option>
                                @foreach($regimes as $regime)
                                    <option value="{{ $regime->id }}" {{ old('regime_id') == $regime->id ? 'selected' : '' }}>
                                        {{ $regime->libelle }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Quantité restante</label>
                            <input type="number" name="quantite_restante" class="form-control" value="{{ old('quantite_restante', 0) }}" min="0" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Plats du menu</label>
                            <select name="plats[]" class="form-select" multiple size="8">
                                @foreach($plats as $plat)
                                    <option value="{{ $plat->id }}"
                                        {{ collect(old('plats', []))->contains($plat->id) ? 'selected' : '' }}>
                                        {{ $plat->titre_plat }}
                                        @if($plat->allergenes->isNotEmpty())
                                            — {{ $plat->allergenes->pluck('libelle')->implode(', ') }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Maintiens Ctrl (ou Cmd) pour sélectionner plusieurs plats.</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= MODALES MODIFICATION MENU ================= --}}
    @foreach($menus as $menu)
        <div class="modal fade" id="editMenuModal_{{ $menu->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form method="POST" action="{{ route('menus.update', $menu) }}" class="modal-content">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">Modifier le menu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Titre</label>
                                <input type="text" name="titre" class="form-control" value="{{ $menu->titre }}" required maxlength="50">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Nb min. personnes</label>
                                <input type="number" name="nombre_personne_minimum" class="form-control" value="{{ $menu->nombre_personne_minimum }}" min="1" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Prix / personne</label>
                                <input type="number" step="0.01" min="0" name="prix_par_personne" class="form-control" value="{{ $menu->prix_par_personne }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Thème</label>
                                <select name="theme_id" class="form-select" required>
                                    <option value="">Choisir un thème</option>
                                    @foreach($themes as $theme)
                                        <option value="{{ $theme->id }}" {{ $menu->theme_id == $theme->id ? 'selected' : '' }}>
                                            {{ $theme->libelle }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Régime</label>
                                <select name="regime_id" class="form-select" required>
                                    <option value="">Choisir un régime</option>
                                    @foreach($regimes as $regime)
                                        <option value="{{ $regime->id }}" {{ $menu->regime_id == $regime->id ? 'selected' : '' }}>
                                            {{ $regime->libelle }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Quantité restante</label>
                                <input type="number" name="quantite_restante" class="form-control" value="{{ $menu->quantite_restante }}" min="0" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4" required>{{ $menu->description }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Plats du menu</label>
                                <select name="plats[]" class="form-select" multiple size="8">
                                    @foreach($plats as $plat)
                                        <option value="{{ $plat->id }}"
                                            {{ $menu->plats->pluck('id')->contains($plat->id) ? 'selected' : '' }}>
                                            {{ $plat->titre_plat }}
                                            @if($plat->allergenes->isNotEmpty())
                                                — {{ $plat->allergenes->pluck('libelle')->implode(', ') }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Maintiens Ctrl (ou Cmd) pour sélectionner plusieurs plats.</small>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    {{-- ================= MODALE CRÉATION PLAT ================= --}}
    <div class="modal fade" id="createPlatModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('plats.store') }}" class="modal-content" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Créer un plat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nom du plat</label>
                            <input type="text" name="titre_plat" class="form-control" value="{{ old('titre_plat') }}" required maxlength="50">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Photo</label>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Allergènes</label>
                            <select name="allergenes[]" class="form-select" multiple size="8">
                                @foreach($allergenes as $allergene)
                                    <option value="{{ $allergene->id }}"
                                        {{ collect(old('allergenes', []))->contains($allergene->id) ? 'selected' : '' }}>
                                        {{ $allergene->libelle }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Maintiens Ctrl (ou Cmd) pour sélectionner plusieurs allergènes.</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= MODALES MODIFICATION PLAT ================= --}}
    @foreach($plats as $plat)
        <div class="modal fade" id="editPlatModal_{{ $plat->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form method="POST" action="{{ route('plats.update', $plat) }}" class="modal-content" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">Modifier le plat</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nom du plat</label>
                                <input type="text" name="titre_plat" class="form-control" value="{{ $plat->titre_plat }}" required maxlength="50">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Photo</label>
                                <input type="file" name="photo" class="form-control" accept="image/*">
                                @if(!empty($plat->photo))
                                    <small class="text-success">Une photo est déjà enregistrée.</small>
                                @endif
                            </div>

                            <div class="col-12">
                                <label class="form-label">Allergènes</label>
                                <select name="allergenes[]" class="form-select" multiple size="8">
                                    @foreach($allergenes as $allergene)
                                        <option value="{{ $allergene->id }}"
                                            {{ $plat->allergenes->pluck('id')->contains($allergene->id) ? 'selected' : '' }}>
                                            {{ $allergene->libelle }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Maintiens Ctrl (ou Cmd) pour sélectionner plusieurs allergènes.</small>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.toggle-menu-details').forEach(button => {
            button.addEventListener('click', function () {
                const targetId = this.dataset.target;
                const row = document.getElementById(targetId);
                const icon = this.querySelector('i');

                if (!row) return;

                row.classList.toggle('d-none');

                if (icon) {
                    icon.classList.toggle('fa-chevron-down');
                    icon.classList.toggle('fa-chevron-up');
                }
            });
        });
    });
</script>
@endpush