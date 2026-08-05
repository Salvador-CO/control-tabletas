@extends('layouts.app')

@section('title', 'Vales de Resguardo')
@section('page-title', 'Vales de Resguardo')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <p class="text-muted mb-0">Gestiona las asignaciones de tabletas por periodo Exacer.</p>
    <a href="{{ route('assignments.create') }}" class="btn btn-primary-custom">
        <i class="bi bi-plus-circle-fill me-2"></i>Nuevo Vale
    </a>
</div>

<div class="data-table">
    @if($assignments->isEmpty())
        <div class="p-5 text-center text-muted">
            <i class="bi bi-file-earmark-x fs-1 d-block mb-2"></i>
            <div class="fw-semibold mb-1">Sin vales de resguardo</div>
            <div class="small mb-3">Crea el primer vale para asignar tabletas a una sede.</div>
            <a href="{{ route('assignments.create') }}" class="btn btn-primary-custom">
                <i class="bi bi-plus-circle-fill me-2"></i>Crear primer vale
            </a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Vale</th>
                        <th>Sede</th>
                        <th>Coordinador</th>
                        <th>Periodo</th>
                        <th>Tabletas</th>
                        <th>Devoluciones</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assignments as $a)
                    @php
                        $total    = $a->items->count();
                        $returned = $a->items->where('is_returned', true)->count();
                    @endphp
                    <tr>
                        <td>
                            <span class="fw-bold text-muted small">VAL-{{ str_pad($a->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td><strong>{{ $a->location->name ?? '—' }}</strong></td>
                        <td>
                            <div>{{ $a->coordinator->full_name ?? '—' }}</div>
                            @if($a->delivery_person_name)
                                <div class="text-muted small">Entrega: {{ $a->delivery_person_name }}</div>
                            @endif
                        </td>
                        <td class="small text-muted">
                            @if($a->start_date)
                                {{ $a->start_date->format('d/m/Y') }} —
                                {{ $a->end_date?->format('d/m/Y') ?? '∞ Indefinido' }}
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $total }}</span>
                            <span class="text-muted small">pzs.</span>
                        </td>
                        <td>
                            @if($total > 0)
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:6px; width:70px; border-radius:99px;">
                                        <div class="progress-bar bg-success" style="width:{{ ($returned/$total)*100 }}%"></div>
                                    </div>
                                    <span class="small fw-semibold {{ $returned == $total ? 'text-success' : 'text-muted' }}">
                                        {{ $returned }}/{{ $total }}
                                    </span>
                                </div>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-status badge-{{ $a->status }}">{{ ucfirst($a->status) }}</span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('assignments.show', $a) }}" class="btn btn-sm btn-outline-primary" title="Ver / Liberar">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="{{ route('assignments.pdf', $a) }}" class="btn btn-sm btn-outline-secondary" title="PDF" target="_blank">
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

@endsection
