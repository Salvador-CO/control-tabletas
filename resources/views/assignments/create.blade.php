@extends('layouts.app')

@section('title', 'Nuevo Vale de Resguardo')
@section('page-title', 'Nuevo Vale de Resguardo')

@section('content')

<form method="POST" action="{{ route('assignments.store') }}" id="assignmentForm">
@csrf
<div class="row g-3">

    {{-- ── Left: Assignment Details ── --}}
    <div class="col-12 col-lg-5">
        <div class="stat-card mb-3">
            <h6 class="fw-semibold mb-3"><i class="bi bi-info-circle-fill text-primary me-2"></i>Datos del Vale</h6>

            <div class="mb-3">
                <label class="form-label small fw-semibold text-muted">Evento / Exacer</label>
                <select name="event_id" class="form-select">
                    <option value="">— Sin evento específico —</option>
                    @foreach($events as $ev)
                        <option value="{{ $ev->id }}" {{ old('event_id') == $ev->id ? 'selected' : '' }}>
                            {{ $ev->name }}
                            @if($ev->start_date)({{ \Carbon\Carbon::parse($ev->start_date)->format('M Y') }})@endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold text-muted">Sede / Plantel *</label>
                <select name="location_id" class="form-select @error('location_id') is-invalid @enderror" required>
                    <option value="">Seleccionar sede…</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>
                            {{ $loc->name }}
                        </option>
                    @endforeach
                </select>
                @error('location_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold text-muted">Coordinador de Sede *</label>
                <select name="coordinator_id" class="form-select @error('coordinator_id') is-invalid @enderror" required>
                    <option value="">Seleccionar coordinador…</option>
                    @foreach($staff as $s)
                        <option value="{{ $s->id }}" {{ old('coordinator_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->full_name }} ({{ $s->role }})
                        </option>
                    @endforeach
                </select>
                @error('coordinator_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold text-muted">Persona que Entrega</label>
                <input type="text" name="delivery_person_name" class="form-control"
                       value="{{ old('delivery_person_name', 'MARCELA PEÑA ORDOÑEZ') }}"
                       placeholder="Nombre completo de quien entrega">
            </div>

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label small fw-semibold text-muted">Fecha de Entrega *</label>
                    <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                           value="{{ old('start_date', date('Y-m-d')) }}" required>
                    @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-6">
                    <label class="form-label small fw-semibold text-muted">Fecha de Devolución *</label>
                    <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                           value="{{ old('end_date') }}" required>
                    @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold text-muted">No. de Cargadores Incluidos</label>
                <input type="number" name="chargers_count" class="form-control" min="0"
                       value="{{ old('chargers_count', 0) }}">
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold text-muted">Observaciones</label>
                <textarea name="observations" class="form-control" rows="2"
                          placeholder="Condición de los equipos, acuerdos especiales…">{{ old('observations') }}</textarea>
            </div>
        </div>
    </div>

    {{-- ── Right: Device Selection ── --}}
    <div class="col-12 col-lg-7">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-semibold mb-0"><i class="bi bi-tablet-fill text-success me-2"></i>Seleccionar Dispositivos</h6>
                <span class="badge bg-success-subtle text-success fw-semibold" id="selectedCount">0 seleccionados</span>
            </div>

            @if($availableDevices->isEmpty())
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                    No hay dispositivos disponibles en este momento.
                </div>
            @else
                {{-- Búsqueda inline --}}
                <div class="input-group mb-3">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="deviceSearch" class="form-control" placeholder="Filtrar por serie o modelo…">
                </div>

                <div class="device-list" style="max-height: 380px; overflow-y: auto;">
                    @foreach($availableDevices as $device)
                    <div class="device-item border rounded-3 p-3 mb-2 cursor-pointer" data-device-id="{{ $device->id }}"
                         data-serial="{{ strtolower($device->serial_number) }}"
                         data-model="{{ strtolower($device->model) }}"
                         style="cursor:pointer; transition: all .2s;">
                        <div class="d-flex align-items-center gap-3">
                            <input type="checkbox" class="form-check-input device-checkbox flex-shrink-0"
                                   name="devices_check[]" value="{{ $device->id }}"
                                   style="width:20px;height:20px;">
                            <div class="flex-grow-1">
                                <div class="fw-semibold">
                                    {{ $device->brand }} {{ $device->model }}
                                    <span class="badge bg-light text-muted fw-normal ms-1 small">{{ $device->category->name ?? '' }}</span>
                                </div>
                                <div class="text-muted small">
                                    <i class="bi bi-upc me-1"></i><code>{{ $device->serial_number }}</code>
                                    @if($device->charger_details)
                                        <span class="ms-2 text-success"><i class="bi bi-plug-fill"></i> {{ $device->charger_details }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- Staff assignment per device --}}
                            <div class="staff-select" style="display:none; min-width:150px;">
                                <select class="form-select form-select-sm staff-selector" data-device-id="{{ $device->id }}">
                                    <option value="">Sin persona</option>
                                    @foreach($staff as $s)
                                        <option value="{{ $s->id }}">{{ $s->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Hidden inputs for selected devices --}}
                <div id="hiddenDeviceInputs"></div>
            @endif

            @error('devices')
                <div class="text-danger small mt-2"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex gap-2 mt-3">
            <a href="{{ route('assignments.index') }}" class="btn btn-outline-secondary flex-fill">
                <i class="bi bi-arrow-left me-1"></i>Cancelar
            </a>
            <button type="submit" class="btn btn-primary-custom flex-fill" id="submitBtn" disabled>
                <i class="bi bi-file-earmark-check-fill me-2"></i>Generar Vale de Resguardo
            </button>
        </div>
    </div>

</div>
</form>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('.device-checkbox');
    const hiddenContainer = document.getElementById('hiddenDeviceInputs');
    const countBadge = document.getElementById('selectedCount');
    const submitBtn  = document.getElementById('submitBtn');
    const searchInput = document.getElementById('deviceSearch');

    function updateHiddenInputs() {
        hiddenContainer.innerHTML = '';
        let count = 0;
        checkboxes.forEach(cb => {
            if (cb.checked) {
                count++;
                const id = cb.value;
                const staffSel = document.querySelector(`.staff-selector[data-device-id="${id}"]`);
                const idInput = document.createElement('input');
                idInput.type  = 'hidden';
                idInput.name  = `devices[${id}][id]`;
                idInput.value = id;
                hiddenContainer.appendChild(idInput);

                if (staffSel && staffSel.value) {
                    const staffInput = document.createElement('input');
                    staffInput.type  = 'hidden';
                    staffInput.name  = `devices[${id}][staff_id]`;
                    staffInput.value = staffSel.value;
                    hiddenContainer.appendChild(staffInput);
                }
            }
        });
        countBadge.textContent = count + ' seleccionado' + (count !== 1 ? 's' : '');
        submitBtn.disabled = count === 0;
    }

    // Click on device row toggles checkbox
    document.querySelectorAll('.device-item').forEach(item => {
        item.addEventListener('click', function (e) {
            if (e.target.tagName === 'SELECT' || e.target.tagName === 'OPTION') return;
            const cb = item.querySelector('.device-checkbox');
            cb.checked = !cb.checked;
            const staffDiv = item.querySelector('.staff-select');
            staffDiv.style.display = cb.checked ? 'block' : 'none';
            item.style.background  = cb.checked ? '#f0fdf4' : '';
            item.style.borderColor = cb.checked ? 'var(--primary)' : '';
            updateHiddenInputs();
        });
    });

    // Staff selectors update hidden inputs
    document.querySelectorAll('.staff-selector').forEach(sel => {
        sel.addEventListener('change', updateHiddenInputs);
    });

    // Search filter
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.device-item').forEach(item => {
                const match = item.dataset.serial.includes(q) || item.dataset.model.includes(q);
                item.style.display = match ? '' : 'none';
            });
        });
    }
});
</script>
@endpush
