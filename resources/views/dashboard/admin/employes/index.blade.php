@extends('layouts.app')

@section('content')

    <div class="container pt-24">
        <div class="mb-4">
            <a href="{{ route('admin.dashboard') }}"
               class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>
                Retour au tableau de bord
            </a>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 m-0">Employés</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                Nouvel employé
            </button>
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

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($employes as $u)
                            <tr>
                                <td>{{ $u->nom }}</td>
                                <td>{{ $u->prenom }}</td>
                                <td>{{ $u->email }}</td>
                                <td>
                                    @if($u->is_active)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-secondary">Inactif</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button
                                        class="btn btn-sm btn-outline-secondary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal"
                                        data-id="{{ $u->id }}"
                                        data-nom="{{ $u->nom }}"
                                        data-prenom="{{ $u->prenom }}"
                                        data-email="{{ $u->email }}"
                                    >
                                        Modifier
                                    </button>

                                    <form method="POST"
                                          action="{{ route('admin.employes.toggle', $u) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Changer le statut de ce compte ?')">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-outline-danger">
                                            {{ $u->is_active ? 'Désactiver' : 'Réactiver' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Aucun employé
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($employes->hasPages())
                <div class="card-footer">
                    {{ $employes->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- ================= MODALE CRÉATION ================= --}}
    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.employes.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Créer un employé</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Nom</label>
                        <input name="nom" class="form-control" value="{{ old('nom') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Prénom</label>
                        <input name="prenom" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input name="email" type="email" class="form-control" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button class="btn btn-primary">Créer</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= MODALE ÉDITION ================= --}}
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="editForm" class="modal-content" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Modifier l’employé</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Nom</label>
                        <input name="nom" id="edit_nom" class="form-control" required>
                    </div>
                
                    <div class="mb-3">
                        <label class="form-label">Prénom</label>
                        <input name="prenom" id="edit_prenom" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input name="email" id="edit_email" type="email" class="form-control" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('editModal').addEventListener('show.bs.modal', function (e) {
        const btn = e.relatedTarget;

        document.getElementById('edit_prenom').value = btn.dataset.prenom;
        document.getElementById('edit_nom').value = btn.dataset.nom;
        document.getElementById('edit_email').value = btn.dataset.email;
        document.getElementById('editForm').action = `/admin/employes/${btn.dataset.id}`;
    });
</script>
@endpush