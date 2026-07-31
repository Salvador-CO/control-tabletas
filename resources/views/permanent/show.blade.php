@extends('layouts.app')

@section('title', 'Asignación Permanente — ' . ($permanent->staff->full_name ?? ''))
@section('page-title', 'Asignación Permanente')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <a href="{{ route('permanent.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Regresar
    </a>
    <div class="d-flex gap-2">
        <a href="{{ route('permanent.pdf', $permanent) }}" class="btn btn-sm btn-outline-danger" target="_blank">
            <i class="bi bi-file-pdf-fill me-1"></i>Imprimir / PDF
        </a>
        @if(!$permanent->released_date)
            <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#releaseModal">
                <i class="bi bi-box-arrow-in-down me-1"></i>Liberar Dispositivo
            </button>
        @endif
    </div>
</div>

<div class="row g-3">
    {{-- Info Card --}}
    <div class="col-12 col-lg-5">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="stat-icon" style="width:52px;height:52px;background:#ede9fe;color:#7c3aed;border-radius:12px;font-size:1.4rem;">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div>
                    <div class="fw-bold fs-6">{{ $permanent->staff->full_name ?? '—' }}</div>
                    <div class="text-muted small">{{ $permanent->role }}</div>
                </div>
            </div>

            <dl style="font-size:.875rem;" class="mb-0">
                <dt class="text-muted small">Dispositivo Asignado</dt>
                <dd class="fw-semibold">
                    {{ $permanent->device->brand ?? '?' }} {{ $permanent->device->model ?? '' }}
                    <span class="badge bg-light text-muted fw-normal ms-1">{{ $permanent->device->category->name ?? '' }}</span>
                </dd>

                <dt class="text-muted small">No. de Serie</dt>
                <dd><code class="bg-light px-2 py-1 rounded">{{ $permanent->device->serial_number ?? '—' }}</code></dd>

                <dt class="text-muted small">Cargador</dt>
                <dd>{{ $permanent->device->charger_details ?: 'No especificado' }}</dd>

                <dt class="text-muted small">Fecha de Asignación</dt>
                <dd>{{ $permanent->assigned_date->format('d \d\e F \d\e Y') }}</dd>

                @if($permanent->released_date)
                <dt class="text-muted small">Fecha de Liberación</dt>
                <dd class="text-success">{{ $permanent->released_date->format('d \d\e F \d\e Y') }}</dd>

                <dt class="text-muted small">Motivo de Liberación</dt>
                <dd>{{ $permanent->released_reason }}</dd>
                @endif

                @if($permanent->notes)
                <dt class="text-muted small">Observaciones</dt>
                <dd>{{ $permanent->notes }}</dd>
                @endif

                <dt class="text-muted small">Estado</dt>
                <dd>
                    @if($permanent->released_date)
                        <span class="badge-status badge-completado">Liberado</span>
                    @else
                        <span class="badge-status" style="background:#ede9fe;color:#7c3aed;">Activo</span>
                    @endif
                </dd>
            </dl>
        </div>
    </div>

    {{-- Tiempo y duración --}}
    <div class="col-12 col-lg-7">
        <div class="stat-card mb-3">
            <h6 class="fw-semibold mb-3"><i class="bi bi-calendar-check-fill text-primary me-2"></i>Duración de la Asignación</h6>
            @php
                $since = $permanent->assigned_date;
                $until = $permanent->released_date ?? now();
                $days  = $since->diffInDays($until);
                $months = $since->diffInMonths($until);
            @endphp
            <div class="row g-3">
                <div class="col-6">
                    <div class="text-center p-3 rounded-3" style="background:#f8fafc;">
                        <div class="fw-bold fs-3 text-primary">{{ $days }}</div>
                        <div class="small text-muted">días asignado</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center p-3 rounded-3" style="background:#f8fafc;">
                        <div class="fw-bold fs-3" style="color:#7c3aed;">{{ $months }}</div>
                        <div class="small text-muted">meses</div>
                    </div>
                </div>
            </div>
            <div class="mt-3 small text-muted">
                Desde el <strong>{{ $permanent->assigned_date->format('d/m/Y') }}</strong>
                @if(!$permanent->released_date)
                    hasta hoy.
                @else
                    hasta el <strong>{{ $permanent->released_date->format('d/m/Y') }}</strong>.
                @endif
            </div>
        </div>

        @if(!$permanent->released_date)
        <div class="stat-card" style="border-left: 4px solid #f59e0b;">
            <h6 class="fw-semibold mb-2 text-warning"><i class="bi bi-exclamation-triangle-fill me-2"></i>Asignación Activa</h6>
            <p class="text-muted small mb-0">
                Este dispositivo está asignado permanentemente a
                <strong>{{ $permanent->staff->full_name }}</strong>.
                Para liberarlo y devolverlo al inventario disponible, usa el botón
                <strong>"Liberar Dispositivo"</strong>.
            </p>
        </div>
        @endif
    </div>
</div>

{{-- ── Modal: Liberar dispositivo ── --}}
@if(!$permanent->released_date)
<div class="modal fade" id="releaseModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background:#fef3c7; border-bottom:1px solid #fcd34d;">
                <h5 class="modal-title fw-semibold text-warning">
                    <i class="bi bi-box-arrow-in-down me-2"></i>Liberar Dispositivo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('permanent.release', $permanent) }}">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small">
                        Al liberar, el dispositivo
                        <strong>{{ $permanent->device->serial_number ?? '' }}</strong>
                        regresará al inventario como <strong>Disponible</strong>.
                    </p>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Fecha de Devolución *</label>
                        <input type="date" name="released_date" class="form-control"
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Motivo de Liberación *</label>
                        <input type="text" name="released_reason" class="form-control"
                               placeholder="Ej: Cambio de puesto, baja del personal, reasignación…" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning text-white fw-semibold">
                        <i class="bi bi-check-circle me-1"></i>Confirmar Liberación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection
