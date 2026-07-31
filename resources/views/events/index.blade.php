@extends('layouts.app')

@section('title', 'Exacers / Periodos')
@section('page-title', 'Exacers & Periodos')

@section('content')
<div class="row g-3">
    <div class="col-12 col-xl-8">
        <div class="data-table">
            <div class="p-3 border-bottom">
                <h6 class="fw-semibold mb-0"><i class="bi bi-calendar-event-fill text-primary me-2"></i>{{ $events->count() }} eventos registrados</h6>
            </div>
            @if($events->isEmpty())
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>No hay eventos registrados.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr><th>Evento</th><th>Inicio</th><th>Fin</th><th>Vales</th><th>Acciones</th></tr></thead>
                        <tbody>
                        @foreach($events as $ev)
                        <tr>
                            <td class="fw-semibold">{{ $ev->name }}</td>
                            <td class="small">{{ $ev->start_date ? \Carbon\Carbon::parse($ev->start_date)->format('d/m/Y') : '—' }}</td>
                            <td class="small">{{ $ev->end_date ? \Carbon\Carbon::parse($ev->end_date)->format('d/m/Y') : '—' }}</td>
                            <td><span class="badge bg-light text-dark">{{ $ev->assignments_count }}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal" data-bs-target="#editEvModal"
                                    data-id="{{ $ev->id }}" data-name="{{ $ev->name }}"
                                    data-start="{{ $ev->start_date }}" data-end="{{ $ev->end_date }}">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <form method="POST" action="{{ route('events.destroy', $ev) }}" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar evento {{ $ev->name }}?')">
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
            <h6 class="fw-semibold mb-3"><i class="bi bi-plus-circle-fill text-success me-2"></i>Registrar Exacer / Evento</h6>
            <form method="POST" action="{{ route('events.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Nombre del Evento *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                           placeholder="Ej: Exacer 2025-I" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Fecha de Inicio *</label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Fecha de Fin *</label>
                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
                </div>
                <button type="submit" class="btn btn-primary-custom w-100">
                    <i class="bi bi-save-fill me-2"></i>Guardar
                </button>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editEvModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header"><h5 class="modal-title fw-semibold">Editar Evento</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" id="editEvForm">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Nombre</label>
                        <input type="text" name="name" id="edit_e_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Inicio</label>
                        <input type="date" name="start_date" id="edit_e_start" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Fin</label>
                        <input type="date" name="end_date" id="edit_e_end" class="form-control" required>
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
document.getElementById('editEvModal').addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    document.getElementById('editEvForm').action = '/events/' + btn.dataset.id;
    document.getElementById('edit_e_name').value  = btn.dataset.name  || '';
    document.getElementById('edit_e_start').value = btn.dataset.start || '';
    document.getElementById('edit_e_end').value   = btn.dataset.end   || '';
});
</script>
@endpush
