<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignación Permanente — {{ $permanent->staff->full_name ?? '' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10pt; color: #1a1a1a; }
        .page { width: 21cm; min-height: 29.7cm; padding: 1.5cm; margin: 0 auto; }

        .header { display: flex; align-items: flex-start; border-bottom: 3px solid #7c3aed; padding-bottom: .75cm; margin-bottom: .5cm; }
        .header-info { flex: 1; padding-left: .3cm; }
        .header-info h1 { font-size: 13pt; font-weight: bold; color: #7c3aed; text-transform: uppercase; }
        .header-info p { font-size: 8pt; color: #555; margin-top: 2px; }
        .header-folio { text-align: right; }
        .header-folio .folio-num { font-size: 16pt; font-weight: bold; color: #7c3aed; }
        .header-folio .folio-label { font-size: 7pt; color: #777; text-transform: uppercase; }

        .type-badge {
            display: inline-block;
            background: #ede9fe;
            color: #7c3aed;
            font-size: 8pt;
            font-weight: bold;
            padding: .15cm .4cm;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-top: .3cm;
        }

        .section-title { font-size: 9pt; font-weight: bold; color: #7c3aed; text-transform: uppercase; letter-spacing: .06em; margin: .5cm 0 .25cm; border-bottom: 1px solid #ede9fe; padding-bottom: .15cm; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .3cm .75cm; margin: .4cm 0; }
        .info-item label { display: block; font-size: 7.5pt; color: #777; text-transform: uppercase; letter-spacing: .05em; }
        .info-item span { font-size: 9.5pt; font-weight: bold; }

        .device-box {
            border: 2px solid #7c3aed;
            border-radius: 6px;
            padding: .4cm .6cm;
            margin: .4cm 0;
            background: #faf5ff;
        }
        .device-box .device-title { font-size: 11pt; font-weight: bold; color: #1a1a1a; }
        .device-box .device-serial { font-size: 12pt; font-weight: bold; color: #7c3aed; font-family: monospace; }

        .legal-text { font-size: 8pt; color: #444; line-height: 1.5; margin: .5cm 0; background: #f5f3ff; padding: .35cm .5cm; border-left: 3px solid #7c3aed; border-radius: 4px; }

        .signatures { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5cm; margin-top: 1cm; }
        .sig-box { text-align: center; }
        .sig-line { border-top: 1px solid #334155; margin-top: 1.5cm; padding-top: .2cm; }
        .sig-name { font-weight: bold; font-size: 9pt; }
        .sig-role { font-size: 8pt; color: #555; }

        .doc-footer { text-align: center; margin-top: .75cm; font-size: 7.5pt; color: #999; border-top: 1px solid #e2e8f0; padding-top: .3cm; }

        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
        }
    </style>
</head>
<body>

<div class="no-print" style="background:#0f172a; padding:12px 20px; color:#e2e8f0; font-family:Arial; font-size:13px; display:flex; gap:12px; align-items:center;">
    <a href="{{ route('permanent.show', $permanent) }}" style="color:#94a3b8; text-decoration:none;">← Regresar</a>
    <span>|</span>
    <strong>Vista previa — Asignación Permanente</strong>
    <button onclick="window.print()" style="margin-left:auto; background:#7c3aed; border:none; color:#fff; padding:6px 18px; border-radius:6px; cursor:pointer; font-size:13px;">
        🖨 Imprimir / Guardar PDF
    </button>
</div>

<div class="page">

    <!-- Header -->
    <div class="header">
        <div class="header-info">
            <h1>COLEGIO DE BACHILLERES DEL ESTADO DE CHIAPAS</h1>
            <p>SUBDIRECCIÓN ACADÉMICA — CONTROL DE DISPOSITIVOS TECNOLÓGICOS</p>
            <div class="type-badge">📌 ASIGNACIÓN PERMANENTE DE DISPOSITIVO</div>
        </div>
        <div class="header-folio">
            <div class="folio-label">Folio</div>
            <div class="folio-num">PERM-{{ str_pad($permanent->id, 4, '0', STR_PAD_LEFT) }}</div>
        </div>
    </div>

    <!-- Datos del titular -->
    <div class="section-title">Datos del Titular</div>
    <div class="info-grid">
        <div class="info-item">
            <label>Nombre Completo</label>
            <span>{{ $permanent->staff->full_name ?? '—' }}</span>
        </div>
        <div class="info-item">
            <label>Cargo / Función</label>
            <span>{{ $permanent->role }}</span>
        </div>
        <div class="info-item">
            <label>Sede / Dependencia</label>
            <span>{{ $permanent->staff->location->name ?? 'No especificada' }}</span>
        </div>
        <div class="info-item">
            <label>Fecha de Asignación</label>
            <span>{{ $permanent->assigned_date->format('d \d\e F \d\e Y') }}</span>
        </div>
    </div>

    <!-- Dispositivo asignado -->
    <div class="section-title">Dispositivo Asignado</div>
    <div class="device-box">
        <div class="device-title">
            {{ $permanent->device->brand ?? '?' }} {{ $permanent->device->model ?? '' }}
            <span style="font-size:9pt; font-weight:normal; color:#555;">
                ({{ $permanent->device->category->name ?? '' }})
            </span>
        </div>
        <div class="device-serial">{{ $permanent->device->serial_number ?? '—' }}</div>
        <div style="margin-top:.2cm; font-size:8.5pt; color:#444;">
            <strong>Cargador:</strong> {{ $permanent->device->charger_details ?: 'No incluido / No especificado' }}
        </div>
    </div>

    @if($permanent->notes)
    <p style="font-size:9pt; margin:.3cm 0; background:#f0fdf4; padding:.25cm .4cm; border-left:3px solid #059669; border-radius:4px;">
        <strong>Estado del equipo / Observaciones:</strong> {{ $permanent->notes }}
    </p>
    @endif

    <!-- Texto legal -->
    <div class="legal-text">
        El/La titular indicado(a) recibe en este acto el dispositivo tecnológico descrito arriba,
        comprometiéndose a su uso exclusivo para actividades institucionales, a su resguardo y conservación en
        buen estado, y a devolverlo a la Subdirección Académica en caso de cambio de adscripción, comisión,
        separación del cargo o cuando así lo determine la autoridad competente.<br><br>
        Esta asignación tiene carácter <strong>indefinido</strong> y permanecerá vigente hasta que se formalice
        su liberación mediante documento correspondiente.
    </div>

    <!-- Firmas -->
    <div class="signatures">
        <div class="sig-box">
            <div class="sig-line"></div>
            <div class="sig-name">{{ strtoupper($permanent->staff->full_name ?? '________________________________________') }}</div>
            <div class="sig-role">{{ $permanent->role }}</div>
            <div style="font-size:7.5pt; color:#999; margin-top:2px;">{{ $permanent->staff->location->name ?? '' }}</div>
        </div>
        <div class="sig-box">
            <div class="sig-line"></div>
            <div class="sig-name">MARCELA PEÑA ORDOÑEZ</div>
            <div class="sig-role">Responsable de Dispositivos</div>
            <div style="font-size:7.5pt; color:#999; margin-top:2px;">Subdirección Académica</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="doc-footer">
        Folio PERM-{{ str_pad($permanent->id, 4, '0', STR_PAD_LEFT) }} —
        Generado el {{ now()->format('d/m/Y H:i') }} —
        Sistema de Control de Dispositivos — Colegio de Bachilleres
    </div>
</div>

</body>
</html>
