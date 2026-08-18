@extends('layouts.app')

@section('content')
@php
    // Forzar español en Carbon para diffForHumans()
    \Carbon\Carbon::setLocale('es');
@endphp

{{-- ─── Encabezado ───────────────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0">
            <i class="bi bi-shield-check me-2 text-primary"></i>Control MDM / Telemetría
        </h2>
        <small class="text-muted">Panel de control y supervisión remota de tabletas</small>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-light text-dark border" id="refresh-badge">
            <i class="bi bi-arrow-clockwise me-1"></i>Actualizando en <span id="refresh-countdown">30</span>s
        </span>
        <div class="progress" style="width:80px;height:6px;" id="refresh-progress-wrapper">
            <div class="progress-bar bg-primary" id="refresh-progress" style="width:100%;transition:width 1s linear;"></div>
        </div>
    </div>
</div>

{{-- ─── Alertas de sesión ─────────────────────────────────────── --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- ─── KPIs ──────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="fs-1 fw-bold text-success">{{ $onlineDevices }}</div>
                <div class="small text-muted"><i class="bi bi-circle-fill text-success me-1" style="font-size:.5rem;"></i>En línea</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="fs-1 fw-bold text-danger">{{ $totalDevices - $onlineDevices }}</div>
                <div class="small text-muted"><i class="bi bi-circle-fill text-danger me-1" style="font-size:.5rem;"></i>Desconectados</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="fs-1 fw-bold text-warning">{{ $pendingCommands + $pendingMessages }}</div>
                <div class="small text-muted"><i class="bi bi-hourglass-split me-1 text-warning"></i>Comandos pendientes</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="fs-1 fw-bold text-info">{{ $avgBattery !== null ? round($avgBattery) . '%' : '—' }}</div>
                <div class="small text-muted"><i class="bi bi-battery-half me-1 text-info"></i>Batería promedio</div>
            </div>
        </div>
    </div>
</div>

{{-- ─── FILA SUPERIOR: Fondo de pantalla + Mensajes Masivos ─────── --}}
<div class="row g-3 mb-4">
    {{-- Fondo de Pantalla --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                <h5 class="mb-0"><i class="bi bi-image text-primary me-2"></i>Fondo de Pantalla Global</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('mdm.set-global-wallpaper') }}" method="POST" class="d-flex align-items-end gap-2">
                    @csrf
                    <div class="flex-grow-1">
                        <label class="form-label text-muted small fw-semibold">URL de la Imagen</label>
                        <input type="url" name="wallpaper_url" class="form-control" placeholder="https://ejemplo.com/fondo.jpg" required>
                    </div>
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-send-fill me-1"></i>Aplicar
                    </button>
                </form>
                <small class="text-muted d-block mt-2">La imagen se establecerá como fondo en todas las tablets en su próxima sincronización.</small>
            </div>
        </div>
    </div>

    {{-- Sistema de Mensajes Masivos --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-megaphone-fill text-warning me-2"></i>Enviar Aviso a Todas las Tablets</h5>
                @if($pendingMessages > 0)
                    <form action="{{ route('mdm.clear-message') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Cancelar mensaje pendiente">
                            <i class="bi bi-x-circle me-1"></i>Cancelar ({{ $pendingMessages }})
                        </button>
                    </form>
                @endif
            </div>
            <div class="card-body">
                <form action="{{ route('mdm.send-message') }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label text-muted small fw-semibold">Mensaje a mostrar en pantalla</label>
                        <textarea name="message" class="form-control" rows="2" maxlength="500"
                            placeholder="Ej: Recuerden devolver las tabletas al finalizar la sesión..." required></textarea>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Máximo 500 caracteres</small>
                            <small class="text-muted"><span id="char-count">0</span>/500</small>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning w-100">
                        <i class="bi bi-broadcast me-1"></i>Enviar a {{ $totalDevices }} dispositivo(s)
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ─── FILA PRINCIPAL: Tabla de Dispositivos + Intrusos ────────── --}}
<div class="row g-3 mb-4">
    {{-- Tabla principal de dispositivos --}}
    <div class="col-xl-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                <h5 class="mb-0"><i class="bi bi-tablet me-2"></i>Estado de Dispositivos Registrados</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Dispositivo</th>
                                <th>Estado</th>
                                <th>Batería</th>
                                <th>Red / IP</th>
                                <th>Última Sync</th>
                                <th>Ubicación</th>
                                <th class="text-center pe-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($devices as $device)
                                @php
                                    $t = $device->telemetry;
                                    $minutesAgo = $t && $t->last_sync_at ? $t->last_sync_at->diffInMinutes(now()) : null;
                                    if ($minutesAgo === null)       { $statusColor = 'secondary'; $statusIcon = 'circle'; $statusLabel = 'Sin datos'; }
                                    elseif ($minutesAgo <= 5)       { $statusColor = 'success';   $statusIcon = 'circle-fill'; $statusLabel = 'En línea'; }
                                    elseif ($minutesAgo <= 30)      { $statusColor = 'warning';   $statusIcon = 'circle-fill'; $statusLabel = 'Reciente'; }
                                    else                            { $statusColor = 'danger';    $statusIcon = 'circle-fill'; $statusLabel = 'Fuera de línea'; }
                                @endphp
                                <tr>
                                    {{-- Dispositivo --}}
                                    <td class="ps-3">
                                        <div class="fw-semibold">{{ $device->model }}</div>
                                        <small class="text-muted d-block">SN: {{ $device->serial_number }}</small>
                                        @if($device->device_reported_serial && $device->device_reported_serial !== $device->serial_number)
                                            <small class="text-info d-block">
                                                <i class="bi bi-link-45deg"></i> ID: {{ $device->device_reported_serial }}
                                            </small>
                                        @endif
                                        @if($t)
                                            <small class="text-muted d-block">
                                                <i class="bi bi-android2"></i> Android {{ $t->android_version ?? '?' }}
                                                &nbsp;·&nbsp;
                                                <i class="bi bi-app"></i> App {{ $t->app_version ?? '?' }}
                                            </small>
                                        @endif
                                    </td>

                                    {{-- Estado Online/Offline --}}
                                    <td>
                                        <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }} border border-{{ $statusColor }}-subtle">
                                            <i class="bi bi-{{ $statusIcon }} me-1" style="font-size:.5rem;vertical-align:middle;"></i>{{ $statusLabel }}
                                        </span>
                                        @if($t)
                                            <div class="mt-1">
                                                @if($t->pending_command)
                                                    <span class="badge bg-warning text-dark" title="Comando pendiente">
                                                        <i class="bi bi-hourglass-split"></i> {{ $t->pending_command }}
                                                    </span>
                                                @endif
                                                @if($t->pending_message)
                                                    <span class="badge bg-info text-white" title="Mensaje pendiente">
                                                        <i class="bi bi-envelope-fill"></i> Msg
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Batería --}}
                                    <td style="min-width:100px;">
                                        @if($t && $t->battery_level !== null)
                                            @php
                                                $bat = $t->battery_level;
                                                $batColor = $bat > 50 ? 'success' : ($bat > 20 ? 'warning' : 'danger');
                                            @endphp
                                            <div class="d-flex align-items-center gap-1">
                                                @if($t->is_charging)
                                                    <i class="bi bi-battery-charging text-success"></i>
                                                @else
                                                    <i class="bi bi-battery-half text-{{ $batColor }}"></i>
                                                @endif
                                                <span class="fw-semibold">{{ $bat }}%</span>
                                            </div>
                                            <div class="progress mt-1" style="height:4px;width:70px;">
                                                <div class="progress-bar bg-{{ $batColor }}" style="width:{{ $bat }}%;"></div>
                                            </div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    {{-- Red / IP --}}
                                    <td>
                                        @if($t)
                                            @if($t->wifi_ssid)
                                                <div class="small"><i class="bi bi-wifi text-success me-1"></i>{{ $t->wifi_ssid }}</div>
                                            @endif
                                            @if($t->ip_address)
                                                <div class="small text-muted"><i class="bi bi-hdd-network me-1"></i>{{ $t->ip_address }}</div>
                                            @endif
                                            @if(!$t->wifi_ssid && !$t->ip_address)
                                                <span class="text-muted small">—</span>
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    {{-- Última Sync --}}
                                    <td>
                                        @if($t && $t->last_sync_at)
                                            <span title="{{ $t->last_sync_at->format('d/m/Y H:i:s') }}">
                                                {{ $t->last_sync_at->locale('es')->diffForHumans() }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    {{-- Ubicación --}}
                                    <td>
                                        @if($t && $t->latitude)
                                            <a href="https://maps.google.com/?q={{ $t->latitude }},{{ $t->longitude }}"
                                               target="_blank" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-geo-alt"></i> Mapa
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="text-center pe-3">
                                        @if($t)
                                            <div class="d-flex gap-1 justify-content-center flex-wrap">
                                                {{-- Bloquear dispositivo --}}
                                                <form action="{{ route('mdm.send-command', $device->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="command" value="lock_device">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Bloquear dispositivo">
                                                        <i class="bi bi-lock-fill"></i>
                                                    </button>
                                                </form>
                                                {{-- Reiniciar --}}
                                                <form action="{{ route('mdm.send-command', $device->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="command" value="reboot">
                                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Reiniciar dispositivo">
                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                    </button>
                                                </form>
                                                {{-- Limpiar caché --}}
                                                <form action="{{ route('mdm.send-command', $device->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="command" value="clear_cache">
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Limpiar caché">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle small">Sin app</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted py-4">No hay dispositivos registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Columna derecha --}}
    <div class="col-xl-4 d-flex flex-column gap-3">

        {{-- Dispositivos Intrusos --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0 text-danger">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Dispositivos No Registrados</h5>
                <small class="text-muted">Tienen la app pero no están en el inventario</small>
            </div>
            <div class="card-body p-0">
                @if($unregistered->isEmpty())
                    <p class="text-muted text-center mb-0 py-4"><i class="bi bi-check-circle me-1 text-success"></i>Ningún intruso detectado.</p>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($unregistered as $unreg)
                            <li class="list-group-item px-3 py-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-semibold">{{ $unreg->device_model ?? 'Desconocido' }}</div>
                                        <small class="text-muted">ID: <code>{{ $unreg->reported_serial }}</code></small><br>
                                        <small class="text-muted">Visto: {{ $unreg->last_sync_at ? $unreg->last_sync_at->locale('es')->diffForHumans() : 'Nunca' }}</small>
                                        @if($unreg->battery_level !== null)
                                            <small class="text-muted d-block"><i class="bi bi-battery-half"></i> {{ $unreg->battery_level }}%</small>
                                        @endif
                                    </div>
                                    <div class="d-flex flex-column gap-1">
                                        {{-- Vincular con dispositivo existente --}}
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                            title="Vincular con dispositivo existente"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modal-link-{{ $loop->index }}"
                                            data-reported="{{ $unreg->reported_serial }}">
                                            <i class="bi bi-link-45deg"></i> Vincular
                                        </button>
                                        {{-- Registrar como nuevo --}}
                                        <a href="{{ route('devices.index', ['serial_number' => $unreg->reported_serial, 'brand' => 'XIAOMI', 'model' => $unreg->device_model]) }}"
                                           class="btn btn-sm btn-primary-custom" title="Registrar como nuevo">
                                            <i class="bi bi-plus-lg"></i> Nuevo
                                        </a>
                                    </div>
                                </div>
                            </li>

                            {{-- Modal Vincular Serial --}}
                            <div class="modal fade" id="modal-link-{{ $loop->index }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"><i class="bi bi-link-45deg me-2"></i>Vincular Serial del Dispositivo</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="text-muted small">
                                                El dispositivo está reportando el serial/ID: <strong><code>{{ $unreg->reported_serial }}</code></strong><br>
                                                Este es el número que el dispositivo usa internamente (Android ID / IMEI), que puede diferir del serial de la caja.<br>
                                                Selecciona el dispositivo registrado al que corresponde para vincularlo.
                                            </p>
                                            <form action="" method="POST" id="link-form-{{ $loop->index }}">
                                                @csrf
                                                <input type="hidden" name="reported_serial" value="{{ $unreg->reported_serial }}">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Seleccionar dispositivo del inventario</label>
                                                    <select name="device_id" class="form-select" required
                                                        onchange="document.getElementById('link-form-{{ $loop->index }}').action='/mdm/device/'+this.value+'/link-serial'">
                                                        <option value="">— Elige un dispositivo —</option>
                                                        @foreach($devices as $d)
                                                            <option value="{{ $d->id }}">{{ $d->model }} — SN: {{ $d->serial_number }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <button type="submit" class="btn btn-primary w-100">
                                                    <i class="bi bi-link-45deg me-1"></i>Vincular Serial
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        {{-- Historial de Comandos Recientes --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2 text-secondary"></i>Historial de Acciones</h5>
                <small class="text-muted">Últimas 20 acciones remotas</small>
            </div>
            <div class="card-body p-0" style="max-height:300px;overflow-y:auto;">
                @if($commandHistory->isEmpty())
                    <p class="text-muted text-center py-4 mb-0">Sin historial de comandos.</p>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($commandHistory as $log)
                            @php
                                $cmdIcons = [
                                    'lock_device'  => ['icon' => 'lock-fill',              'color' => 'danger'],
                                    'reboot'        => ['icon' => 'arrow-counterclockwise', 'color' => 'warning'],
                                    'clear_cache'   => ['icon' => 'trash3',                 'color' => 'secondary'],
                                    'send_message'  => ['icon' => 'megaphone-fill',          'color' => 'info'],
                                    'open_camera'   => ['icon' => 'camera-fill',             'color' => 'primary'],
                                ];
                                $cmdStyle = $cmdIcons[$log->command] ?? ['icon' => 'terminal', 'color' => 'dark'];
                                $cmdLabels = [
                                    'lock_device'  => 'Bloquear',
                                    'reboot'        => 'Reiniciar',
                                    'clear_cache'   => 'Caché',
                                    'send_message'  => 'Mensaje',
                                    'open_camera'   => 'Cámara',
                                ];
                                $cmdLabel = $cmdLabels[$log->command] ?? $log->command;
                            @endphp
                            <li class="list-group-item px-3 py-2 d-flex align-items-center gap-2">
                                <span class="badge bg-{{ $cmdStyle['color'] }}-subtle text-{{ $cmdStyle['color'] }} border border-{{ $cmdStyle['color'] }}-subtle">
                                    <i class="bi bi-{{ $cmdStyle['icon'] }}"></i>
                                </span>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="small fw-semibold text-truncate">{{ $log->device->model ?? '—' }} — {{ $cmdLabel }}</div>
                                    @if($log->command === 'send_message' && $log->payload)
                                        <div class="small text-muted text-truncate">"{{ Str::limit($log->payload, 50) }}"</div>
                                    @endif
                                    <div class="x-small text-muted">{{ $log->sent_at->locale('es')->diffForHumans() }}</div>
                                </div>
                                @if($log->executed_at)
                                    <i class="bi bi-check-circle-fill text-success" title="Ejecutado {{ $log->executed_at->locale('es')->diffForHumans() }}"></i>
                                @else
                                    <i class="bi bi-hourglass-split text-warning" title="Pendiente"></i>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Contador de caracteres para el formulario de mensaje ──────────────
const msgArea = document.querySelector('textarea[name="message"]');
const charCount = document.getElementById('char-count');
if (msgArea && charCount) {
    msgArea.addEventListener('input', () => {
        charCount.textContent = msgArea.value.length;
    });
}

// ── Auto-refresh con cuenta regresiva visual ──────────────────────────
let remaining = 30;
const countdownEl = document.getElementById('refresh-countdown');
const progressEl  = document.getElementById('refresh-progress');

function tick() {
    remaining--;
    if (countdownEl) countdownEl.textContent = remaining;
    if (progressEl)  progressEl.style.width = ((remaining / 30) * 100) + '%';
    if (remaining <= 0) {
        window.location.reload();
    }
}
setInterval(tick, 1000);
</script>
@endpush
