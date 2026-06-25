<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator — Cuti DKP Lampung</title>
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
            <h1 class="login-title">ADMIN PANEL</h1>
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
                <div class="login-brand-icon-sm mx-auto mb-2" style="background: transparent;">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
                <h4 class="fw-bold" style="color: var(--primary)">Cuti DKP Lampung</h4>
                <p class="text-muted small">DKP Provinsi Lampung</p>
            </div>

            <h2 class="login-heading">Login Administrator</h2>
            <p class="login-sub-heading">Masuk menggunakan NIP dan password admin Anda</p>

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" autocomplete="off">
                @csrf
                <input type="hidden" name="login_type" value="admin">

                <div class="form-group mb-4">
                    <label class="form-label fw-semibold" for="nip">
                        <i class="bi bi-person-badge me-1"></i>NIP Administrator
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
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Ingat saya di perangkat ini</label>
                </div>

                <button type="submit" class="btn btn-login w-100 mb-3">
                    <i class="bi bi-shield-lock-fill me-2"></i>Masuk Ke Admin Panel
                </button>

                <div class="text-center">
                    <a href="javascript:void(0)" id="btnAutofillAdmin" class="text-decoration-none text-muted small d-block mb-2" style="font-weight: 500;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='#6c757d'">
                        <i class="bi bi-magic me-1"></i>Gunakan Akun Demo Admin
                    </a>
                    <a href="{{ route('login') }}" class="text-decoration-none text-primary small d-block mt-3" style="font-weight: 500;">
                        <i class="bi bi-arrow-left me-1"></i>Masuk sebagai Pegawai
                    </a>
                </div>
            </form>

            <p class="text-center text-muted" style="font-size: 0.75rem; margin-top: 3rem;">
                © {{ date('Y') }} Dinas Kelautan dan Perikanan Provinsi Lampung<br>
                Cuti DKP Lampung v1.0 — Hak Cipta Dilindungi
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

    document.getElementById('btnAutofillAdmin').addEventListener('click', function (e) {
        e.preventDefault();
        document.getElementById('nip').value = '198501012010011001';
        document.getElementById('password').value = 'Admin@DKP2026';
        
        const loginBtn = document.querySelector('.btn-login');
        loginBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...';
        loginBtn.disabled = true;
        
        setTimeout(() => {
            loginBtn.closest('form').submit();
        }, 400);
    });
</script>
</body>
</html>
