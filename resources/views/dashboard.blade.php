@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- ── Stat Cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-value">{{ $totalDevices }}</div>
                    <div class="stat-label">Total Dispositivos</div>
                </div>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-tablet-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-value text-success">{{ $availableDevices }}</div>
                    <div class="stat-label">Disponibles</div>
                </div>
                <div class="stat-icon" style="background:#d1fae5; color:#065f46;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-value text-warning">{{ $inUseDevices }}</div>
                    <div class="stat-label">En Resguardo Exacer</div>
                </div>
                <div class="stat-icon" style="background:#fef3c7; color:#92400e;">
                    <i class="bi bi-box-arrow-up-right"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-value" style="color:#7c3aed;">{{ $fixedDevices }}</div>
                    <div class="stat-label">Asignadas a Jefes</div>
                </div>
                <div class="stat-icon" style="background:#ede9fe; color:#7c3aed;">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Pending returns alert ── --}}
@if($pendingReturns > 0)
<div class="alert d-flex align-items-center gap-2 mb-3" style="background:#fef3c7; border-left:4px solid #f59e0b; border-radius:10px; color:#92400e;">
    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
    <span><strong>{{ $pendingReturns }}</strong> tableta(s) de Exacer pendientes de devolución.</span>
    <a href="{{ route('assignments.index') }}" class="ms-auto btn btn-sm" style="background:#f59e0b; color:#fff; border:none; border-radius:6px;">
        Ver vales activos
    </a>
</div>
@endif

<div class="row g-3">
    {{-- ── Recent Exacer Assignments ── --}}
    <div class="col-12 col-xl-7">
        <div class="data-table">
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                <h6 class="fw-semibold mb-0"><i class="bi bi-file-earmark-check-fill text-success me-2"></i>Vales Exacer Recientes</h6>
                <a href="{{ route('assignments.index') }}" class="btn btn-sm btn-outline-secondary">Ver todos</a>
            </div>
            @if($recentAssignments->isEmpty())
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    No hay vales de resguardo aún.
                    <div class="mt-2">
                        <a href="{{ route('assignments.create') }}" class="btn btn-sm btn-primary-custom">
                            <i class="bi bi-plus-circle me-1"></i>Crear primer vale
                        </a>
                    </div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Sede</th>
                                <th>Periodo</th>
                                <th>Tabletas</th>
                                <th>Devueltas</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentAssignments as $a)
                            @php
                                $total    = $a->items->count();
                                $returned = $a->items->where('is_returned', true)->count();
                            @endphp
                            <tr>
                                <td class="text-muted small">VAL-{{ str_pad($a->id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td><strong>{{ $a->location->name ?? '—' }}</strong></td>
                                <td class="small text-muted">
                                    {{ $a->start_date?->format('d/m/Y') ?? '—' }}
                                </td>
                                <td><span class="fw-semibold">{{ $total }}</span> <span class="text-muted small">pzs.</span></td>
                                <td>
                                    @if($total > 0)
                                        <div class="progress" style="height:6px; width:60px; border-radius:99px;">
                                            <div class="progress-bar bg-success" style="width:{{ ($returned/$total)*100 }}%"></div>
                                        </div>
                                        <div class="small text-muted">{{ $returned }}/{{ $total }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge-status badge-{{ $a->status }}">{{ ucfirst($a->status) }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('assignments.show', $a) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="col-12 col-xl-5">
        {{-- Quick Actions --}}
        <div class="stat-card mb-3">
            <h6 class="fw-semibold mb-3"><i class="bi bi-lightning-fill text-warning me-2"></i>Acciones Rápidas</h6>
            <div class="d-grid gap-2">
                <a href="{{ route('assignments.create') }}" class="btn btn-primary-custom">
                    <i class="bi bi-plus-circle-fill me-2"></i>Nuevo Vale Exacer
                </a>
                <a href="{{ route('permanent.create') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-person-badge-fill me-2"></i>Nueva Asignación Fija (Jefe)
                </a>
                <a href="{{ route('devices.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-tablet me-2"></i>Ver Inventario
                </a>
            </div>
        </div>

        {{-- Permanent Assignments (Jefes) --}}
        <div class="data-table">
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                <h6 class="fw-semibold mb-0" style="font-size:.88rem;">
                    <i class="bi bi-person-badge-fill me-2" style="color:#7c3aed;"></i>Asignadas a Jefes
                </h6>
                <a href="{{ route('permanent.index') }}" class="btn btn-sm btn-outline-secondary">Ver todas</a>
            </div>
            @if($permanentActive->isEmpty())
                <div class="p-3 text-center text-muted small">
                    <i class="bi bi-person-badge fs-2 d-block mb-1"></i>
                    Sin asignaciones permanentes activas.
                </div>
            @else
                @foreach($permanentActive as $pa)
                <div class="d-flex align-items-center gap-2 px-3 py-2 border-bottom">
                    <div class="stat-icon flex-shrink-0" style="width:36px;height:36px;background:#ede9fe;color:#7c3aed;border-radius:8px;font-size:.85rem;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="fw-semibold small text-truncate">{{ $pa->staff->full_name ?? '—' }}</div>
                        <div class="text-muted" style="font-size:.72rem;">
                            {{ $pa->role }} ·
                            <code class="bg-light px-1 rounded">{{ $pa->device->serial_number ?? '—' }}</code>
                        </div>
                    </div>
                    <a href="{{ route('permanent.show', $pa) }}" class="btn btn-sm btn-outline-secondary flex-shrink-0">
                        <i class="bi bi-eye"></i>
                    </a>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

@endsection