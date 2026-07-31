@extends('layouts.app')

@section('title', 'Inventario de Dispositivos')
@section('page-title', 'Inventario de Dispositivos')

@section('content')

<div class="row g-3">
    {{-- ── Filters + Table ── --}}
    <div class="col-12 col-xl-8">
        {{-- Filter bar --}}
        <form method="GET" action="{{ route('devices.index') }}" class="stat-card mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-sm-6">
                    <label class="form-label text-muted small fw-semibold">Buscar</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Serie, modelo, marca…"
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-sm-4">
                    <label class="form-label text-muted small fw-semibold">Estado</label>
                    <select name="status" class="form-select">
                        <option value="Todos">Todos</option>
                        <option value="disponible"    {{ request('status') == 'disponible'    ? 'selected' : '' }}>Disponible</option>
                        <option value="en_resguardo"  {{ request('status') == 'en_resguardo'  ? 'selected' : '' }}>En Resguardo</option>
                        <option value="asignado_fijo" {{ request('status') == 'asignado_fijo' ? 'selected' : '' }}>Asignado Fijo</option>
                        <option value="mantenimiento" {{ request('status') == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                    </select>
                </div>
                <div class="col-sm-2">
                    <button type="submit" class="btn btn-primary-custom w-100">
                        <i class="bi bi-funnel-fill"></i>
                    </button>
                </div>
            </div>
        </form>

        {{-- Table --}}
        <div class="data-table">
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                <h6 class="fw-semibold mb-0">
                    <i class="bi bi-tablet-fill text-primary me-2"></i>
                    {{ $devices->total() }} dispositivos
                </h6>
                <span class="text-muted small">{{ $devices->currentPage() }}/{{ $devices->lastPage() }} págs.</span>
            </div>

            @if($devices->isEmpty())
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    No hay dispositivos registrados.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Categoría</th>
                                <th>Marca / Modelo</th>
                                <th>No. Serie</th>
                                <th>Cargador</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($devices as $device)
                            <tr>
                                <td class="text-muted small">{{ $device->id }}</td>
                                <td>
                                    <span class="badge bg-light text-dark fw-normal">
                                        {{ $device->category->name ?? '—' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $device->brand }}</div>
                                    <div class="text-muted small">{{ $device->model }}</div>
                                </td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded small">{{ $device->serial_number }}</code>
                                </td>
                                <td>
                                    @if($device->charger_details)
                                        <span class="text-success"><i class="bi bi-plug-fill me-1"></i>{{ $device->charger_details }}</span>
                                    @else
                                        <span class="text-muted small">Sin cargador</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge-status badge-{{ $device->status }}">
                                        {{ str_replace('_', ' ', ucfirst($device->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editDeviceModal"
                                            data-id="{{ $device->id }}"
                                            data-brand="{{ $device->brand }}"
                                            data-model="{{ $device->model }}"
                                            data-serial="{{ $device->serial_number }}"
                                            data-status="{{ $device->status }}"
                                            data-charger="{{ $device->charger_details }}"
                                            data-notes="{{ $device->notes }}">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $devices->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    {{-- ── Add Device Form ── --}}
    <div class="col-12 col-xl-4">
        <div class="stat-card">
            <h6 class="fw-semibold mb-3"><i class="bi bi-plus-circle-fill text-success me-2"></i>Registrar Dispositivo</h6>
            <form method="POST" action="{{ route('devices.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Categoría *</label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">Seleccionar categoría…</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold text-muted">Marca *</label>
                        <input type="text" name="brand" class="form-control @error('brand') is-invalid @enderror"
                               value="{{ old('brand', 'XIAOMI') }}" required>
                        @error('brand')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold text-muted">Modelo *</label>
                        <input type="text" name="model" class="form-control @error('model') is-invalid @enderror"
                               value="{{ old('model', 'Pad 6') }}" required>
                        @error('model')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">No. Serie *</label>
                    <input type="text" name="serial_number" class="form-control @error('serial_number') is-invalid @enderror"
                           value="{{ old('serial_number') }}" placeholder="Ej: SN123456ABC" required>
                    @error('serial_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Detalle del Cargador</label>
                    <input type="text" name="charger_details" class="form-control"
                           value="{{ old('charger_details') }}" placeholder="Ej: cargador punta, sin cargador…">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Notas / Observaciones</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Estado físico, daños, etc.">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary-custom w-100">
                    <i class="bi bi-save-fill me-2"></i>Guardar Dispositivo
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ── Edit Device Modal ── --}}
<div class="modal fade" id="editDeviceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-semibold"><i class="bi bi-pencil-fill me-2 text-primary"></i>Editar Dispositivo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editDeviceForm">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">Marca</label>
                            <input type="text" name="brand" id="edit_brand" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">Modelo</label>
                            <input type="text" name="model" id="edit_model" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-muted">No. Serie</label>
                            <input type="text" name="serial_number" id="edit_serial" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-muted">Estado</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="disponible">Disponible</option>
                                <option value="en_resguardo">En Resguardo</option>
                                <option value="asignado_fijo">Asignado Fijo</option>
                                <option value="mantenimiento">Mantenimiento</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-muted">Detalle del Cargador</label>
                            <input type="text" name="charger_details" id="edit_charger" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-muted">Notas</label>
                            <textarea name="notes" id="edit_notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom"><i class="bi bi-save me-1"></i>Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('editDeviceModal').addEventListener('show.bs.modal', function(event) {
    const btn    = event.relatedTarget;
    const form   = document.getElementById('editDeviceForm');
    form.action  = '/devices/' + btn.dataset.id;

    document.getElementById('edit_brand').value   = btn.dataset.brand   || '';
    document.getElementById('edit_model').value   = btn.dataset.model   || '';
    document.getElementById('edit_serial').value  = btn.dataset.serial  || '';
    document.getElementById('edit_status').value  = btn.dataset.status  || 'disponible';
    document.getElementById('edit_charger').value = btn.dataset.charger || '';
    document.getElementById('edit_notes').value   = btn.dataset.notes   || '';
});
</script>
@endpush
