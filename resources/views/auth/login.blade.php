<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SIPENCUTI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="login-body">

<div class="login-wrapper">

    <!-- Left Panel -->
    <div class="login-panel-left d-none d-lg-flex">
        <div class="login-left-content">
            <div class="login-brand-icon">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 100px; height: 100px; object-fit: contain;">
            </div>
            <h1 class="login-title">SIPENCUTI</h1>
            <p class="login-subtitle">Sistem Informasi Pengajuan Cuti Pegawai</p>
            <div class="login-divider"></div>
            <p class="login-desc">
                Dinas Kelautan dan Perikanan<br>
                Provinsi Lampung
            </p>
        </div>
        <div class="login-waves-container">
            <svg class="editorial-waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
                <defs>
                    <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s58 18 88 18 58-18 88-18 58 18 88 18v44h-352z" />
                    <linearGradient id="wave-grad-1" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="rgba(255, 255, 255, 0.2)" />
                        <stop offset="100%" stop-color="rgba(255, 255, 255, 0.0)" />
                    </linearGradient>
                    <linearGradient id="wave-grad-2" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="rgba(255, 255, 255, 0.15)" />
                        <stop offset="100%" stop-color="rgba(255, 255, 255, 0.0)" />
                    </linearGradient>
                    <linearGradient id="wave-grad-3" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="rgba(255, 255, 255, 0.08)" />
                        <stop offset="100%" stop-color="rgba(255, 255, 255, 0.0)" />
                    </linearGradient>
                    <linearGradient id="wave-grad-4" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="rgba(255, 255, 255, 0.25)" />
                        <stop offset="100%" stop-color="rgba(255, 255, 255, 0.0)" />
                    </linearGradient>
                </defs>
                <g class="parallax">
                    <use xlink:href="#gentle-wave" x="48" y="0" fill="url(#wave-grad-1)" />
                    <use xlink:href="#gentle-wave" x="48" y="3" fill="url(#wave-grad-2)" />
                    <use xlink:href="#gentle-wave" x="48" y="5" fill="url(#wave-grad-3)" />
                    <use xlink:href="#gentle-wave" x="48" y="7" fill="url(#wave-grad-4)" />
                </g>
            </svg>
        </div>
    </div>

    <!-- Right Panel -->
    <div class="login-panel-right">
        <div class="login-form-wrapper">

            <!-- Mobile Header -->
            <div class="text-center d-lg-none mb-4">
                <div class="login-brand-icon-sm mx-auto mb-2" style="background: transparent;">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
                <h4 class="fw-bold" style="color: var(--primary)">SIPENCUTI</h4>
                <p class="text-muted small">DKP Provinsi Lampung</p>
            </div>

            <h2 class="login-heading">Selamat Datang</h2>
            <p class="login-sub-heading">Masuk menggunakan NIP dan password Anda</p>

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" autocomplete="off">
                @csrf
                <input type="hidden" name="login_type" value="pegawai">

                <div class="form-group mb-4">
                    <label class="form-label fw-semibold" for="nip">
                        <i class="bi bi-person-badge me-1"></i>NIP (Nomor Induk Pegawai)
                    </label>
                    <input
                        type="text"
                        id="nip"
                        name="nip"
                        class="form-control form-control-lg @error('nip') is-invalid @enderror"
                        placeholder="Masukkan NIP Anda"
                        value="{{ old('nip') }}"
                        maxlength="18"
                        inputmode="numeric"
                        autofocus
                        required
                    >
                    @error('nip')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <label class="form-label fw-semibold" for="password">
                        <i class="bi bi-lock me-1"></i>Password
                    </label>
                    <div class="input-password-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control form-control-lg @error('password') is-invalid @enderror"
                            placeholder="Masukkan password"
                            required
                        >
                        <button type="button" class="btn-toggle-password" id="togglePassword">
                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                        </button>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Ingat saya di perangkat ini</label>
                </div>

                <button type="submit" class="btn btn-login w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk ke Sistem
                </button>

                <div class="text-center mt-3">
                    <a href="{{ route('admin.login') }}" class="text-decoration-none text-muted small" style="transition: all 0.2s; opacity: 0.8;" onmouseover="this.style.opacity='1'; this.style.color='var(--primary)'" onmouseout="this.style.opacity='0.8'; this.style.color='#6c757d'">
                        <i class="bi bi-shield-lock-fill me-1"></i>Masuk sebagai Admin
                    </a>
                </div>
            </form>

            <p class="login-footer-text">
                Lupa password? Hubungi Administrator DKP Lampung.
            </p>

            <p class="text-center text-muted" style="font-size: 0.75rem; margin-top: 2rem;">
                © {{ date('Y') }} Dinas Kelautan dan Perikanan Provinsi Lampung<br>
                SIPENCUTI v1.0 — Hak Cipta Dilindungi
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const pwd  = document.getElementById('password');
        const icon = document.getElementById('togglePasswordIcon');
        if (pwd.type === 'password') {
            pwd.type  = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            pwd.type  = 'password';
            icon.className = 'bi bi-eye';
        }
    });
</script>
</body>
</html>