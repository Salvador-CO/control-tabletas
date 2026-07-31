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
                    <div class="stat-label">En Resguardo / Uso</div>
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
                    <div class="stat-value text-danger">{{ $pendingReturns }}</div>
                    <div class="stat-label">Pendientes de Devolución</div>
                </div>
                <div class="stat-icon" style="background:#fee2e2; color:#991b1b;">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Recent Assignments ── --}}
<div class="row g-3">
    <div class="col-12 col-xl-8">
        <div class="data-table">
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                <h6 class="fw-semibold mb-0"><i class="bi bi-file-earmark-check-fill text-success me-2"></i>Vales Recientes</h6>
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
                                <th>Coordinador</th>
                                <th>Periodo</th>
                                <th>Dispositivos</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentAssignments as $a)
                            <tr>
                                <td class="text-muted" style="font-size:.8rem;">VAL-{{ str_pad($a->id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td><strong>{{ $a->location->name ?? '—' }}</strong></td>
                                <td>{{ $a->coordinator->full_name ?? '—' }}</td>
                                <td style="font-size:.8rem;">
                                    {{ $a->start_date ? $a->start_date->format('d/m/Y') : '—' }}
                                    @if($a->end_date) — {{ $a->end_date->format('d/m/Y') }} @endif
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $a->items->count() }}</span>
                                    <span class="text-muted small">pzs.</span>
                                </td>
                                <td>
                                    <span class="badge-status badge-{{ $a->status }}">
                                        {{ ucfirst($a->status) }}
                                    </span>
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

    <div class="col-12 col-xl-4">
        {{-- Quick Actions --}}
        <div class="stat-card mb-3">
            <h6 class="fw-semibold mb-3"><i class="bi bi-lightning-fill text-warning me-2"></i>Acciones Rápidas</h6>
            <div class="d-grid gap-2">
                <a href="{{ route('assignments.create') }}" class="btn btn-primary-custom">
                    <i class="bi bi-plus-circle-fill me-2"></i>Nuevo Vale de Resguardo
                </a>
                <a href="{{ route('devices.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-tablet me-2"></i>Ver Inventario
                </a>
                <a href="{{ route('assignments.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-check2-square me-2"></i>Liberar Devoluciones
                </a>
            </div>
        </div>

        {{-- Device Status Summary --}}
        <div class="stat-card">
            <h6 class="fw-semibold mb-3"><i class="bi bi-pie-chart-fill text-primary me-2"></i>Estado del Inventario</h6>
            @php
                $total = $totalDevices > 0 ? $totalDevices : 1;
                $availPct  = round($availableDevices  / $total * 100);
                $inUsePct  = round($inUseDevices / $total * 100);
                $otherPct  = max(0, 100 - $availPct - $inUsePct);
            @endphp
            <div class="mb-2">
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">Disponibles</span>
                    <span class="fw-semibold text-success">{{ $availableDevices }}</span>
                </div>
                <div class="progress" style="height:8px; border-radius:99px;">
                    <div class="progress-bar bg-success" style="width:{{ $availPct }}%;"></div>
                </div>
            </div>
            <div class="mb-2">
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">En Resguardo/Uso</span>
                    <span class="fw-semibold text-warning">{{ $inUseDevices }}</span>
                </div>
                <div class="progress" style="height:8px; border-radius:99px;">
                    <div class="progress-bar bg-warning" style="width:{{ $inUsePct }}%;"></div>
                </div>
            </div>
            @if($otherPct > 0)
            <div>
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">Fijos / Mant.</span>
                    <span class="fw-semibold text-secondary">{{ $totalDevices - $availableDevices - $inUseDevices }}</span>
                </div>
                <div class="progress" style="height:8px; border-radius:99px;">
                    <div class="progress-bar bg-secondary" style="width:{{ $otherPct }}%;"></div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection