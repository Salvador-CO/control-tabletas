@extends('layouts.app')

@section('title', 'Vale VAL-' . str_pad($assignment->id, 4, '0', STR_PAD_LEFT))
@section('page-title', 'Vale de Resguardo — ' . ($assignment->location->name ?? ''))

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <a href="{{ route('assignments.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Regresar
    </a>
    <div class="d-flex gap-2">
        @if($assignment->status !== 'cancelado')
        <button class="btn btn-sm btn-outline-success"
                data-bs-toggle="modal" data-bs-target="#addDevicesModal">
            <i class="bi bi-plus-circle-fill me-1"></i>Agregar Tabletas
        </button>
        @endif
        <a href="{{ route('assignments.pdf', $assignment) }}" class="btn btn-sm btn-outline-danger" target="_blank">
            <i class="bi bi-file-pdf-fill me-1"></i>Descargar / Imprimir PDF
        </a>
    </div>
</div>

<div class="row g-3">
    {{-- ── Assignment Info ── --}}
    <div class="col-12 col-lg-4">
        <div class="stat-card mb-3">
            <h6 class="fw-semibold mb-3"><i class="bi bi-info-circle-fill text-primary me-2"></i>Información del Vale</h6>
            <dl class="mb-0" style="font-size:.875rem;">
                <dt class="text-muted small">Folio</dt>
                <dd class="fw-bold">VAL-{{ str_pad($assignment->id, 4, '0', STR_PAD_LEFT) }}</dd>

                <dt class="text-muted small">Sede</dt>
                <dd>{{ $assignment->location->name ?? '—' }}</dd>

                <dt class="text-muted small">Coordinador / Responsable</dt>
                <dd>{{ $assignment->coordinator->full_name ?? '—' }}</dd>

                <dt class="text-muted small">Entregó</dt>
                <dd>{{ $assignment->delivery_person_name ?? '—' }}</dd>

                <dt class="text-muted small">Periodo</dt>
                <dd>
                    {{ $assignment->start_date?->format('d/m/Y') ?? '—' }}
                    @if($assignment->end_date)
                        — {{ $assignment->end_date->format('d/m/Y') }}
                    @else
                        — <span class="badge" style="background:#fef3c7;color:#92400e;font-size:.7rem;"><i class="bi bi-infinity me-1"></i>Indefinido</span>
                    @endif
                </dd>

                @if($assignment->event)
                <dt class="text-muted small">Evento</dt>
                <dd>{{ $assignment->event->name }}</dd>
                @endif

                <dt class="text-muted small">Cargadores</dt>
                <dd>{{ $assignment->chargers_count }}</dd>

                <dt class="text-muted small">Estado</dt>
                <dd><span class="badge-status badge-{{ $assignment->status }}">{{ ucfirst($assignment->status) }}</span></dd>

                @if($assignment->observations)
                <dt class="text-muted small">Observaciones</dt>
                <dd>{{ $assignment->observations }}</dd>
                @endif
            </dl>
        </div>

        {{-- Progress --}}
        @php
            $total    = $assignment->items->count();
            $returned = $assignment->items->where('is_returned', true)->count();
            $pct      = $total > 0 ? round($returned / $total * 100) : 0;
        @endphp
        <div class="stat-card">
            <h6 class="fw-semibold mb-2"><i class="bi bi-arrow-return-left text-success me-2"></i>Devoluciones</h6>
            <div class="stat-value text-success">{{ $returned }}<span class="fs-5 text-muted fw-normal">/{{ $total }}</span></div>
            <div class="progress mt-2" style="height:10px; border-radius:99px;">
                <div class="progress-bar bg-success" style="width:{{ $pct }}%; transition: width .5s;"></div>
            </div>
            <div class="small text-muted mt-1">{{ $pct }}% devuelto</div>
        </div>
    </div>

    {{-- ── Devices List with Liberation ── --}}
    <div class="col-12 col-lg-8">
        <div class="data-table">
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-semibold mb-0"><i class="bi bi-tablet-fill text-primary me-2"></i>Dispositivos — Marcar Devolución</h6>
                <span class="small text-muted">Clic en ✓ para liberar</span>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Dispositivo</th>
                            <th>No. Serie</th>
                            <th>Personal</th>
                            <th>Cargo en este Periodo</th>
                            <th>Devuelto</th>
                            <th class="text-center">Liberar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assignment->items as $item)
                        <tr id="row-{{ $item->id }}" class="{{ $item->is_returned ? 'bg-success bg-opacity-10' : '' }}">
                            <td>
                                <div class="fw-semibold">{{ $item->device->brand ?? '?' }} {{ $item->device->model ?? '' }}</div>
                                <div class="small text-muted">{{ $item->device->category->name ?? '' }}</div>
                            </td>
                            <td>
                                <code class="bg-light px-2 py-1 rounded small">{{ $item->device->serial_number ?? '—' }}</code>
                            </td>
                            <td>{{ $item->staff->full_name ?? '—' }}</td>
                            <td>
                                @if($item->role_in_period)
                                    <span class="badge bg-light text-dark fw-normal small">{{ $item->role_in_period }}</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                @if($item->is_returned && $item->returned_at)
                                    <span class="small text-success">
                                        <i class="bi bi-check-circle-fill me-1"></i>
                                        {{ $item->returned_at->format('d/m/Y H:i') }}
                                    </span>
                                @else
                                    <span class="small text-muted">Pendiente</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button"
                                        class="liberation-btn {{ $item->is_returned ? 'returned' : '' }}"
                                        data-item-id="{{ $item->id }}"
                                        title="{{ $item->is_returned ? 'Clic para desmarcar' : 'Clic para marcar como devuelto' }}">
                                    <i class="bi bi-check-lg" style="font-size:1rem;"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ── Modal: Agregar Tabletas ── --}}
