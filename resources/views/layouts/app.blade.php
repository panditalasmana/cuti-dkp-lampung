<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIPENCUTI') — DKP Provinsi Lampung</title>

    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Flatpickr -->
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ═══ SIDEBAR ═══════════════════════════════════════════════════════════ -->
<nav class="sidebar" id="sidebar">
    <!-- Logo -->
    <div class="sidebar-brand">
        <div class="brand-logo">
            <i class="bi bi-water"></i>
        </div>
        <div class="brand-text">
            <span class="brand-title">SIPENCUTI</span>
            <span class="brand-sub">DKP Prov. Lampung</span>
        </div>
        <button class="sidebar-close d-lg-none" id="sidebarClose">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <!-- User Info -->
    <div class="sidebar-user">
        <div class="user-avatar">
            @if(Auth::user()->pegawai?->foto)
                <img src="{{ asset('storage/' . Auth::user()->pegawai->foto) }}" alt="foto">
            @else
                <i class="bi bi-person-fill"></i>
            @endif
        </div>
        <div class="user-info">
            <div class="user-name">{{ Str::limit(Auth::user()->name, 22) }}</div>
            <div class="user-role">
                @if(Auth::user()->isAdmin())
                    <span class="badge bg-warning text-dark">Administrator</span>
                @else
                    <span class="badge bg-info text-dark">Pegawai</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <ul class="sidebar-nav">
        @if(Auth::user()->isAdmin())
            {{-- ADMIN MENU --}}
            <li class="nav-label">Menu Utama</li>

            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.pengajuan.index') }}" class="nav-link {{ request()->routeIs('admin.pengajuan.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Pengajuan Cuti</span>
                    @php $menunggu = \App\Models\PengajuanCuti::where('status','menunggu')->count(); @endphp
                    @if($menunggu > 0)
                        <span class="nav-badge">{{ $menunggu }}</span>
                    @endif
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.laporan.index') }}" class="nav-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line"></i>
                    <span>Laporan & Statistik</span>
                </a>
            </li>

            <li class="nav-label">Master Data</li>

            <li class="nav-item">
                <a href="{{ route('admin.pegawai.index') }}" class="nav-link {{ request()->routeIs('admin.pegawai.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Data Pegawai</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.bidang.index') }}" class="nav-link {{ request()->routeIs('admin.bidang.*') ? 'active' : '' }}">
                    <i class="bi bi-diagram-3"></i>
                    <span>Data Bidang</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.jabatan.index') }}" class="nav-link {{ request()->routeIs('admin.jabatan.*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge"></i>
                    <span>Data Jabatan</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.jenis-cuti.index') }}" class="nav-link {{ request()->routeIs('admin.jenis-cuti.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check"></i>
                    <span>Jenis Cuti</span>
                </a>
            </li>

            <li class="nav-label">Sistem</li>

            <li class="nav-item">
                <a href="{{ route('admin.activity-log.index') }}" class="nav-link {{ request()->routeIs('admin.activity-log.*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i>
                    <span>Activity Log</span>
                </a>
            </li>

        @else
            {{-- PEGAWAI MENU --}}
            <li class="nav-label">Menu Utama</li>

            <li class="nav-item">
                <a href="{{ route('pegawai.dashboard') }}" class="nav-link {{ request()->routeIs('pegawai.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('pegawai.pengajuan.create') }}" class="nav-link {{ request()->routeIs('pegawai.pengajuan.create') ? 'active' : '' }}">
                    <i class="bi bi-plus-circle"></i>
                    <span>Ajukan Cuti</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('pegawai.riwayat.index') }}" class="nav-link {{ request()->routeIs('pegawai.riwayat.*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i>
                    <span>Riwayat Pengajuan</span>
                </a>
            </li>

            <li class="nav-label">Akun</li>

            <li class="nav-item">
                <a href="{{ route('pegawai.profil.index') }}" class="nav-link {{ request()->routeIs('pegawai.profil.*') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i>
                    <span>Profil Saya</span>
                </a>
            </li>
        @endif
    </ul>

    <!-- Logout -->
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="bi bi-box-arrow-right"></i>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</nav>

<!-- ═══ MAIN CONTENT ═══════════════════════════════════════════════════════ -->
<div class="main-wrapper" id="mainWrapper">

    <!-- Topbar -->
    <header class="topbar">
        <button class="topbar-toggle" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>

        <div class="topbar-breadcrumb">
            @yield('breadcrumb')
        </div>

        <div class="topbar-right">
            <span class="topbar-date d-none d-md-block">
                <i class="bi bi-calendar3 me-1"></i>
                {{ now()->isoFormat('dddd, D MMMM Y') }}
            </span>
        </div>
    </header>

    <!-- Page Content -->
    <main class="page-content">

        {{-- Global Alert Flash --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show alert-flash" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show alert-flash" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show alert-flash" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Terdapat kesalahan:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="page-footer">
        <span>© {{ date('Y') }} Dinas Kelautan dan Perikanan Provinsi Lampung. SIPENCUTI v1.0</span>
    </footer>
</div>

<!-- ═══ SCRIPTS ════════════════════════════════════════════════════════════ -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


@stack('scripts')
</body>
</html>