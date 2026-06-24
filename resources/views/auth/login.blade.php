<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Cuti DKP Lampung</title>
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
                <i class="bi bi-water"></i>
            </div>
            <h1 class="login-title">CUTI DKP LAMPUNG</h1>
            <p class="login-subtitle">Sistem Informasi Pengajuan Cuti Pegawai</p>
            <div class="login-divider"></div>
            <p class="login-desc">
                Dinas Kelautan dan Perikanan<br>
                Provinsi Lampung
            </p>
            <div class="login-waves">
                <div class="wave wave1"></div>
                <div class="wave wave2"></div>
                <div class="wave wave3"></div>
            </div>
        </div>
    </div>

    <!-- Right Panel -->
    <div class="login-panel-right">
        <div class="login-form-wrapper">

            <!-- Mobile Header -->
            <div class="text-center d-lg-none mb-4">
                <div class="login-brand-icon-sm mx-auto mb-2">
                    <i class="bi bi-water"></i>
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