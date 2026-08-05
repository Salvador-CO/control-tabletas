@extends('layouts.app')

@section('title', 'Nuevo Vale de Resguardo Exacer')
@section('page-title', 'Nuevo Vale de Resguardo — Exacer')

@section('content')

{{-- Banner: datos del periodo anterior --}}
@if($previousItems->count() > 0)
<div class="alert d-flex align-items-start gap-3 mb-3" style="background:#dbeafe; border-left:4px solid #3b82f6; border-radius:10px; color:#1e40af;">
    <i class="bi bi-clock-history fs-5 flex-shrink-0 mt-1"></i>
    <div>
        <div class="fw-semibold">Datos del periodo anterior disponibles</div>
        <div class="small">Se encontraron <strong>{{ $previousItems->count() }}</strong> dispositivos del último vale de esta sede que siguen disponibles. Se han preseleccionado con el cargo del periodo anterior — puedes modificarlos.</div>
    </div>
</div>
@endif

<form method="POST" action="{{ route('assignments.store') }}" id="assignmentForm">
    @csrf
    <div class="row g-3">

        {{-- ── Left: Assignment Details ── --}}
        <div class="col-12 col-lg-5">
            <div class="stat-card mb-3">
                <h6 class="fw-semibold mb-3"><i class="bi bi-info-circle-fill text-primary me-2"></i>Datos del Vale</h6>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Evento / Exacer</label>
                    <select name="event_id" id="event_select" class="form-select">
                        <option value="" data-start="" data-end="">— Sin evento específico —</option>
                        @foreach($events as $ev)
                        <option value="{{ $ev->id }}"
                            data-start="{{ $ev->start_date }}"
                            data-end="{{ $ev->end_date }}"
                            {{ old('event_id') == $ev->id ? 'selected' : '' }}>
                            {{ $ev->name }}{{ $ev->start_date ? ' (' . \Carbon\Carbon::parse($ev->start_date)->format('M Y') . ')' : '' }}{{ !$ev->end_date ? ' — Indefinido' : '' }}
                        </option>
                        @endforeach
                    </select>
                    <div class="form-text text-muted" id="event_date_hint" style="display:none;">
                        <i class="bi bi-info-circle me-1"></i>Fechas cargadas del evento seleccionado. Puedes modificarlas.
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Sede / Plantel *</label>
                    <select name="location_id" id="location_select"
                        class="form-select @error('location_id') is-invalid @enderror" required>
                        <option value="">Seleccionar sede…</option>
                        @foreach($locations as $loc)
                        <option value="{{ $loc->id }}"
                            {{ (old('location_id') ?? $previousLocationId) == $loc->id ? 'selected' : '' }}>
                            {{ $loc->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('location_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="mt-1">
                        <a href="#" id="loadPreviousBtn" class="small text-primary" style="display:none;">
                            <i class="bi bi-clock-history me-1"></i>Cargar datos del periodo anterior para esta sede
                        </a>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Coordinador o responsable directo *</label>
                    <select name="coordinator_id" id="coordinator_select" class="form-select @error('coordinator_id') is-invalid @enderror" required>
                        <option value="">Seleccionar coordinador…</option>
                        @foreach($staff as $s)
                        <option value="{{ $s->id }}" {{ old('coordinator_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->full_name }}
                            @if($s->role)({{ $s->role }})@endif
                        </option>
                        @endforeach
                    </select>
                    @error('coordinator_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Persona que Entrega</label>
                    <input type="text" name="delivery_person_name" class="form-control"
                        value="{{ old('delivery_person_name', 'MARCELA PEÑA ORDOÑEZ') }}">
                </div>

                <div class="row g-2 mb-1">
                    <div class="col-6">
                        <label class="form-label small fw-semibold text-muted">Fecha de Entrega *</label>
                        <input type="date" name="start_date" id="start_date_input"
                            class="form-control @error('start_date') is-invalid @enderror"
                            value="{{ old('start_date', date('Y-m-d')) }}" required>
                        @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold text-muted">Fecha de Devolución</label>
                        {{-- Checkbox indefinido --}}
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" id="end_indefinite"
                                {{ !old('end_date') && old('end_indefinite') ? 'checked' : '' }}>
                            <label class="form-check-label" style="font-size:.75rem;color:#6b7280;" for="end_indefinite">
                                <i class="bi bi-infinity me-1 text-warning"></i><strong>Indefinido</strong>
                            </label>
                        </div>
                        <input type="date" name="end_date" id="end_date_input"
                            class="form-control @error('end_date') is-invalid @enderror"
                            value="{{ old('end_date') }}"
                            {{ old('end_indefinite') ? 'disabled' : '' }}>
                        @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text" style="font-size:.72rem;color:#92400e;" id="end_indefinite_hint"
                            style="{{ old('end_indefinite') ? '' : 'display:none;' }}">
                            <i class="bi bi-infinity me-1"></i>Se regresa al terminar el evento
                        </div>
                    </div>
                </div>
                <div class="mb-3"></div>

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
                <div class="input-group mb-3">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="deviceSearch" class="form-control" placeholder="Filtrar por serie o modelo…">
                </div>

                <div class="device-list" style="max-height: 420px; overflow-y: auto;">
                    @foreach($availableDevices as $device)
                    @php
                    // Buscar si este dispositivo viene del periodo anterior
                    $prevItem = $previousItems->firstWhere('device_id', $device->id);
                    @endphp
                    <div class="device-item border rounded-3 p-3 mb-2"
                        data-device-id="{{ $device->id }}"
                        data-serial="{{ strtolower($device->serial_number) }}"
                        data-model="{{ strtolower($device->model) }}"
                        style="cursor:pointer; transition: all .2s;
                                {{ $prevItem ? 'background:#f0fdf4; border-color:var(--primary)!important;' : '' }}">
                        <div class="d-flex align-items-start gap-3">
                            <input type="checkbox" class="form-check-input device-checkbox flex-shrink-0 mt-1"
                                value="{{ $device->id }}"
                                style="width:20px;height:20px;"
                                {{ $prevItem ? 'checked' : '' }}>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">
                                    {{ $device->brand }} {{ $device->model }}
                                    <span class="badge bg-light text-muted fw-normal ms-1 small">{{ $device->category->name ?? '' }}</span>
                                    @if($prevItem)
                                    <span class="badge ms-1" style="background:#d1fae5;color:#065f46;font-size:.65rem;">periodo anterior</span>
                                    @endif
                                </div>
                                <div class="text-muted small">
                                    <i class="bi bi-upc me-1"></i><code>{{ $device->serial_number }}</code>
                                    @if($device->charger_details)
                                    <span class="ms-2 text-success"><i class="bi bi-plug-fill"></i> {{ $device->charger_details }}</span>
                                    @endif
                                </div>

                                {{-- Campos de persona y cargo (visibles solo cuando está seleccionado) --}}
                                <div class="staff-fields mt-2 row g-2" style="{{ $prevItem ? '' : 'display:none!important;' }}">
                                    <div class="col-6">
                                        <select class="form-select form-select-sm staff-selector"
                                            data-device-id="{{ $device->id }}">
                                            <option value="">Sin persona asignada</option>
                                            @foreach($staff as $s)
                                            <option value="{{ $s->id }}"
                                                data-last-role="{{ $s->lastKnownRole() }}"
                                                {{ ($prevItem && $prevItem->staff_id == $s->id) ? 'selected' : '' }}>
                                                {{ $s->full_name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <input type="text" class="form-control form-control-sm role-input"
                                            data-device-id="{{ $device->id }}"
                                            placeholder="Cargo en este periodo"
                                            value="{{ $prevItem?->role_in_period ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

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
                <button type="submit" class="btn btn-primary-custom flex-fill" id="submitBtn">
                    <i class="bi bi-file-earmark-check-fill me-2"></i>Generar Vale de Resguardo
                </button>
            </div>
        </div>

    </div>
</form>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hiddenContainer = document.getElementById('hiddenDeviceInputs');
        const countBadge = document.getElementById('selectedCount');
        const submitBtn = document.getElementById('submitBtn');
        const searchInput = document.getElementById('deviceSearch');

        function updateHiddenInputs() {
            hiddenContainer.innerHTML = '';
            let count = 0;

            document.querySelectorAll('.device-checkbox:checked').forEach(cb => {
                count++;
                const id = cb.value;
                const staffSel = document.querySelector(`.staff-selector[data-device-id="${id}"]`);
                const roleInput = document.querySelector(`.role-input[data-device-id="${id}"]`);

                const addHidden = (name, value) => {
                    const el = document.createElement('input');
                    el.type = 'hidden';
                    el.name = name;
                    el.value = value;
                    hiddenContainer.appendChild(el);
                };

                addHidden(`devices[${id}][id]`, id);
                if (staffSel?.value) addHidden(`devices[${id}][staff_id]`, staffSel.value);
                if (roleInput?.value) addHidden(`devices[${id}][role]`, roleInput.value);
            });

            countBadge.textContent = count + ' seleccionado' + (count !== 1 ? 's' : '');
            submitBtn.disabled = count === 0;
        }

        // Click en tarjeta de dispositivo
        document.querySelectorAll('.device-item').forEach(item => {
            item.addEventListener('click', function(e) {
                if (['SELECT', 'OPTION', 'INPUT'].includes(e.target.tagName)) return;
                const cb = item.querySelector('.device-checkbox');
                cb.checked = !cb.checked;
                toggleDeviceItem(item, cb.checked);
                updateHiddenInputs();
            });

            // Checkboxes directos
            item.querySelector('.device-checkbox').addEventListener('change', function() {
                toggleDeviceItem(item, this.checked);
                updateHiddenInputs();
            });
        });

        function toggleDeviceItem(item, checked) {
            const fields = item.querySelector('.staff-fields');
            fields.style.display = checked ? 'flex' : 'none';
            item.style.background = checked ? '#f0fdf4' : '';
            item.style.borderColor = checked ? 'var(--primary)' : '';

            // Inicializar Tom Select en el staff-selector al activar por primera vez
            if (checked) {
                const sel = item.querySelector('.staff-selector');
                if (sel && !sel.tomselect) {
                    new TomSelect(sel, {
                        placeholder: 'Sin persona asignada',
                        allowEmptyOption: true,
                        sortField: {
                            field: 'text',
                            direction: 'asc'
                        },
                        onChange(value) {
                            const deviceId = sel.dataset.deviceId;
                            const roleInput = document.querySelector(`.role-input[data-device-id="${deviceId}"]`);
                            const opt = sel.options[sel.selectedIndex];
                            if (opt && opt.dataset.lastRole && roleInput && !roleInput.value) {
                                roleInput.value = opt.dataset.lastRole;
                            }
                            updateHiddenInputs();
                        }
                    });
                }
            }
        }

        document.querySelectorAll('.role-input').forEach(input => {
            input.addEventListener('input', updateHiddenInputs);
        });

        // Buscador
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const q = this.value.toLowerCase();
                document.querySelectorAll('.device-item').forEach(item => {
                    const match = item.dataset.serial.includes(q) || item.dataset.model.includes(q);
                    item.style.display = match ? '' : 'none';
                });
            });
        }

        // ── Tom Select: selects principales ──────────────────────────────

        // Helpers para el campo de fecha fin
        const startDateInput = document.getElementById('start_date_input');
        const endDateInput = document.getElementById('end_date_input');
        const endIndefinite = document.getElementById('end_indefinite');
        const endIndefiniteHint = document.getElementById('end_indefinite_hint');
        const eventDateHint = document.getElementById('event_date_hint');

        function setIndefinite(on) {
            endIndefinite.checked = on;
            endDateInput.disabled = on;
            endIndefiniteHint.style.display = on ? '' : 'none';
            if (on) endDateInput.value = '';
        }

        // Checkbox indefinido
        endIndefinite.addEventListener('change', function() {
            setIndefinite(this.checked);
        });

        // Tom Select — Evento: al seleccionar, rellena fechas
        new TomSelect('#event_select', {
            placeholder: '— Sin evento específico —',
            allowEmptyOption: true,
            sortField: {
                field: 'text',
                direction: 'asc'
            },
            onChange(value) {
                const opt = document.querySelector(`#event_select option[value="${value}"]`);
                if (opt && value) {
                    const evStart = opt.dataset.start || '';
                    const evEnd = opt.dataset.end || '';

                    if (evStart) startDateInput.value = evStart;

                    if (evEnd) {
                        setIndefinite(false);
                        endDateInput.value = evEnd;
                    } else {
                        setIndefinite(true);
                    }

                    eventDateHint.style.display = '';
                } else {
                    eventDateHint.style.display = 'none';
                }
            }
        });

        new TomSelect('#location_select', {
            placeholder: 'Seleccionar sede…',
            allowEmptyOption: true,
            sortField: {
                field: 'text',
                direction: 'asc'
            },
            onChange(value) {
                const btn = document.getElementById('loadPreviousBtn');
                if (btn) {
                    if (value) {
                        btn.style.display = 'inline';
                        btn.href = `/assignments/create?location_id=${value}`;
                    } else {
                        btn.style.display = 'none';
                    }
                }
            }
        });

        new TomSelect('#coordinator_select', {
            placeholder: 'Buscar coordinador…',
            allowEmptyOption: true,
            sortField: {
                field: 'text',
                direction: 'asc'
            },
        });

        // ── Tom Select: staff-selectors por dispositivo ───────────────────
        // Se inicializa en todos los que ya están visibles (periodo anterior)
        document.querySelectorAll('.staff-selector').forEach(sel => {
            if (!sel.tomselect) {
                new TomSelect(sel, {
                    placeholder: 'Sin persona asignada',
                    allowEmptyOption: true,
                    sortField: {
                        field: 'text',
                        direction: 'asc'
                    },
                    onChange(value) {
                        const deviceId = sel.dataset.deviceId;
                        const roleInput = document.querySelector(`.role-input[data-device-id="${deviceId}"]`);
                        const opt = sel.options[sel.selectedIndex];
                        if (opt && opt.dataset.lastRole && roleInput && !roleInput.value) {
                            roleInput.value = opt.dataset.lastRole;
                        }
                        updateHiddenInputs();
                    }
                });
            }
        });

        // Init: calcular conteo inicial
        updateHiddenInputs();

        // Init: mostrar campos de las tarjetas pre-seleccionadas
        document.querySelectorAll('.device-checkbox:checked').forEach(cb => {
            const item = cb.closest('.device-item');
            if (item) toggleDeviceItem(item, true);
        });
    });
</script>
@endpush