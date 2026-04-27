@extends('layouts.app')

@section('content')
    @php
        $isAdmin = auth()->user()?->role_id === 1;
    @endphp

    <div class="container pt-24">
        <div class="mb-4">
            <a href="{{ auth()->user()?->role_id === 1 ? route('admin.dashboard') : route('dashboard.employe') }}"                class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>
                Retour au tableau de bord
            </a>        
        </div>

        <h1 class="h3 mb-4">⚙️ Paramètres du système</h1>

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

        {{-- ONGLETS --}}
        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#horaires" type="button">
                    🕒 Horaires
                </button>
            </li>

            @if($isAdmin)
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#allergenes" type="button">
                        ⚠️ Allergènes
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#regimes" type="button">
                        🥗 Régimes
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#themes" type="button">
                        🎉 Thèmes
                    </button>
                </li>
            @endif
        </ul>

        <div class="tab-content">

            {{-- ===================== HORAIRES ===================== --}}
            <div class="tab-pane fade show active" id="horaires">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Horaires d’ouverture</h4>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addHoraire" type="button">
                        ➕ Ajouter
                    </button>
                </div>

                <table class="table table-striped align-middle">
                    <thead>
                    <tr>
                        <th>Jour</th>
                        <th>Ouverture</th>
                        <th>Fermeture</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($horaires as $h)
                        <tr>
                            <td>{{ $h->jour }}</td>
                            <td>{{ $h->heure_ouverture }}</td>
                            <td>{{ $h->heure_fermeture }}</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-secondary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editHoraire_{{ $h->id }}"
                                        type="button">
                                    Modifier
                                </button>

                                <form method="POST"
                                      action="{{ route('admin.parametres.horaires.destroy', $h) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Supprimer cet horaire ?')">
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
                            <td colspan="4" class="text-center text-muted">Aucun horaire</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($isAdmin)
                {{-- ===================== ALLERGÈNES ===================== --}}
                <div class="tab-pane fade" id="allergenes">
                    <div class="d-flex justify-content-between mb-3">
                        <h4>Allergènes</h4>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAllergene" type="button">
                            ➕ Ajouter
                        </button>
                    </div>

                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Libellé</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($allergenes as $a)
                            <tr>
                                <td>{{ $a->libelle }}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editAllergene_{{ $a->id }}"
                                            type="button">
                                        Modifier
                                    </button>

                                    <form method="POST"
                                          action="{{ route('admin.parametres.allergenes.destroy', $a) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Supprimer cet allergène ?')">
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
                                <td colspan="2" class="text-center text-muted">Aucun allergène</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- ===================== RÉGIMES ===================== --}}
                <div class="tab-pane fade" id="regimes">
                    <div class="d-flex justify-content-between mb-3">
                        <h4>Régimes alimentaires</h4>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRegime" type="button">
                            ➕ Ajouter
                        </button>
                    </div>

                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Libellé</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($regimes as $r)
                            <tr>
                                <td>{{ $r->libelle }}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editRegime_{{ $r->id }}"
                                            type="button">
                                        Modifier
                                    </button>

                                    <form method="POST"
                                          action="{{ route('admin.parametres.regimes.destroy', $r) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Supprimer ce régime ?')">
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
                                <td colspan="2" class="text-center text-muted">Aucun régime</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- ===================== THÈMES ===================== --}}
                <div class="tab-pane fade" id="themes">
                    <div class="d-flex justify-content-between mb-3">
                        <h4>Thèmes de menus</h4>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTheme" type="button">
                            ➕ Ajouter
                        </button>
                    </div>

                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Libellé</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($themes as $t)
                            <tr>
                                <td>{{ $t->libelle }}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editTheme_{{ $t->id }}"
                                            type="button">
                                        Modifier
                                    </button>

                                    <form method="POST"
                                          action="{{ route('admin.parametres.themes.destroy', $t) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Supprimer ce thème ?')">
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
                                <td colspan="2" class="text-center text-muted">Aucun thème</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    </div>

    {{-- ===================== MODALE AJOUT HORAIRE ===================== --}}
    <div class="modal fade" id="addHoraire" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.parametres.horaires.store') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un horaire</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Jour</label>
                        <select name="jour" class="form-control" required>
                            <option value="Lundi">Lundi</option>
                            <option value="Mardi">Mardi</option>
                            <option value="Mercredi">Mercredi</option>
                            <option value="Jeudi">Jeudi</option>
                            <option value="Vendredi">Vendredi</option>
                            <option value="Samedi">Samedi</option>
                            <option value="Dimanche">Dimanche</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Heure d’ouverture</label>
                        <input type="time" name="heure_ouverture" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Heure de fermeture</label>
                        <input type="time" name="heure_fermeture" class="form-control" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================== MODALES ÉDITION HORAIRES ===================== --}}
    @foreach($horaires as $h)
        <div class="modal fade" id="editHoraire_{{ $h->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST"
                      action="{{ route('admin.parametres.horaires.update', $h) }}"
                      class="modal-content">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">Modifier l’horaire</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Jour</label>
                            <select name="jour" class="form-control" required>
                                <option value="Lundi" {{ $h->jour == 'Lundi' ? 'selected' : '' }}>Lundi</option>
                                <option value="Mardi" {{ $h->jour == 'Mardi' ? 'selected' : '' }}>Mardi</option>
                                <option value="Mercredi" {{ $h->jour == 'Mercredi' ? 'selected' : '' }}>Mercredi</option>
                                <option value="Jeudi" {{ $h->jour == 'Jeudi' ? 'selected' : '' }}>Jeudi</option>
                                <option value="Vendredi" {{ $h->jour == 'Vendredi' ? 'selected' : '' }}>Vendredi</option>
                                <option value="Samedi" {{ $h->jour == 'Samedi' ? 'selected' : '' }}>Samedi</option>
                                <option value="Dimanche" {{ $h->jour == 'Dimanche' ? 'selected' : '' }}>Dimanche</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Heure d’ouverture</label>
                            <input type="time" name="heure_ouverture" class="form-control" value="{{ $h->heure_ouverture }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Heure de fermeture</label>
                            <input type="time" name="heure_fermeture" class="form-control" value="{{ $h->heure_fermeture }}" required>
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

    @if($isAdmin)
        {{-- ===================== MODALE AJOUT ALLERGÈNE ===================== --}}
        <div class="modal fade" id="addAllergene" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.parametres.allergenes.store') }}" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter un allergène</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <label class="form-label">Libellé</label>
                        <input name="libelle" class="form-control" required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===================== MODALES ÉDITION ALLERGÈNES ===================== --}}
        @foreach($allergenes as $a)
            <div class="modal fade" id="editAllergene_{{ $a->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <form method="POST"
                          action="{{ route('admin.parametres.allergenes.update', $a) }}"
                          class="modal-content">
                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5 class="modal-title">Modifier allergène</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <input name="libelle" class="form-control" value="{{ $a->libelle }}" required>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach

        {{-- ===================== MODALE AJOUT RÉGIME ===================== --}}
        <div class="modal fade" id="addRegime" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.parametres.regimes.store') }}" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter un régime</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <label class="form-label">Libellé</label>
                        <input name="libelle" class="form-control" required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===================== MODALES ÉDITION RÉGIMES ===================== --}}
        @foreach($regimes as $r)
            <div class="modal fade" id="editRegime_{{ $r->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <form method="POST"
                          action="{{ route('admin.parametres.regimes.update', $r) }}"
                          class="modal-content">
                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5 class="modal-title">Modifier régime</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <input name="libelle" class="form-control" value="{{ $r->libelle }}" required>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach

        {{-- ===================== MODALE AJOUT THÈME ===================== --}}
        <div class="modal fade" id="addTheme" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.parametres.themes.store') }}" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter un thème</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <label class="form-label">Libellé</label>
                        <input name="libelle" class="form-control" required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===================== MODALES ÉDITION THÈMES ===================== --}}
        @foreach($themes as $t)
            <div class="modal fade" id="editTheme_{{ $t->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <form method="POST"
                          action="{{ route('admin.parametres.themes.update', $t) }}"
                          class="modal-content">
                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5 class="modal-title">Modifier thème</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <input name="libelle" class="form-control" value="{{ $t->libelle }}" required>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    @endif
@endsection