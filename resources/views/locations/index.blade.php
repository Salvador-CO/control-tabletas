@extends('layouts.app')

@section('title', 'Sedes / Planteles')
@section('page-title', 'Gestión de Sedes')

@section('content')
<div class="row g-3">
    <div class="col-12 col-xl-8">
        <div class="data-table">
            <div class="p-3 border-bottom">
                <h6 class="fw-semibold mb-0"><i class="bi bi-geo-alt-fill text-primary me-2"></i>{{ $locations->count() }} sedes registradas</h6>
            </div>
            @if($locations->isEmpty())
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-geo fs-1 d-block mb-2"></i>No hay sedes registradas.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr><th>Nombre</th><th>Estado</th><th>Personal</th><th>Acciones</th></tr></thead>
                        <tbody>
                        @foreach($locations as $loc)
                        <tr>
                            <td class="fw-semibold">{{ $loc->name }}</td>
                            <td>{{ $loc->state ?? '—' }}</td>
                            <td><span class="badge bg-light text-dark">{{ $loc->staff_count }}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal" data-bs-target="#editLocModal"
                                    data-id="{{ $loc->id }}" data-name="{{ $loc->name }}" data-state="{{ $loc->state }}">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <form method="POST" action="{{ route('locations.destroy', $loc) }}" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar sede {{ $loc->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash-fill"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="stat-card">
            <h6 class="fw-semibold mb-3"><i class="bi bi-geo-alt-fill text-success me-2"></i>Agregar Sede</h6>
            <form method="POST" action="{{ route('locations.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Nombre de la Sede *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Estado / Municipio</label>
                    <input type="text" name="state" class="form-control" value="{{ old('state') }}" placeholder="Ej: Tuxtla Gutiérrez">
                </div>
                <button type="submit" class="btn btn-primary-custom w-100">
                    <i class="bi bi-save-fill me-2"></i>Guardar
                </button>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editLocModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header"><h5 class="modal-title fw-semibold">Editar Sede</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" id="editLocForm">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Nombre</label>
                        <input type="text" name="name" id="edit_l_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Estado</label>
                        <input type="text" name="state" id="edit_l_state" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('editLocModal').addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    const form = document.getElementById('editLocForm');
    form.action = '/locations/' + btn.dataset.id;
    document.getElementById('edit_l_name').value  = btn.dataset.name  || '';
    document.getElementById('edit_l_state').value = btn.dataset.state || '';
});
</script>
@endpush
