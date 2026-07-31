<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Control de Tabletas') — Colegio de Bachilleres</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 260px;
            --primary: #059669;
            --primary-dark: #047857;
            --primary-light: #d1fae5;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
        }
        * { font-family: 'Inter', sans-serif; }
        body { background: #f1f5f9; min-height: 100vh; }

        /* ── Sidebar ── */
        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            top: 0; left: 0;
            transition: transform .3s ease;
            z-index: 1040;
            display: flex;
            flex-direction: column;
        }
        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #1e293b;
            display: flex;
            align-items: center;
            gap: .75rem;
            text-decoration: none;
        }
        .sidebar-brand .brand-icon {
            width: 40px; height: 40px;
            background: var(--primary);
            border-radius: 10px;
            display: grid; place-items: center;
            font-size: 1.1rem; color: #fff;
            flex-shrink: 0;
        }
        .sidebar-brand .brand-name { color: #f8fafc; font-weight: 700; font-size: .95rem; line-height: 1.2; }
        .sidebar-brand .brand-sub  { color: #64748b; font-size: .72rem; }

        .sidebar-nav { padding: 1rem .75rem; flex: 1; }
        .nav-label { color: #475569; font-size: .68rem; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; padding: .5rem .75rem .25rem; }
        .sidebar-link {
            display: flex; align-items: center; gap: .75rem;
            padding: .62rem .75rem; border-radius: 8px;
            color: #94a3b8; font-size: .875rem; font-weight: 500;
            text-decoration: none; transition: all .2s;
            margin-bottom: 2px;
        }
        .sidebar-link i { font-size: 1.05rem; width: 20px; text-align: center; }
        .sidebar-link:hover  { background: var(--sidebar-hover); color: #e2e8f0; }
        .sidebar-link.active { background: var(--primary); color: #fff; }
        .sidebar-link .badge-count {
            margin-left: auto;
            background: #ef4444; color: #fff;
            font-size: .68rem; font-weight: 700;
            border-radius: 99px; padding: .1rem .45rem;
        }

        /* ── Main content ── */
        #main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin .3s ease;
        }
        .topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: .75rem 1.5rem;
            display: flex; align-items: center; gap: 1rem;
            position: sticky; top: 0; z-index: 100;
        }
        .topbar .page-title { font-weight: 600; font-size: 1.05rem; color: #0f172a; margin: 0; }
        .page-body { padding: 1.5rem; }

        /* ── Cards ── */
        .stat-card {
            background: #fff;
            border-radius: 14px;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
            border: 1px solid #f1f5f9;
            transition: transform .2s, box-shadow .2s;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.1); }
        .stat-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: grid; place-items: center;
            font-size: 1.3rem;
        }
        .stat-value { font-size: 2rem; font-weight: 700; color: #0f172a; line-height: 1; }
        .stat-label { font-size: .78rem; color: #64748b; font-weight: 500; margin-top: .25rem; }
        .stat-change { font-size: .75rem; margin-top: .5rem; }

        /* ── Alert flash ── */
        .flash-alert {
            border-radius: 10px;
            border-left: 4px solid var(--primary);
            background: var(--primary-light);
            color: var(--primary-dark);
        }

        /* ── Table ── */
        .data-table { background: #fff; border-radius: 14px; box-shadow: 0 1px 3px rgba(0,0,0,.06); overflow: hidden; }
        .data-table .table { margin: 0; }
        .data-table .table thead th { background: #f8fafc; font-size: .75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: .05em; border-bottom: 2px solid #e2e8f0; padding: .9rem 1rem; }
        .data-table .table tbody td { padding: .85rem 1rem; vertical-align: middle; font-size: .875rem; border-bottom: 1px solid #f1f5f9; }
        .data-table .table tbody tr:last-child td { border-bottom: none; }
        .data-table .table tbody tr:hover td { background: #f8fafc; }

        /* ── Badges ── */
        .badge-status { font-size: .72rem; font-weight: 600; padding: .3em .7em; border-radius: 99px; }
        .badge-disponible   { background: #d1fae5; color: #065f46; }
        .badge-en_resguardo { background: #fef3c7; color: #92400e; }
        .badge-asignado_fijo{ background: #dbeafe; color: #1e40af; }
        .badge-mantenimiento{ background: #fce7f3; color: #9d174d; }
        .badge-activo       { background: #fef3c7; color: #92400e; }
        .badge-completado   { background: #d1fae5; color: #065f46; }
        .badge-cancelado    { background: #fee2e2; color: #991b1b; }

        /* ── Palomita Liberation ── */
        .liberation-btn {
            width: 32px; height: 32px;
            border-radius: 50%;
            border: 2px solid #d1d5db;
            background: #fff;
            display: grid; place-items: center;
            cursor: pointer; transition: all .2s;
            color: transparent;
        }
        .liberation-btn.returned { border-color: var(--primary); background: var(--primary); color: #fff; }
        .liberation-btn:hover { transform: scale(1.15); }

        /* ── Forms ── */
        .form-control, .form-select {
            border-color: #e2e8f0;
            border-radius: 8px;
            font-size: .875rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(5,150,105,.15);
        }
        .btn-primary-custom {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
            border-radius: 8px;
            font-weight: 600;
            font-size: .875rem;
        }
        .btn-primary-custom:hover { background: var(--primary-dark); border-color: var(--primary-dark); color: #fff; }

        /* ── Responsive sidebar ── */
        @media (max-width: 991.98px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); }
            #main-content { margin-left: 0; }
            .sidebar-overlay { display: block !important; }
        }
        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,.5);
            z-index: 1039;
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- Sidebar -->
<nav id="sidebar">
    <a href="{{ route('dashboard') }}" class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-tablet"></i></div>
        <div>
            <div class="brand-name">Control Tabletas</div>
            <div class="brand-sub">Colegio de Bachilleres</div>
        </div>
    </a>

    <div class="sidebar-nav">
        <div class="nav-label">Principal</div>
        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> <span>Dashboard</span>
        </a>

        <div class="nav-label mt-2">Inventario</div>
        <a href="{{ route('devices.index') }}" class="sidebar-link {{ request()->routeIs('devices.*') ? 'active' : '' }}">
            <i class="bi bi-tablet-fill"></i> <span>Dispositivos</span>
        </a>
        <a href="{{ route('staff.index') }}" class="sidebar-link {{ request()->routeIs('staff.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> <span>Personal</span>
        </a>
        <a href="{{ route('locations.index') }}" class="sidebar-link {{ request()->routeIs('locations.*') ? 'active' : '' }}">
            <i class="bi bi-geo-alt-fill"></i> <span>Sedes</span>
        </a>

        <div class="nav-label mt-2">Operaciones</div>
        <a href="{{ route('events.index') }}" class="sidebar-link {{ request()->routeIs('events.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-event-fill"></i> <span>Exacers / Periodos</span>
        </a>
        <a href="{{ route('assignments.index') }}" class="sidebar-link {{ request()->routeIs('assignments.index') || request()->routeIs('assignments.show') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-check-fill"></i> <span>Vales de Resguardo</span>
        </a>
        <a href="{{ route('assignments.create') }}" class="sidebar-link {{ request()->routeIs('assignments.create') ? 'active' : '' }}">
            <i class="bi bi-plus-circle-fill"></i> <span>Nuevo Vale Exacer</span>
        </a>

        <div class="nav-label mt-2">Asignaciones Fijas</div>
        <a href="{{ route('permanent.index') }}" class="sidebar-link {{ request()->routeIs('permanent.index') || request()->routeIs('permanent.show') ? 'active' : '' }}">
            <i class="bi bi-person-badge-fill"></i> <span>Jefes / Permanentes</span>
        </a>
        <a href="{{ route('permanent.create') }}" class="sidebar-link {{ request()->routeIs('permanent.create') ? 'active' : '' }}">
            <i class="bi bi-person-plus-fill"></i> <span>Nueva Asignación Fija</span>
        </a>

    </div>
</nav>

<!-- Main Content -->
<div id="main-content">
    <!-- Topbar -->
    <div class="topbar">
        <button class="btn btn-sm btn-light d-lg-none me-2" onclick="openSidebar()">
            <i class="bi bi-list fs-5"></i>
        </button>
        <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
        <div class="ms-auto d-flex align-items-center gap-2">
            <span class="badge bg-success-subtle text-success fw-semibold">
                <i class="bi bi-circle-fill" style="font-size:.5rem;"></i> Sistema activo
            </span>
        </div>
    </div>

    <!-- Flash Messages -->
    <div class="page-body">
        @if(session('success'))
            <div class="alert flash-alert d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 rounded-3 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Por favor corrija los siguientes errores:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function openSidebar()  { document.getElementById('sidebar').classList.add('open'); document.getElementById('sidebarOverlay').style.display = 'block'; }
    function closeSidebar() { document.getElementById('sidebar').classList.remove('open'); document.getElementById('sidebarOverlay').style.display = 'none'; }
</script>
@stack('scripts')
</body>
</html>
