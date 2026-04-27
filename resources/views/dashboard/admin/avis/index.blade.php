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
            <h1 class="h3 m-0">Avis clients</h1>
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
                                <th>Commande</th>
                                <th>Client</th>
                                <th>Note</th>
                                <th>Description</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($avis as $a)
                                <tr>
                                    <td>#{{ $a->commande_id }}</td>
                                    <td>
                                        {{ $a->commande?->user?->prenom }} {{ $a->commande?->user?->nom }}
                                    </td>
                                    <td>{{ $a->note }}/5</td>
                                    <td>{{ $a->description ?: '—' }}</td>
                                    <td>
                                        @if($a->statut === 'valide')
                                            <span class="badge bg-success">Validé</span>
                                        @elseif($a->statut === 'refuse')
                                            <span class="badge bg-danger">Refusé</span>
                                        @else
                                            <span class="badge bg-warning text-dark">En attente</span>
                                        @endif
                                    </td>
                                    <td>{{ $a->created_at?->format('d/m/Y H:i') }}</td>
                                    <td class="text-end">
                                        <button
                                            class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal"
                                            data-id="{{ $a->id }}"
                                            data-statut="{{ $a->statut }}"
                                        >
                                            Modifier
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Aucun avis
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($avis->hasPages())
                <div class="card-footer">
                    {{ $avis->links() }}
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="editForm" class="modal-content" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Modifier le statut de l’avis</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_statut" class="form-label">Statut</label>
                        <select name="statut" id="edit_statut" class="form-select" required>
                            <option value="en attente">En attente</option>
                            <option value="valide">Validé</option>
                            <option value="refuse">Refusé</option>
                        </select>
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
        const urlTemplate = @json(route('avis.update', ['avi' => '__ID__']));

        document.getElementById('edit_statut').value = btn.dataset.statut;
        document.getElementById('editForm').action = urlTemplate.replace('__ID__', btn.dataset.id);
    });
</script>
@endpush