@extends('layouts.app')

@section('title', 'Personal')
@section('page-title', 'Gestión de Personal')

@section('content')
<div class="row g-3">
    {{-- Staff Table --}}
    <div class="col-12 col-xl-8">
        <div class="data-table">
            <div class="p-3 border-bottom">
                <h6 class="fw-semibold mb-0"><i class="bi bi-people-fill text-primary me-2"></i>{{ $staff->count() }} personas registradas</h6>
            </div>
            @if($staff->isEmpty())
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-person-x fs-1 d-block mb-2"></i>No hay personal registrado.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr><th>Nombre</th><th>Rol</th><th>Sede</th><th>Acciones</th></tr></thead>
                        <tbody>
                        @foreach($staff as $s)
                        <tr>
                            <td class="fw-semibold">{{ $s->full_name }}</td>
                            <td><span class="badge bg-light text-dark fw-normal">{{ $s->role }}</span></td>
                            <td>{{ $s->location->name ?? '—' }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal" data-bs-target="#editStaffModal"
                                    data-id="{{ $s->id }}" data-name="{{ $s->full_name }}"
                                    data-role="{{ $s->role }}" data-location="{{ $s->location_id }}">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <form method="POST" action="{{ route('staff.destroy', $s) }}" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar a {{ $s->full_name }}?')">
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

    {{-- Add Staff --}}
    <div class="col-12 col-xl-4">
        <div class="stat-card">
            <h6 class="fw-semibold mb-3"><i class="bi bi-person-plus-fill text-success me-2"></i>Registrar Personal</h6>
            <form method="POST" action="{{ route('staff.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Nombre Completo *</label>
                    <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror"
                           value="{{ old('full_name') }}" required>
                    @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Rol / Cargo *</label>
                    <input type="text" name="role" class="form-control" value="{{ old('role') }}"
                           placeholder="Ej: Coordinador Académico" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Sede (opcional)</label>
                    <select name="location_id" class="form-select">
                        <option value="">Sin sede</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary-custom w-100">
                    <i class="bi bi-save-fill me-2"></i>Guardar
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editStaffModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header"><h5 class="modal-title fw-semibold">Editar Personal</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" id="editStaffForm">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Nombre Completo</label>
                        <input type="text" name="full_name" id="edit_s_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Rol</label>
                        <input type="text" name="role" id="edit_s_role" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Sede</label>
                        <select name="location_id" id="edit_s_location" class="form-select">
                            <option value="">Sin sede</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('editStaffModal').addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    const form = document.getElementById('editStaffForm');
    form.action = '/staff/' + btn.dataset.id;
    document.getElementById('edit_s_name').value     = btn.dataset.name || '';
    document.getElementById('edit_s_role').value     = btn.dataset.role || '';
    document.getElementById('edit_s_location').value = btn.dataset.location || '';
});
</script>
@endpush
