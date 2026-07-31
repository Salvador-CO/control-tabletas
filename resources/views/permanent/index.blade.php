@extends('layouts.app')

@section('title', 'Asignaciones Permanentes')
@section('page-title', 'Asignaciones Permanentes — Jefes y Personal Fijo')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <p class="text-muted mb-0">Dispositivos asignados indefinidamente. No participan en el pool de Exacer.</p>
    <a href="{{ route('permanent.create') }}" class="btn btn-primary-custom">
        <i class="bi bi-person-plus-fill me-2"></i>Nueva Asignación Fija
    </a>
</div>

{{-- ── Asignaciones Activas ── --}}
<div class="data-table mb-4">
    <div class="p-3 border-bottom d-flex align-items-center gap-2">
        <span class="stat-icon flex-shrink-0" style="width:32px;height:32px;background:#ede9fe;color:#7c3aed;border-radius:8px;font-size:.85rem;">
            <i class="bi bi-person-badge-fill"></i>
        </span>
        <h6 class="fw-semibold mb-0">Asignaciones Activas <span class="badge ms-1" style="background:#ede9fe;color:#7c3aed;">{{ $active->count() }}</span></h6>
    </div>

    @if($active->isEmpty())
        <div class="p-4 text-center text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            No hay asignaciones permanentes activas.
        </div>
    @else
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Persona</th>
                        <th>Cargo en la Asignación</th>
                        <th>Dispositivo</th>
                        <th>No. Serie</th>
                        <th>Desde</th>
                        <th>Notas</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($active as $pa)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $pa->staff->full_name ?? '—' }}</div>
                            <div class="text-muted small">{{ $pa->staff->location->name ?? '' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark fw-normal">{{ $pa->role }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $pa->device->brand ?? '?' }} {{ $pa->device->model ?? '' }}</div>
                            <div class="text-muted small">{{ $pa->device->category->name ?? '' }}</div>
                        </td>
                        <td>
                            <code class="bg-light px-2 py-1 rounded small">{{ $pa->device->serial_number ?? '—' }}</code>
                        </td>
                        <td class="small text-muted">{{ $pa->assigned_date->format('d/m/Y') }}</td>
                        <td class="small text-muted">{{ Str::limit($pa->notes, 40) ?: '—' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('permanent.show', $pa) }}" class="btn btn-sm btn-outline-primary" title="Ver detalle">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="{{ route('permanent.pdf', $pa) }}" class="btn btn-sm btn-outline-secondary" title="PDF" target="_blank">
                                    <i class="bi bi-file-pdf-fill"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- ── Historial: Liberadas ── --}}
@if($released->count() > 0)
<div class="data-table">
    <div class="p-3 border-bottom">
        <h6 class="fw-semibold mb-0 text-muted"><i class="bi bi-clock-history me-2"></i>Historial — Últimas Liberadas</h6>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Persona</th>
                    <th>Cargo</th>
                    <th>Dispositivo</th>
                    <th>Periodo</th>
                    <th>Motivo de liberación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($released as $pa)
                <tr class="text-muted">
                    <td>{{ $pa->staff->full_name ?? '—' }}</td>
                    <td><span class="badge bg-light text-muted fw-normal">{{ $pa->role }}</span></td>
                    <td>{{ $pa->device->brand ?? '' }} {{ $pa->device->model ?? '' }}
                        <div class="small"><code>{{ $pa->device->serial_number ?? '' }}</code></div>
                    </td>
                    <td class="small">
                        {{ $pa->assigned_date->format('d/m/Y') }} →
                        {{ $pa->released_date->format('d/m/Y') }}
                    </td>
                    <td class="small">{{ $pa->released_reason ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection
