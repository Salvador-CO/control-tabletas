@extends('layouts.app')

@section('title', 'Nueva Asignación Permanente')
@section('page-title', 'Nueva Asignación Permanente')

@section('content')
<div class="row g-3 justify-content-center">
    <div class="col-12 col-lg-7">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-2 mb-4">
                <div class="stat-icon flex-shrink-0" style="background:#ede9fe;color:#7c3aed;border-radius:10px;width:44px;height:44px;">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0">Asignar dispositivo de forma permanente</h5>
                    <p class="text-muted small mb-0">Para jefes de departamento y personal con tableta fija indefinida.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('permanent.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Persona (Jefe / Titular) *</label>
                    <select name="staff_id" class="form-select @error('staff_id') is-invalid @enderror" required>
                        <option value="">Seleccionar persona…</option>
                        @foreach($staff as $s)
                            <option value="{{ $s->id }}"
                                    data-last-role="{{ $s->lastKnownRole() }}"
                                    {{ old('staff_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->full_name }}
                                @if($s->role) ({{ $s->role }}) @endif
                            </option>
                        @endforeach
                    </select>
                    @error('staff_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">
                        Cargo en esta Asignación *
                        <span class="text-muted fw-normal">(el cargo real en este momento)</span>
                    </label>
                    <input type="text" name="role" id="role_input"
                           class="form-control @error('role') is-invalid @enderror"
                           value="{{ old('role') }}"
                           placeholder="Ej: Jefe de Departamento Académico" required>
                    @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Dispositivo a Asignar *</label>
                    @if($availableDevices->isEmpty())
                        <div class="alert alert-warning py-2 small">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            No hay dispositivos disponibles para asignación permanente en este momento.
                        </div>
                    @else
                        <select name="device_id" class="form-select @error('device_id') is-invalid @enderror" required>
                            <option value="">Seleccionar dispositivo…</option>
                            @foreach($availableDevices as $d)
                                <option value="{{ $d->id }}" {{ old('device_id') == $d->id ? 'selected' : '' }}>
                                    [{{ $d->category->name ?? '' }}] {{ $d->brand }} {{ $d->model }}
                                    — Serie: {{ $d->serial_number }}
                                    @if($d->charger_details) | {{ $d->charger_details }} @endif
                                </option>
                            @endforeach
                        </select>
                        @error('device_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Fecha de Asignación *</label>
                    <input type="date" name="assigned_date"
                           class="form-control @error('assigned_date') is-invalid @enderror"
                           value="{{ old('assigned_date', date('Y-m-d')) }}" required>
                    @error('assigned_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold text-muted">Observaciones / Estado del equipo</label>
                    <textarea name="notes" class="form-control" rows="3"
                              placeholder="Estado físico del equipo, accesorios incluidos, condiciones especiales…">{{ old('notes') }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('permanent.index') }}" class="btn btn-outline-secondary flex-fill">
                        <i class="bi bi-arrow-left me-1"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary-custom flex-fill" {{ $availableDevices->isEmpty() ? 'disabled' : '' }}>
                        <i class="bi bi-person-badge-fill me-2"></i>Registrar Asignación Permanente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Prellenar el cargo cuando se selecciona una persona
document.querySelector('select[name="staff_id"]').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const lastRole = selected.dataset.lastRole || '';
    const roleInput = document.getElementById('role_input');
    if (lastRole && !roleInput.value) {
        roleInput.value = lastRole;
    }
});
</script>
@endpush
