<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - PT Arung Laut Nusantara</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    @php
        $user = auth()->user();
        $isAdmin = $user?->isAdmin();
        $isHrd = $user?->isHRD();
        $isEmployee = $user?->isEmployee();
        $pageTitle = trim($__env->yieldContent('page-title', $__env->yieldContent('title', 'Dashboard')));
        $pageKicker = trim($__env->yieldContent('page-kicker', 'Attendance Operations'));
        $pageSubtitle = trim($__env->yieldContent('page-subtitle', 'Pantau data, keputusan, dan aktivitas harian dari satu tampilan yang lebih rapi.'));

        $navigation = match (true) {
            $isAdmin => [
                [
                    'section' => 'Control Center',
                    'items' => [
                        ['label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard'],
                        ['label' => 'Data Karyawan', 'icon' => 'bi-people', 'route' => 'admin.employees', 'active' => 'admin.employees*'],
                        ['label' => 'Jadwal Shift', 'icon' => 'bi-clock-history', 'route' => 'admin.shifts', 'active' => 'admin.shifts*'],
                        ['label' => 'Kehadiran', 'icon' => 'bi-calendar-check', 'route' => 'admin.attendances', 'active' => 'admin.attendances*'],
                        ['label' => 'QR Shift', 'icon' => 'bi-qr-code', 'route' => 'admin.qr-code', 'active' => 'admin.qr-code*'],
                    ],
                ],
            ],
            $isHrd => [
                [
                    'section' => 'Monitoring',
                    'items' => [
                        ['label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'route' => 'hrd.dashboard', 'active' => 'hrd.dashboard'],
                        ['label' => 'Laporan Kehadiran', 'icon' => 'bi-file-earmark-bar-graph', 'route' => 'hrd.attendance-report', 'active' => 'hrd.attendance-report*'],
                        ['label' => 'Pengajuan Cuti', 'icon' => 'bi-calendar2-week', 'route' => 'hrd.leave-requests', 'active' => 'hrd.leave-requests*'],
                        ['label' => 'Statistik', 'icon' => 'bi-graph-up-arrow', 'route' => 'hrd.statistics', 'active' => 'hrd.statistics*'],
                    ],
                ],
            ],
            default => [
                [
                    'section' => 'My Workspace',
                    'items' => [
                        ['label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'route' => 'employee.dashboard', 'active' => 'employee.dashboard'],
                        ['label' => 'Scan Absensi', 'icon' => 'bi-qr-code-scan', 'route' => 'employee.scanner', 'active' => 'employee.scanner'],
                        ['label' => 'Riwayat Absensi', 'icon' => 'bi-clock-history', 'route' => 'employee.attendance-history', 'active' => 'employee.attendance-history*'],
                        ['label' => 'Pengajuan Cuti', 'icon' => 'bi-calendar-event', 'route' => 'employee.leave-requests', 'active' => 'employee.leave-requests*'],
                        ['label' => 'Notifikasi', 'icon' => 'bi-bell', 'route' => 'employee.notifications', 'active' => 'employee.notifications*'],
                        ['label' => 'Profil', 'icon' => 'bi-person-badge', 'route' => 'employee.profile', 'active' => 'employee.profile*'],
                    ],
                ],
            ],
        };
    @endphp

    <style>
        :root {
            --sidebar-width: 304px;
            --primary: #c97570;
            --primary-dark: #9b5e58;
            --primary-soft: rgba(201, 117, 112, 0.14);
            --primary-mist: rgba(201, 117, 112, 0.08);
            --accent: #e0a19d;
            --accent-dark: #7f4743;
            --surface: #f8f1ef;
            --surface-strong: #efe0db;
            --panel: rgba(255, 255, 255, 0.88);
            --ink: #4a2f32;
            --muted: #816568;
            --success: #4f8a66;
            --warning: #c88a4d;
            --danger: #ba5d57;
            --info: #8a7fc5;
            --border-soft: rgba(139, 101, 105, 0.14);
            --shadow-lg: 0 24px 55px rgba(87, 52, 57, 0.12);
            --shadow-md: 0 16px 36px rgba(87, 52, 57, 0.09);
            --radius-xl: 30px;
            --radius-lg: 24px;
            --radius-md: 18px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(224, 161, 157, 0.22), transparent 24%),
                radial-gradient(circle at bottom right, rgba(201, 117, 112, 0.18), transparent 26%),
                linear-gradient(180deg, #fbf6f4 0%, #f6eeeb 100%);
        }

        a { text-decoration: none; }

        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 1035;
            padding: 1rem;
            background: linear-gradient(180deg, #5f383d 0%, #875055 38%, #c97570 100%);
            box-shadow: 18px 0 48px rgba(74, 47, 50, 0.14);
            display: flex;
            flex-direction: column;
        }

        .sidebar-shell {
            height: 100%;
            border-radius: 30px;
            background: linear-gradient(180deg, rgba(255,255,255,0.12), rgba(255,255,255,0.04));
            border: 1px solid rgba(255,255,255,0.12);
            backdrop-filter: blur(14px);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            padding: 1.35rem 1.35rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-brand-badge {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            display: grid;
            place-items: center;
            color: #fff7f5;
            background: rgba(255,255,255,0.14);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.2);
            font-size: 1.35rem;
        }

        .sidebar-brand h5 {
            margin: 0;
            color: #fff8f7;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .sidebar-brand small {
            color: rgba(255, 246, 244, 0.72);
        }

        .sidebar-nav {
            padding: 1rem 0.75rem;
            overflow: auto;
            flex: 1;
        }

        .nav-section {
            color: rgba(255, 241, 239, 0.55);
            font-size: 0.68rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            font-weight: 700;
            padding: 0.95rem 0.9rem 0.35rem;
        }

        .sidebar-link {
            color: rgba(255, 248, 246, 0.88);
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.84rem 0.95rem;
            border-radius: 18px;
            margin-bottom: 0.3rem;
            font-weight: 600;
            transition: all 0.22s ease;
        }

        .sidebar-link i {
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            background: rgba(255,255,255,0.1);
            font-size: 1.1rem;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            color: #fff;
            background: rgba(255,255,255,0.14);
            transform: translateX(4px);
        }

        .sidebar-link:hover i,
        .sidebar-link.active i {
            background: rgba(255,255,255,0.19);
        }

        .sidebar-footer {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding: 1rem 1.15rem 1.2rem;
        }

        .sidebar-profile {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            margin-bottom: 1rem;
        }

        .avatar-fallback {
            width: 46px;
            height: 46px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            color: #fff;
            font-weight: 800;
            background: rgba(255,255,255,0.14);
        }

        #main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 1020;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 1.1rem 1.5rem 0;
        }

        .topbar-inner {
            width: 100%;
            padding: 1rem 1.15rem;
            border-radius: var(--radius-xl);
            border: 1px solid rgba(201, 117, 112, 0.12);
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(12px);
            box-shadow: var(--shadow-md);
        }

        .page-kicker {
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.18em;
            font-size: 0.68rem;
            font-weight: 700;
        }

        .page-title {
            margin: 0;
            color: var(--ink);
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: -0.035em;
        }

        .page-subtitle {
            color: var(--muted);
            margin: 0.18rem 0 0;
        }

        .content-area {
            padding: 1.5rem;
        }

        .card,
        .dashboard-panel,
        .hero-panel,
        .filter-card,
        .summary-card {
            border: 1px solid var(--border-soft);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            background: var(--panel);
            backdrop-filter: blur(10px);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(129, 101, 104, 0.1);
            padding: 1.15rem 1.3rem;
        }

        .card-body { padding: 1.3rem; }

        .hero-panel {
            padding: 1.45rem;
            color: #fff9f8;
            background: linear-gradient(135deg, #8b5557 0%, #c97570 64%, #df9a95 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-panel::after {
            content: '';
            position: absolute;
            width: 280px;
            height: 280px;
            right: -90px;
            bottom: -120px;
            background: radial-gradient(circle, rgba(255,255,255,0.18), transparent 70%);
        }

        .hero-panel .muted {
            color: rgba(255,248,247,0.8);
        }

        .stat-card {
            height: 100%;
            border-radius: 24px;
            padding: 1.15rem;
            background: rgba(255,255,255,0.94);
            border: 1px solid var(--border-soft);
            box-shadow: 0 16px 30px rgba(87, 52, 57, 0.06);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--accent) 100%);
        }

        .stat-card.success::before { background: linear-gradient(90deg, #4f8a66 0%, #7ab18b 100%); }
        .stat-card.warning::before { background: linear-gradient(90deg, #c88a4d 0%, #dfb37a 100%); }
        .stat-card.danger::before { background: linear-gradient(90deg, #ba5d57 0%, #d98b85 100%); }
        .stat-card.info::before { background: linear-gradient(90deg, #8a7fc5 0%, #b7a8df 100%); }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 18px;
            display: grid;
            place-items: center;
            font-size: 1.35rem;
            background: var(--primary-soft);
            color: var(--primary);
            margin-bottom: 0.9rem;
        }

        .stat-value {
            font-size: 1.95rem;
            line-height: 1;
            color: var(--ink);
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .stat-label {
            color: var(--muted);
            margin-top: 0.35rem;
            font-size: 0.92rem;
        }

        .stat-helper {
            color: #9b7f82;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        .quick-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border-radius: 18px;
            border: 1px solid rgba(129, 101, 104, 0.1);
            background: rgba(255,255,255,0.72);
            color: inherit;
            transition: all 0.22s ease;
        }

        .quick-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 26px rgba(87, 52, 57, 0.08);
        }

        .quick-link-icon {
            width: 46px;
            height: 46px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            font-size: 1.2rem;
        }

        .soft-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.55rem 0.85rem;
            border-radius: 999px;
            background: var(--primary-mist);
            color: var(--accent-dark);
            font-size: 0.82rem;
            font-weight: 600;
        }

        .filter-card {
            padding: 1.1rem;
        }

        .filter-card .form-control,
        .filter-card .form-select,
        .card .form-control,
        .card .form-select {
            border-radius: 14px;
            border-color: rgba(129, 101, 104, 0.18);
            min-height: 46px;
            box-shadow: none;
        }

        .filter-card .form-control:focus,
        .filter-card .form-select:focus,
        .card .form-control:focus,
        .card .form-select:focus {
            border-color: rgba(201, 117, 112, 0.44);
            box-shadow: 0 0 0 0.2rem rgba(201, 117, 112, 0.12);
        }

        .btn {
            border-radius: 14px;
            font-weight: 700;
            padding: 0.72rem 1.2rem;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
        }

        .btn-outline-primary {
            border: 1px solid rgba(201, 117, 112, 0.28);
            color: var(--primary-dark);
            background: rgba(255,255,255,0.72);
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            color: #fff;
        }

        .table thead th {
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #8b6d71;
            border-bottom-width: 1px;
        }

        .table > :not(caption) > * > * {
            background: transparent;
            padding-top: 0.95rem;
            padding-bottom: 0.95rem;
            border-color: rgba(129, 101, 104, 0.08);
        }

        .table tbody tr:hover {
            background: rgba(201, 117, 112, 0.04);
        }

        .badge {
            border-radius: 999px;
            font-weight: 700;
            padding: 0.55rem 0.8rem;
        }

        .badge-soft-primary { background: rgba(201,117,112,.14); color: #8b5557; }
        .badge-soft-success { background: rgba(79,138,102,.14); color: #38694a; }
        .badge-soft-warning { background: rgba(200,138,77,.14); color: #9a6731; }
        .badge-soft-info { background: rgba(138,127,197,.14); color: #6358a0; }
        .badge-soft-danger { background: rgba(186,93,87,.14); color: #984b46; }

        .alert {
            border: none;
            border-radius: 20px;
            box-shadow: 0 14px 28px rgba(87, 52, 57, 0.06);
        }

        .chart-shell {
            position: relative;
            min-height: 320px;
        }

        .top-actions .btn {
            padding-top: 0.65rem;
            padding-bottom: 0.65rem;
        }

        .data-summary {
            display: grid;
            gap: 1rem;
        }

        .data-summary-item {
            border-radius: 18px;
            padding: 1rem;
            background: linear-gradient(180deg, rgba(255,255,255,0.8), rgba(250,244,242,0.9));
            border: 1px solid rgba(129, 101, 104, 0.1);
        }

        .empty-state {
            padding: 2.5rem 1rem;
            text-align: center;
            color: var(--muted);
        }

        .empty-state i {
            font-size: 3rem;
            color: rgba(201, 117, 112, 0.6);
            margin-bottom: 0.75rem;
        }

        .select2-container--bootstrap-5 .select2-selection {
            border-radius: 14px;
            border-color: rgba(129, 101, 104, 0.18);
            min-height: 46px;
            padding-top: 0.28rem;
        }

        @media (max-width: 991.98px) {
            #sidebar {
                transform: translateX(-100%);
                transition: transform 0.28s ease;
            }

            #sidebar.open {
                transform: translateX(0);
            }

            #main-content {
                margin-left: 0;
            }

            .topbar,
            .content-area {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <nav id="sidebar">
        <div class="sidebar-shell">
            <div class="sidebar-brand">
                <div class="d-flex align-items-center gap-3">
                    <div class="sidebar-brand-badge">
                        <i class="bi {{ $isAdmin ? 'bi-grid-1x2-fill' : ($isHrd ? 'bi-clipboard-data-fill' : 'bi-qr-code-scan') }}"></i>
                    </div>
                    <div>
                        <h5>{{ $isAdmin ? 'Admin Center' : ($isHrd ? 'HRD Insight Hub' : 'Attendance Desk') }}</h5>
                        <small>PT Arung Laut Nusantara</small>
                    </div>
                </div>
            </div>

            <div class="sidebar-nav">
                @foreach($navigation as $group)
                    <div class="nav-section">{{ $group['section'] }}</div>
                    @foreach($group['items'] as $item)
                        <a href="{{ route($item['route']) }}" class="sidebar-link {{ request()->routeIs($item['active']) ? 'active' : '' }}">
                            <i class="bi {{ $item['icon'] }}"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                @endforeach
            </div>

            <div class="sidebar-footer">
                <div class="sidebar-profile">
                    <div class="avatar-fallback">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    <div>
                        <div class="text-white fw-semibold">{{ \Illuminate\Support\Str::limit($user->name, 20) }}</div>
                        <div class="small text-white-50">{{ getRoleText($user->role) }}</div>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    @if($isEmployee)
                        <a href="{{ route('employee.profile') }}" class="btn btn-sm btn-light">
                            <i class="bi bi-person-circle me-1"></i>Profil Saya
                        </a>
                    @else
                        <a href="{{ route('change-password') }}" class="btn btn-sm btn-light">
                            <i class="bi bi-key me-1"></i>Ubah Password
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-sm w-100 text-white" style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);">
                            <i class="bi bi-box-arrow-left me-1"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div id="main-content">
        <div class="topbar">
            <div class="topbar-inner">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                    <div class="d-flex align-items-start gap-3">
                        <button class="btn btn-outline-secondary d-lg-none" type="button" onclick="document.getElementById('sidebar').classList.toggle('open')">
                            <i class="bi bi-list"></i>
                        </button>
                        <div>
                            <div class="page-kicker">{{ $pageKicker }}</div>
                            <h1 class="page-title">{{ $pageTitle }}</h1>
                            <p class="page-subtitle mb-0">{{ $pageSubtitle }}</p>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2 top-actions">
                        <span class="soft-chip">
                            <i class="bi bi-calendar3"></i>{{ now()->translatedFormat('d M Y') }}
                        </span>
                        <span class="soft-chip">
                            <i class="bi bi-clock-history"></i><span id="layoutCurrentTime">{{ now()->format('H:i') }}</span>
                        </span>
                        <span class="soft-chip">
                            <i class="bi bi-person-circle"></i>{{ getRoleText($user->role) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-area">
            @if(session('success'))
                <div class="alert alert-success mb-4">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger mb-4">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <div class="fw-semibold mb-1"><i class="bi bi-exclamation-octagon me-2"></i>Terdapat kesalahan input</div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            $('.select2').select2({ theme: 'bootstrap-5' });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
        });

        function updateLayoutClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const target = document.getElementById('layoutCurrentTime');
            if (target) {
                target.textContent = `${hours}:${minutes}`;
            }
        }

        setInterval(updateLayoutClock, 1000);
        updateLayoutClock();

        window.appDeleteConfirm = function (callback, options = {}) {
            Swal.fire({
                title: options.title || 'Yakin ingin menghapus?',
                text: options.text || 'Data yang dihapus tidak dapat dipulihkan kembali.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ba5d57',
                cancelButtonColor: '#8b6d71',
                confirmButtonText: options.confirmText || 'Ya, hapus',
                cancelButtonText: options.cancelText || 'Batal'
            }).then(result => {
                if (result.isConfirmed) {
                    callback();
                }
            });
        };
    </script>

    @stack('scripts')
</body>
</html>
