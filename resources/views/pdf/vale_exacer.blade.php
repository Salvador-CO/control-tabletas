<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="preconnect" href="https://googleapis.com">
    <link rel="preconnect" href="https://gstatic.com" crossorigin>
    <link href="https://googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Vale de Resguardo Exacer — {{ $assignment->location->name ?? '' }}</title>
    <style>
        /* ── Reset y Configuración Estricta de Hoja Carta ── */
        * {

            box-sizing: border-box;
        }

        @page {
            size: letter portrait;
            margin: .6cm 1cm 1.5cm 1cm;

            /* Margen real de la página en PDF */
        }

        body {
            font-family: 'Noto Sans', Arial, sans-serif;
            font-size: 12pt;
            color: #1a1a1a;
            background: #ffffff;
        }

        .page {
            width: 85%;
            margin: 0 auto;
            background: #ffffff;
        }

        .encabezado {
            width: 100%;
            text-align: center;
            margin-bottom: 10px;
        }

        .encabezado img {
            width: 13cm;
            height: auto;
            display: block;
        }

        /* ── Encabezado Institucional ── */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2.5px solid #059669;
            padding-bottom: 4px;
            margin-bottom: 30px;
            table-layout: fixed;

        }

        .header-table td {
            vertical-align: bottom;
            border: none;
            padding: 0;
        }



        .header-text .sub {
            font-size: 8pt;
            color: #4b5563;
            margin-top: 2px;
            font-weight: bold;
        }

        .header-text .tipo {
            font-size: 9pt;
            color: #059669;
            font-weight: bold;
            margin-top: 3px;
            text-transform: uppercase;
        }

        .header-folio {
            text-align: right;
            white-space: nowrap;
        }

        .folio-label {
            font-size: 6.5pt;
            color: #6b7280;
            text-transform: uppercase;
        }

        .folio-num {
            font-size: 15pt;
            font-weight: bold;
            color: #059669;
            line-height: 1;
        }

        .folio-date {
            font-size: 7pt;
            color: #4b5563;
            margin-top: 2px;
        }

        /* ── Grid de Info Rápida ── */
        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 4px;
            table-layout: fixed;
        }

        .info-grid td {
            padding: 4px 8px;
            vertical-align: top;
            border: none;
        }

        .info-item label {
            display: block;
            font-size: 6pt;
            color: #059669;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 1px;
        }

        .info-item span {
            font-size: 8pt;
            font-weight: bold;
            color: #111827;
        }

        /* ── Párrafo Descriptivo ── */
        .intro-text {
            font-size: 8pt;
            line-height: 1.35;
            text-align: justify;
            margin-bottom: 8px;
            color: #1f2937;
        }

        .underline-bold {
            font-weight: bold;
            text-decoration: underline;
        }

        /* ── Observaciones (Aviso Amarillo) ── */
        .obs {
            font-size: 7.5pt;
            background: #fffbeb;
            border-left: 3px solid #f59e0b;
            padding: 4px 8px;
            margin-bottom: 8px;
            color: #92400e;
        }

        /* ── Tabla de Dispositivos ── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 7.5pt;
            table-layout: fixed;
        }

        .data-table thead tr {
            background: #059669;
            color: #ffffff;
        }

        .data-table th {
            padding: 4px 4px;
            text-align: center;
            font-size: 7pt;
            font-weight: bold;
            border: 1px solid #047857;
        }

        .data-table tbody tr:nth-child(even) {
            background: #f0fdf4;
        }

        .data-table td {
            padding: 4px 4px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
            text-align: center;
            color: #1e293b;
            word-wrap: break-word;
        }

        .data-table td.text-left {
            text-align: left;
        }

        /* ── Condiciones de Resguardo ── */
        .conditions-section {
            margin-bottom: 10px;
            font-size: 7pt;
            line-height: 1.3;
            color: #334155;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 6px 10px;
        }

        .conditions-title {
            font-weight: bold;
            font-size: 7.5pt;
            color: #0f172a;
            margin-bottom: 3px;
            text-transform: uppercase;
        }

        .conditions-list {
            padding-left: 14px;
            margin: 0;
        }

        .conditions-list li {
            margin-bottom: 2px;
            text-align: justify;
        }

        /* ── Sección de Firmas ── */
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed;
        }

        .signatures-table td {
            width: 50%;
            vertical-align: top;
            text-align: center;
            border: none;
            padding: 0 20px;
        }

        .sig-header {
            font-weight: bold;
            margin-bottom: 35px;
            /* Espacio para firma */
            font-size: 8pt;
            color: #0f172a;
            text-transform: uppercase;
        }

        .sig-line {
            border-top: 1px solid #334155;
            padding-top: 3px;
        }

        .sig-name {
            font-weight: bold;
            font-size: 8pt;
            color: #0f172a;
            text-transform: uppercase;
        }

        .sig-role {
            font-size: 7pt;
            color: #475569;
        }

        .sig-sub {
            font-size: 6.5pt;
            color: #64748b;
        }

        /* ── Footer ── */
        .doc-footer {
            text-align: center;
            font-size: 6pt;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
            margin-top: 8px;
        }
    </style>
</head>

<body>

    <div class="page">

        {{-- ── Encabezado Institucional ── --}}
        <div class="encabezado">
            <img src="{{ public_path('images/logo_cobac.png') }}" alt="Encabezado">
        </div>
        <table class="header-table">
            <tr>
                <td class="header-text" style="width: 70%;">
                    <div class="tipo">Vale Provisional de Resguardo de Tabletas — Exacer</div>
                    <div class="sub">DASE — CONTROL DE DISPOSITIVOS TECNOLÓGICOS</div>

                </td>
                <td class="header-folio" style="width: 30%;">
                    <div class="folio-label">Folio</div>
                    <div class="folio-num">VAL-{{ str_pad($assignment->id, 4, '0', STR_PAD_LEFT) }}</div>
                    <div class="folio-date">Fecha: {{ now()->format('d/m/Y') }}</div>
                </td>
            </tr>
        </table>

        {{-- ── Grid de Info Rápida ── --}}
        <table class="info-grid">
            <tr>
                <td class="info-item" colspan="2" style="width: 50%;">
                    <label>Sede / Plantel</label>
                    <span>{{ $assignment->location->name ?? '—' }}</span>
                </td>
                <td class="info-item" colspan="2" style="width: 50%;">
                    <label>Coordinador / Responsable</label>
                    <span>{{ $assignment->coordinator->full_name ?? '—' }}</span>
                </td>
            </tr>
            <tr>
                <td class="info-item" style="width: 25%;">
                    <label>Fecha de Entrega</label>
                    <span>{{ $assignment->start_date?->format('d/m/Y') ?? '—' }}</span>
                </td>
                <td class="info-item" style="width: 25%;">
                    <label>Fecha Devolución</label>
                    <span>{{ $assignment->end_date?->format('d/m/Y') ?? 'Al terminar evento' }}</span>
                </td>
                <td class="info-item" style="width: 25%;">
                    <label>Evento / Exacer</label>
                    <span>{{ $assignment->event->name ?? 'Sin evento' }}</span>
                </td>
                <td class="info-item" style="width: 25%;">
                    <label>Estado del Vale</label>
                    <span style="{{ $assignment->status === 'completado' ? 'color:#059669;' : ($assignment->status === 'cancelado' ? 'color:#dc2626;' : 'color:#d97706;') }}">
                        {{ strtoupper($assignment->status) }}
                    </span>
                </td>
            </tr>
        </table>

        {{-- ── Párrafo Contextual Institucional ── --}}
        <div class="intro-text">
            Como parte de la aplicación del evento <strong>{{ $assignment->event->name ?? 'EXACER' }}</strong> la cual se llevará a cabo del
            <strong>{{ $assignment->start_date?->format('d') ?? '—' }}</strong> al <strong>{{ $assignment->end_date?->format('d \d\e F \d\e Y') ?? '—' }}</strong>
            y con la finalidad de realizar el registro de asistencia y salida de los sustentantes en la sede
            <span class="underline-bold">{{ strtoupper($assignment->location->name ?? '—') }}</span>, se asignan en resguardo provisional las siguientes
            <span class="underline-bold">{{ $assignment->items->count() }}</span> tabletas con
            <span class="underline-bold">{{ $assignment->chargers_count }}</span> cargadores, de acuerdo con el siguiente detalle:
        </div>

        {{-- ── Observaciones (Si existen) ── --}}
        @if($assignment->observations)
        <div class="obs">
            <strong>Observaciones:</strong> {{ $assignment->observations }}
        </div>
        @endif

        {{-- ── Tabla de Dispositivos ── --}}
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 33%;">Personal Asignado</th>
                    <th style="width: 20%;">Marca / Modelo</th>
                    <th style="width: 22%;">No. de Serie</th>
                    <th style="width: 10%;">Funda/Correa</th>
                    <th style="width: 10%;">Firma</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignment->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="text-left">
                        <strong>{{ $item->staff->full_name ?? $assignment->coordinator->full_name ?? '—' }}</strong>
                    </td>
                    <td class="text-left">
                        {{ $item->device->brand ?? '' }} {{ $item->device->model ?? '' }}
                    </td>
                    <td><strong>{{ $item->device->serial_number ?? '—' }}</strong></td>
                    <td>Si</td>
                    <td></td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">No se registraron dispositivos en este resguardo.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- ── Condiciones de Resguardo ── --}}
        <div class="conditions-section">
            <div class="conditions-title">Condiciones de Resguardo:</div>
            <ol class="conditions-list">
                <li>El equipo y los accesorios serán entregados al <strong>COORDINADOR DE SEDE</strong> para el uso del personal antes mencionado durante el período establecido.</li>
                <li>Los responsables se comprometen a garantizar el correcto uso, cuidado y mantenimiento de los dispositivos y sus accesorios durante el periodo de resguardo.</li>
                <li>Al finalizar el período, los equipos deberán ser devueltos en las mismas condiciones en que fueron entregados (incluyendo cargador, funda y correa).</li>
                <li>En caso de daño, extravío o pérdida de los equipos, se deberá informar inmediatamente y tomar las medidas correspondientes conforme a los lineamientos establecidos.</li>
            </ol>
        </div>

        {{-- ── Firmas ── --}}
        <table class="signatures-table">
            <tr>
                <td class="sig-header">Entrega</td>
                <td class="sig-header">Recibe</td>
            </tr>
            <tr>
                <td>
                    <div class="sig-line">
                        <div class="sig-name">{{ $assignment->delivery_person_name ?? 'MARCELA PEÑA ORDOÑEZ' }}</div>
                        <div class="sig-role">Entrega / Subdirección Académica</div>
                    </div>
                </td>
                <td>
                    <div class="sig-line">
                        <div class="sig-name">{{ $assignment->coordinator->full_name ?? '________________________________________' }}</div>
                        <div class="sig-role">{{ $assignment->coordinator->role ?? 'Coordinador(a) de Sede' }}</div>
                        <div class="sig-sub">{{ $assignment->location->name ?? '' }}</div>
                    </div>
                </td>
            </tr>
        </table>

        {{-- ── Footer ── --}}
        <div class="doc-footer">
            Generado el {{ now()->format('d/m/Y H:i') }} —
            VAL-{{ str_pad($assignment->id, 4, '0', STR_PAD_LEFT) }} —
            Sistema de Control de Dispositivos — Colegio de Bachilleres del Estado de Chiapas
        </div>

    </div>

</body>

</html>