<div class="modal fade" id="addDevicesModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">
                    <i class="bi bi-plus-circle-fill text-success me-2"></i>
                    Agregar Tabletas al Vale VAL-{{ str_pad($assignment->id, 4, '0', STR_PAD_LEFT) }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('assignments.add-devices', $assignment) }}" id="addDevicesForm">
                @csrf
                <div class="modal-body">

                    @php
                        $alreadyIds = $assignment->items->pluck('device_id')->toArray();
                    @endphp

                    @if($availableDevices->isEmpty())
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No hay dispositivos disponibles para agregar.
                        </div>
                    @else
                        {{-- Buscador --}}
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="addDeviceSearch" class="form-control" placeholder="Filtrar por serie o modelo…">
                        </div>

                        <div style="max-height:380px; overflow-y:auto;">
                            @foreach($availableDevices as $device)
                            <div class="add-device-item border rounded-3 p-2 mb-2 d-flex align-items-center gap-3"
                                 data-serial="{{ strtolower($device->serial_number) }}"
                                 data-model="{{ strtolower($device->model) }}"
                                 style="cursor:pointer; transition: background .15s;">
                                <input type="checkbox" class="form-check-input add-device-cb flex-shrink-0"
                                       style="width:20px;height:20px;"
                                       name="devices[{{ $device->id }}][id]"
                                       value="{{ $device->id }}">
                                <div class="flex-grow-1">
                                    <div class="fw-semibold small">
                                        {{ $device->brand }} {{ $device->model }}
                                        <span class="text-muted fw-normal ms-1">· {{ $device->serial_number }}</span>
                                    </div>
                                    <div class="text-muted" style="font-size:.75rem;">{{ $device->category->name ?? '' }}</div>
                                </div>
                                <div class="add-extra-fields row g-1" style="width:310px; display:none!important;">
                                    <div class="col-6">
                                        <select name="devices[{{ $device->id }}][staff_id]"
                                                class="form-select form-select-sm add-staff-sel">
                                            <option value="">Sin persona</option>
                                            @foreach($staff as $s)
                                                <option value="{{ $s->id }}">{{ $s->full_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <input type="text"
                                               name="devices[{{ $device->id }}][role]"
                                               class="form-control form-control-sm"
                                               placeholder="Cargo en el periodo">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="small text-muted mt-2">
                            <span id="addSelectedCount">0</span> dispositivo(s) seleccionado(s)
                        </div>
                    @endif

                    <div id="addDevicesError" class="alert alert-danger mt-2 d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom" id="addDevicesSubmit" disabled>
                        <i class="bi bi-plus-circle-fill me-2"></i>Agregar al Vale
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
/* ── Liberar dispositivos ── */
document.querySelectorAll('.liberation-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const itemId = this.dataset.itemId;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        this.disabled = true;

        fetch(`/assignments/items/${itemId}/toggle-liberation`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const row = document.getElementById(`row-${itemId}`);
                if (data.is_returned) {
                    this.classList.add('returned');
                    row.classList.add('bg-success', 'bg-opacity-10');
                } else {
                    this.classList.remove('returned');
                    row.classList.remove('bg-success', 'bg-opacity-10');
                }
                setTimeout(() => location.reload(), 600);
            }
        })
        .catch(() => {
            alert('Error al actualizar. Intente de nuevo.');
            this.disabled = false;
        });
    });
});

/* ── Modal Agregar Tabletas ── */
document.addEventListener('DOMContentLoaded', function () {
    const searchInput   = document.getElementById('addDeviceSearch');
    const countSpan     = document.getElementById('addSelectedCount');
    const submitBtn     = document.getElementById('addDevicesSubmit');

    function updateCount() {
        const checked = document.querySelectorAll('.add-device-cb:checked').length;
        if (countSpan) countSpan.textContent = checked;
        if (submitBtn) submitBtn.disabled = checked === 0;
    }

    // Buscador
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.add-device-item').forEach(item => {
                const match = item.dataset.serial.includes(q) || item.dataset.model.includes(q);
                item.style.display = match ? '' : 'none';
            });
        });
    }

    // Checkbox toggle: mostrar campos extra + contar
    document.querySelectorAll('.add-device-cb').forEach(cb => {
        cb.addEventListener('change', function () {
            const item   = this.closest('.add-device-item');
            const fields = item.querySelector('.add-extra-fields');
            if (this.checked) {
                item.style.background = '#f0fdf4';
                fields.style.setProperty('display', 'flex', 'important');
                // Init Tom Select en el staff-selector
                const sel = fields.querySelector('.add-staff-sel');
                if (sel && !sel.tomselect) {
                    new TomSelect(sel, {
                        placeholder: 'Sin persona',
                        allowEmptyOption: true,
                        sortField: { field: 'text', direction: 'asc' },
                    });
                }
            } else {
                item.style.background = '';
                fields.style.setProperty('display', 'none', 'important');
            }
            updateCount();
        });

        // Clic en la tarjeta (fuera del checkbox)
        const item = cb.closest('.add-device-item');
        item.addEventListener('click', function (e) {
            if (e.target === cb || e.target.closest('.add-extra-fields')) return;
            cb.checked = !cb.checked;
            cb.dispatchEvent(new Event('change'));
        });
    });
});
</script>
@endpush
