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
        <div class="brand-logo" style="background: transparent;">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;">
        </div>
        <div class="brand-text">
            <span class="brand-title">SIPENCUTI</span>
            <span class="brand-sub">Dinas Kelautan & Perikanan</span>
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
                <a href="{{ route('admin.calendar') }}" class="nav-link {{ request()->routeIs('admin.calendar') ? 'active' : '' }}">
                    <i class="bi bi-calendar-week"></i>
                    <span>Kalender Cuti</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.pengajuan.index') }}" class="nav-link {{ request()->routeIs('admin.pengajuan.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Pengajuan Cuti</span>
                    @php $menunggu = \App\Models\PengajuanCuti::where('status','menunggu')->count(); @endphp
                    <span id="badgeSidebarAdmin" class="nav-badge {{ $menunggu > 0 ? '' : 'd-none' }}">{{ $menunggu }}</span>
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
                <a href="{{ route('pegawai.calendar') }}" class="nav-link {{ request()->routeIs('pegawai.calendar') ? 'active' : '' }}">
                    <i class="bi bi-calendar-week"></i>
                    <span>Kalender Cuti</span>
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
<script src="{{ asset('js/app.js') }}"></script>

@stack('scripts')

@if(Auth::check() && Auth::user()->isAdmin())
<!-- Toast Notifikasi Real-time Admin -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1090;">
    <div id="liveNotificationToast" class="toast border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-warning text-dark fw-bold">
            <i class="bi bi-bell-fill me-2 fs-5 text-dark"></i>
            <strong class="me-auto" id="toastTitle">Pengajuan Cuti Baru!</strong>
            <small id="toastTime" class="text-dark-50">Baru saja</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body bg-white text-dark">
            <div id="toastBodyText" class="fw-semibold mb-1"></div>
            <div id="toastSubText" class="small text-muted mb-2"></div>
            <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-sm btn-primary w-100">
                <i class="bi bi-eye me-1"></i>Lihat Pengajuan
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let lastSeenId = sessionStorage.getItem('last_seen_pengajuan_id') || 0;
    
    function playChime() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(587.33, ctx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.15);
            gain.gain.setValueAtTime(0.3, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + 0.5);
        } catch(e) {}
    }

    function checkLiveNotifications() {
        fetch("{{ route('admin.check-notifications') }}")
            .then(res => res.json())
            .then(data => {
                // Update badge sidebar otomatis
                const badgeEl = document.getElementById('badgeSidebarAdmin');
                if (badgeEl) {
                    if (data.unread_count > 0) {
                        badgeEl.innerText = data.unread_count;
                        badgeEl.classList.remove('d-none');
                    } else {
                        badgeEl.classList.add('d-none');
                    }
                }

                if (data.latest_pengajuan) {
                    const latest = data.latest_pengajuan;
                    
                    if (parseInt(lastSeenId) === 0) {
                        sessionStorage.setItem('last_seen_pengajuan_id', latest.id);
                        lastSeenId = latest.id;
                    } else if (latest.id > parseInt(lastSeenId)) {
                        sessionStorage.setItem('last_seen_pengajuan_id', latest.id);
                        lastSeenId = latest.id;

                        document.getElementById('toastBodyText').innerText = latest.nama_pegawai + ' mengajukan ' + latest.jenis_cuti;
                        document.getElementById('toastSubText').innerText = 'Durasi: ' + latest.lama_cuti + ' (' + latest.waktu + ')';
                        
                        const toastEl = document.getElementById('liveNotificationToast');
                        const toast = new bootstrap.Toast(toastEl, { delay: 12000 });
                        toast.show();
                        
                        playChime();
                    }
                }
            })
            .catch(err => console.log(err));
    }

    setInterval(checkLiveNotifications, 10000);
    setTimeout(checkLiveNotifications, 1500);
});
</script>
@endif
</body>
</html>