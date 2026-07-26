<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wajib Ubah Password - Si-Cuti DKP Lampung</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .card-change {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 1.25rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            max-width: 460px;
            width: 100%;
            overflow: hidden;
        }
        .header-box {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #fff;
            padding: 2rem;
            text-align: center;
        }
        .btn-submit {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(2, 132, 199, 0.4);
            color: #fff;
        }
    </style>
</head>
<body>

<div class="card-change">
    <div class="header-box">
        <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-20 rounded-circle p-3 mb-3">
            <i class="bi bi-shield-lock-fill fs-2"></i>
        </div>
        <h4 class="fw-bold mb-1">Aktivasi Akun Pertama</h4>
        <p class="mb-0 small text-white-50">Silakan ubah password default 4-digit NIP Anda demi keamanan data pegawai.</p>
    </div>

    <div class="p-4 p-md-5">
        @if (session('warning'))
            <div class="alert alert-warning border-0 bg-warning bg-opacity-10 text-warning-emphasis rounded-3 small mb-4">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('warning') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger-emphasis rounded-3 small mb-4">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.update.first') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">NIP & Nama Pegawai</label>
                <input type="text" class="form-control bg-light" value="{{ Auth::user()->nip }} - {{ Auth::user()->name }}" readonly>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label small fw-semibold text-secondary">Password Baru <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-key text-muted"></i></span>
                    <input type="password" name="password" id="password" class="form-control border-start-0" placeholder="Minimal 6 karakter" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label small fw-semibold text-secondary">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-key-fill text-muted"></i></span>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control border-start-0" placeholder="Ulangi password baru" required>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-submit">
                    <i class="bi bi-check-circle-fill me-2"></i>Simpan & Lanjutkan
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
