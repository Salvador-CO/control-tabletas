@extends('layouts.app')

@section('title', 'Vale VAL-' . str_pad($assignment->id, 4, '0', STR_PAD_LEFT))
@section('page-title', 'Vale de Resguardo — ' . ($assignment->location->name ?? ''))

@section('content')

<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <a href="{{ route('assignments.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Regresar
    </a>
    <div class="d-flex gap-2">
        <a href="{{ route('assignments.pdf', $assignment) }}" class="btn btn-sm btn-outline-danger" target="_blank">
            <i class="bi bi-file-pdf-fill me-1"></i>Descargar PDF
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

                <dt class="text-muted small">Coordinador</dt>
                <dd>{{ $assignment->coordinator->full_name ?? '—' }}</dd>

                <dt class="text-muted small">Entregó</dt>
                <dd>{{ $assignment->delivery_person_name ?? '—' }}</dd>

                <dt class="text-muted small">Periodo</dt>
                <dd>
                    {{ $assignment->start_date?->format('d/m/Y') ?? '—' }}
                    @if($assignment->end_date) — {{ $assignment->end_date->format('d/m/Y') }} @endif
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
                            <td class="small">{{ $item->staff->full_name ?? '<span class="text-muted">—</span>' }}</td>
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

@endsection

@push('scripts')
<script>
document.querySelectorAll('.liberation-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const itemId = this.dataset.itemId;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Optimistic UI update
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
                // Reload to update progress bar and timestamps
                setTimeout(() => location.reload(), 600);
            }
        })
        .catch(() => {
            alert('Error al actualizar. Intente de nuevo.');
            this.disabled = false;
        });
    });
});
</script>
@endpush
