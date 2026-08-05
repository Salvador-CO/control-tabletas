@extends('layouts.app')

@section('title', 'Personal')
@section('page-title', 'Gestión de Personal')

@section('content')
<div class="row g-3">

    {{-- ── Tabla de Personal ── --}}
    <div class="col-12 col-xl-8">
        <div class="data-table">

            {{-- Cabecera: contador + buscador + botón importar --}}
            <div class="p-3 border-bottom d-flex flex-wrap align-items-center gap-2">
                <h6 class="fw-semibold mb-0 me-auto">
                    <i class="bi bi-people-fill text-primary me-2"></i>
                    <span id="staffCount">{{ $staff->count() }}</span> personas registradas
                </h6>
                <div class="input-group" style="max-width:260px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted" style="font-size:.8rem;"></i>
                    </span>
                    <input type="text" id="staffSearch" class="form-control border-start-0 ps-0"
                           placeholder="Buscar por nombre, cargo o sede…"
                           style="font-size:.85rem;">
                </div>
                <button class="btn btn-outline-success btn-sm fw-semibold"
                        data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="bi bi-upload me-1"></i>Importar CSV / Excel
                </button>
            </div>

            @if($staff->isEmpty())
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-person-x fs-1 d-block mb-2"></i>No hay personal registrado.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table" id="staffTable">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Cargo</th>
                                <th>Sede</th>
                                <th>Notas</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="staffTbody">
                        @foreach($staff as $s)
                        <tr class="staff-row"
                            data-name="{{ strtolower($s->full_name) }}"
                            data-role="{{ strtolower($s->role) }}"
                            data-location="{{ strtolower($s->location->name ?? '') }}">
                            <td class="fw-semibold">{{ $s->full_name }}</td>
                            <td><span class="badge bg-light text-dark fw-normal">{{ $s->role }}</span></td>
                            <td>{{ $s->location->name ?? '—' }}</td>
                            <td>
                                @if($s->notes)
                                    <span class="text-muted small" style="max-width:180px;display:inline-block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"
                                          title="{{ $s->notes }}">
                                        {{ $s->notes }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal" data-bs-target="#editStaffModal"
                                    data-id="{{ $s->id }}"
                                    data-name="{{ $s->full_name }}"
                                    data-role="{{ $s->role }}"
                                    data-location="{{ $s->location_id }}"
                                    data-notes="{{ $s->notes }}">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <form method="POST" action="{{ route('staff.destroy', $s) }}" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar a {{ $s->full_name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash-fill"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-3 py-2 border-top small text-muted" id="searchInfo" style="display:none;">
                    Mostrando <span id="visibleCount">0</span> de {{ $staff->count() }} personas
                </div>
            @endif
        </div>
    </div>

    {{-- ── Formulario Agregar ── --}}
    <div class="col-12 col-xl-4">
        <div class="stat-card">
            <h6 class="fw-semibold mb-3"><i class="bi bi-person-plus-fill text-success me-2"></i>Registrar Personal</h6>
            <form method="POST" action="{{ route('staff.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Nombre Completo *</label>
                    <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror"
                           value="{{ old('full_name') }}" required>
                    @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Rol / Cargo *</label>
                    <input type="text" name="role" class="form-control" value="{{ old('role') }}"
                           placeholder="Ej: Coordinador Académico" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Sede (opcional)</label>
                    <select name="location_id" id="add_location_select" class="form-select">
                        <option value="">Sin sede</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Notas / Observaciones</label>
                    <textarea name="notes" class="form-control" rows="2"
                              placeholder="Estado, observaciones, información adicional…">{{ old('notes') }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary-custom w-100">
                    <i class="bi bi-save-fill me-2"></i>Guardar
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ── Modal Editar ── --}}
<div class="modal fade" id="editStaffModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Editar Personal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editStaffForm">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Nombre Completo</label>
                        <input type="text" name="full_name" id="edit_s_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Rol</label>
                        <input type="text" name="role" id="edit_s_role" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Sede</label>
                        <select name="location_id" id="edit_s_location" class="form-select">
                            <option value="">Sin sede</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Notas / Observaciones</label>
                        <textarea name="notes" id="edit_s_notes" class="form-control" rows="2"
                                  placeholder="Observaciones…"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Modal Importar CSV/Excel ── --}}
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">
                    <i class="bi bi-upload text-success me-2"></i>Importar Personal desde CSV / Excel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                {{-- Paso 1: seleccionar archivo --}}
                <div id="importStep1">
                    <div class="alert" style="background:#f0fdf4;border-left:4px solid #059669;border-radius:10px;color:#065f46;" role="alert">
                        <strong><i class="bi bi-info-circle me-1"></i>Formato esperado:</strong>
                        El archivo debe tener encabezados con las columnas:
                        <code>Nombre</code>, <code>Sede</code>, <code>Cargo</code>
                        (en cualquier orden). Compatible con <strong>.csv</strong> y <strong>.xlsx</strong>.
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Seleccionar archivo</label>
                        <input type="file" id="importFile" class="form-control" accept=".csv,.xlsx,.xls,.txt">
                    </div>
                    <button class="btn btn-primary-custom w-100" id="analyzeBtn">
                        <i class="bi bi-search me-2"></i>Analizar archivo
                    </button>
                    <div id="importError" class="alert alert-danger mt-3 d-none"></div>
                </div>

                {{-- Paso 2: resultados de pre-validación --}}
                <div id="importStep2" style="display:none;">
                    <div class="row g-2 mb-3" id="importSummary"></div>

                    <div id="importTableWrap">
                        <h6 class="fw-semibold mb-2">
                            <i class="bi bi-list-check text-success me-2"></i>
                            Personas a importar (<span id="toImportCount">0</span>)
                        </h6>
                        <div class="table-responsive mb-3" style="max-height:280px;overflow-y:auto;">
                            <table class="table table-sm" id="importToTable">
                                <thead>
                                    <tr>
                                        <th>Nombre</th><th>Cargo</th><th>Sede</th><th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <h6 class="fw-semibold mb-2 text-muted">
                            <i class="bi bi-skip-forward me-2"></i>
                            Se saltarán (<span id="skippedCount">0</span>)
                        </h6>
                        <div class="table-responsive" style="max-height:160px;overflow-y:auto;">
                            <table class="table table-sm" id="importSkipTable">
                                <thead>
                                    <tr>
                                        <th>Nombre</th><th>Motivo</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="importBackBtn" style="display:none;"
                        onclick="resetImport()">← Cargar otro archivo</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary-custom" id="confirmImportBtn" style="display:none;">
                    <i class="bi bi-check-circle-fill me-2"></i>Confirmar importación
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
/* ──────────────────────────────────────────────────
   1. Tom Select en select de sede (formulario agregar)
   ────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {

    new TomSelect('#add_location_select', {
        placeholder: 'Sin sede',
        allowEmptyOption: true,
        sortField: { field: 'text', direction: 'asc' }
    });

    new TomSelect('#edit_s_location', {
        placeholder: 'Sin sede',
        allowEmptyOption: true,
        sortField: { field: 'text', direction: 'asc' }
    });

    /* ──────────────────────────────────────────────────
       2. Modal Editar: poblar datos
       ────────────────────────────────────────────────── */
    const editModal = document.getElementById('editStaffModal');
    const editLocTs = editModal._tomSelectLocation;

    editModal.addEventListener('show.bs.modal', function(e) {
        const btn  = e.relatedTarget;
        const form = document.getElementById('editStaffForm');
        form.action = '/staff/' + btn.dataset.id;
        document.getElementById('edit_s_name').value  = btn.dataset.name  || '';
        document.getElementById('edit_s_role').value  = btn.dataset.role  || '';
        document.getElementById('edit_s_notes').value = btn.dataset.notes || '';

        // Tom Select: actualizar valor
        const tsEl = document.getElementById('edit_s_location');
        if (tsEl.tomselect) {
            tsEl.tomselect.setValue(btn.dataset.location || '');
        } else {
            tsEl.value = btn.dataset.location || '';
        }
    });

    /* ──────────────────────────────────────────────────
       3. Buscador en tabla de personal
       ────────────────────────────────────────────────── */
    const searchInput  = document.getElementById('staffSearch');
    const rows         = document.querySelectorAll('.staff-row');
    const searchInfo   = document.getElementById('searchInfo');
    const visibleCount = document.getElementById('visibleCount');
    const staffCount   = document.getElementById('staffCount');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q   = this.value.trim().toLowerCase();
            let visible = 0;

            rows.forEach(row => {
                const match = !q
                    || row.dataset.name.includes(q)
                    || row.dataset.role.includes(q)
                    || row.dataset.location.includes(q);

                row.style.display = match ? '' : 'none';
                if (match) visible++;
            });

            if (q) {
                searchInfo.style.display = '';
                visibleCount.textContent = visible;
            } else {
                searchInfo.style.display = 'none';
            }
        });
    }
});

/* ──────────────────────────────────────────────────
   4. Importación CSV / Excel
   ────────────────────────────────────────────────── */
let pendingImportData = [];
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

document.getElementById('analyzeBtn')?.addEventListener('click', async function () {
    const fileInput = document.getElementById('importFile');
    const errorDiv  = document.getElementById('importError');
    errorDiv.classList.add('d-none');

    if (!fileInput.files.length) {
        errorDiv.textContent = 'Por favor selecciona un archivo primero.';
        errorDiv.classList.remove('d-none');
        return;
    }

    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Analizando…';

    const formData = new FormData();
    formData.append('file', fileInput.files[0]);
    formData.append('_token', csrfToken);

    try {
        const res  = await fetch('{{ route("staff.import.preview") }}', { method: 'POST', body: formData });
        const data = await res.json();

        if (!res.ok) {
            throw new Error(data.error || data.message || 'Error al analizar el archivo.');
        }

        pendingImportData = data.to_import;
        renderImportPreview(data.to_import, data.skipped);

    } catch (err) {
        errorDiv.textContent = err.message;
        errorDiv.classList.remove('d-none');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-search me-2"></i>Analizar archivo';
    }
});

function renderImportPreview(toImport, skipped) {
    document.getElementById('importStep1').style.display = 'none';
    document.getElementById('importStep2').style.display = '';
    document.getElementById('importBackBtn').style.display = '';

    // Resumen
    const summaryEl = document.getElementById('importSummary');
    summaryEl.innerHTML = `
        <div class="col-4">
            <div class="text-center p-3 rounded-3" style="background:#f0fdf4;">
                <div style="font-size:1.8rem;font-weight:700;color:#059669;">${toImport.length}</div>
                <div class="small text-muted">Se importarán</div>
            </div>
        </div>
        <div class="col-4">
            <div class="text-center p-3 rounded-3" style="background:#fef3c7;">
                <div style="font-size:1.8rem;font-weight:700;color:#d97706;">${toImport.filter(r=>r.status==='warn').length}</div>
                <div class="small text-muted">Sede no encontrada</div>
            </div>
        </div>
        <div class="col-4">
            <div class="text-center p-3 rounded-3" style="background:#fee2e2;">
                <div style="font-size:1.8rem;font-weight:700;color:#dc2626;">${skipped.length}</div>
                <div class="small text-muted">Ya existen (se saltarán)</div>
            </div>
        </div>`;

    // Tabla de importar
    document.getElementById('toImportCount').textContent = toImport.length;
    const toTbody = document.querySelector('#importToTable tbody');
    toTbody.innerHTML = toImport.length
        ? toImport.map(r => `
            <tr>
                <td class="fw-semibold">${escHtml(r.nombre)}</td>
                <td>${escHtml(r.cargo)}</td>
                <td>${escHtml(r.sede)}</td>
                <td>
                    ${r.status === 'warn'
                        ? '<span class="badge" style="background:#fef3c7;color:#92400e;">⚠ Sede no hallada</span>'
                        : '<span class="badge" style="background:#d1fae5;color:#065f46;">✓ OK</span>'}
                </td>
            </tr>`).join('')
        : '<tr><td colspan="4" class="text-center text-muted">Ningún registro nuevo para importar.</td></tr>';

    // Tabla de saltados
    document.getElementById('skippedCount').textContent = skipped.length;
    const skipTbody = document.querySelector('#importSkipTable tbody');
    skipTbody.innerHTML = skipped.length
        ? skipped.map(r => `
            <tr>
                <td>${escHtml(r.nombre)}</td>
                <td><span class="badge bg-danger-subtle text-danger">Ya existe</span></td>
            </tr>`).join('')
        : '<tr><td colspan="2" class="text-center text-muted">Ninguno.</td></tr>';

    const confirmBtn = document.getElementById('confirmImportBtn');
    confirmBtn.style.display = toImport.length > 0 ? '' : 'none';
    confirmBtn.textContent   = `Confirmar importación de ${toImport.length} persona${toImport.length !== 1 ? 's' : ''}`;
    // Re-attach icon
    confirmBtn.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i>Confirmar importación de ${toImport.length} persona${toImport.length !== 1 ? 's' : ''}`;
}

document.getElementById('confirmImportBtn')?.addEventListener('click', async function () {
    if (!pendingImportData.length) return;

    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando…';

    try {
        const res  = await fetch('{{ route("staff.import.confirm") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ items: pendingImportData }),
        });
        const data = await res.json();

        if (!res.ok) throw new Error(data.message || 'Error al guardar.');

        // Éxito: cerrar modal y recargar página
        bootstrap.Modal.getInstance(document.getElementById('importModal')).hide();
        window.location.reload();

    } catch (err) {
        alert('Error: ' + err.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Confirmar importación';
    }
});

function resetImport() {
    document.getElementById('importStep1').style.display = '';
    document.getElementById('importStep2').style.display = 'none';
    document.getElementById('importBackBtn').style.display = 'none';
    document.getElementById('confirmImportBtn').style.display = 'none';
    document.getElementById('importFile').value = '';
    document.getElementById('importError').classList.add('d-none');
    pendingImportData = [];
}

function escHtml(str) {
    return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
@endpush
