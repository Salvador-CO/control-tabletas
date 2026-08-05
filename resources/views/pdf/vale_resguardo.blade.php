<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vale de Resguardo — {{ $assignment->location->name ?? '' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10pt; color: #1a1a1a; }
        .page { width: 21cm; min-height: 29.7cm; padding: 1.5cm; margin: 0 auto; }

        /* Header */
        .header { display: flex; align-items: center; border-bottom: 3px solid #059669; padding-bottom: .75cm; margin-bottom: .5cm; }
        .header-logo { width: 3.5cm; flex-shrink: 0; }
        .header-logo img { width: 100%; }
        .header-info { flex: 1; padding-left: .5cm; }
        .header-info h1 { font-size: 13pt; font-weight: bold; color: #059669; text-transform: uppercase; }
        .header-info p { font-size: 8pt; color: #555; margin-top: 2px; }
        .header-folio { text-align: right; }
        .header-folio .folio-num { font-size: 16pt; font-weight: bold; color: #059669; }
        .header-folio .folio-label { font-size: 7pt; color: #777; text-transform: uppercase; }

        /* Info grid */
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .3cm .75cm; margin: .5cm 0; }
        .info-item label { display: block; font-size: 7.5pt; color: #777; text-transform: uppercase; letter-spacing: .05em; }
        .info-item span  { font-size: 9.5pt; font-weight: bold; }

        /* Table */
        table { width: 100%; border-collapse: collapse; margin-top: .5cm; font-size: 9pt; }
        thead tr { background: #059669; color: #fff; }
        thead th { padding: .2cm .3cm; text-align: left; font-size: 8pt; }
        tbody tr:nth-child(even) { background: #f0fdf4; }
        tbody td { padding: .18cm .3cm; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
        .check-box { width: 18px; height: 18px; border: 2px solid #059669; border-radius: 4px; display: inline-block; vertical-align: middle; text-align: center; line-height: 16px; font-size: 12pt; color: #059669; }
        .check-box.checked { background: #059669; color: #fff; }

        /* Signatures */
        .signatures { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5cm; margin-top: 1cm; }
        .sig-box { text-align: center; }
        .sig-line { border-top: 1px solid #334155; margin-top: 1.5cm; padding-top: .2cm; }
        .sig-name { font-weight: bold; font-size: 9pt; }
        .sig-role { font-size: 8pt; color: #555; }

        /* Footer */
        .doc-footer { text-align: center; margin-top: .75cm; font-size: 7.5pt; color: #999; border-top: 1px solid #e2e8f0; padding-top: .3cm; }

        /* Print */
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .page { padding: 1cm; }
        }
    </style>
</head>
<body>

<div class="no-print" style="background:#0f172a; padding:12px 20px; color:#e2e8f0; font-family:Arial; font-size:13px; display:flex; gap:12px; align-items:center;">
    <a href="{{ route('assignments.show', $assignment) }}" style="color:#94a3b8; text-decoration:none;">← Regresar</a>
    <span>|</span>
    <strong>Vista previa del Vale de Resguardo</strong>
    <button onclick="window.print()" style="margin-left:auto; background:#059669; border:none; color:#fff; padding:6px 18px; border-radius:6px; cursor:pointer; font-size:13px;">
        🖨 Imprimir / Guardar PDF
    </button>
</div>

<div class="page">
    <!-- Header -->
    <div class="header">
        <div class="header-info">
            <h1>COLEGIO DE BACHILLERES DEL ESTADO DE CHIAPAS</h1>
            <p>SUBDIRECCIÓN ACADÉMICA — CONTROL DE DISPOSITIVOS TECNOLÓGICOS</p>
            <p style="margin-top:4px; font-size:8pt; color:#059669; font-weight:bold;">
                VALE PROVISIONAL DE RESGUARDO DE TABLETAS
            </p>
        </div>
        <div class="header-folio">
            <div class="folio-label">Folio</div>
            <div class="folio-num">VAL-{{ str_pad($assignment->id, 4, '0', STR_PAD_LEFT) }}</div>
        </div>
    </div>

    <!-- Info Grid -->
    <div class="info-grid">
        <div class="info-item">
            <label>Sede / Plantel</label>
            <span>{{ $assignment->location->name ?? '—' }}</span>
        </div>
        <div class="info-item">
            <label>Coordinador Responsable</label>
            <span>{{ $assignment->coordinator->full_name ?? '—' }}</span>
        </div>
        <div class="info-item">
            <label>Fecha de Entrega</label>
            <span>{{ $assignment->start_date?->format('d \d\e F \d\e Y') ?? '—' }}</span>
        </div>
        <div class="info-item">
            <label>Fecha de Devolución</label>
            <span>{{ $assignment->end_date?->format('d \d\e F \d\e Y') ?? 'Al terminar el evento (fecha indefinida)' }}</span>
        </div>
        @if($assignment->event)
        <div class="info-item">
            <label>Evento / Exacer</label>
            <span>{{ $assignment->event->name }}</span>
        </div>
        @endif
        <div class="info-item">
            <label>No. de Cargadores Incluidos</label>
            <span>{{ $assignment->chargers_count }}</span>
        </div>
        <div class="info-item">
            <label>Entregó</label>
            <span>{{ $assignment->delivery_person_name ?? '—' }}</span>
        </div>
        <div class="info-item">
            <label>Total de Dispositivos</label>
            <span>{{ $assignment->items->count() }} tableta(s)</span>
        </div>
    </div>

    @if($assignment->observations)
    <p style="font-size:9pt; margin: .3cm 0; background:#f0fdf4; padding:.25cm .4cm; border-left:3px solid #059669; border-radius:4px;">
        <strong>Observaciones:</strong> {{ $assignment->observations }}
    </p>
    @endif

    <!-- Devices Table -->
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Marca / Modelo</th>
                <th>No. de Serie</th>
                <th>Cargador</th>
                <th>Personal Asignado</th>
                <th>Cargo en este Periodo</th>
                <th style="text-align:center;">Devuelto ✓</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assignment->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                    <strong>{{ $item->device->brand ?? '?' }}</strong>
                    {{ $item->device->model ?? '' }}
                </td>
                <td><strong>{{ $item->device->serial_number ?? '—' }}</strong></td>
                <td>{{ $item->device->charger_details ?? 'Sin cargador' }}</td>
                <td>{{ $item->staff->full_name ?? '—' }}</td>
                <td>{{ $item->role_in_period ?? '—' }}</td>
                <td style="text-align:center;">
                    <div class="check-box {{ $item->is_returned ? 'checked' : '' }}">
                        {{ $item->is_returned ? '✓' : '' }}
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>

    </table>

    <!-- Legal Text -->
    <p style="font-size:8pt; color:#555; margin-top:.5cm; line-height:1.4;">
        El/La coordinador(a) de la sede se compromete a devolver los dispositivos tecnológicos
        en las mismas condiciones en que fueron recibidos, a más tardar el
        <strong>{{ $assignment->end_date?->format('d \d\e F \d\e Y') ?? 'la conclusión del evento (fecha por definir)' }}</strong>.
        Cualquier daño o pérdida será responsabilidad de quien suscribe este documento.
    </p>

    <!-- Signatures -->
    <div class="signatures">
        <div class="sig-box">
            <div class="sig-line"></div>
            <div class="sig-name">{{ $assignment->coordinator->full_name ?? '________________________________________' }}</div>
            <div class="sig-role">{{ $assignment->coordinator->role ?? 'Coordinador(a) de Sede' }}</div>
            <div style="font-size:7.5pt; color:#999; margin-top:2px;">{{ $assignment->location->name ?? '' }}</div>
        </div>
        <div class="sig-box">
            <div class="sig-line"></div>
            <div class="sig-name">{{ $assignment->delivery_person_name ?? 'MARCELA PEÑA ORDOÑEZ' }}</div>
            <div class="sig-role">Responsable de Entrega</div>
            <div style="font-size:7.5pt; color:#999; margin-top:2px;">Subdirección Académica</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="doc-footer">
        Documento generado el {{ now()->format('d/m/Y H:i') }} — Sistema de Control de Dispositivos — Colegio de Bachilleres
    </div>
</div>

</body>
</html>
