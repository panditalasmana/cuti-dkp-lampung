<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Pegawai;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// ─── Root Redirect & Home Fallback ──────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

Route::get('/fix-folders', function () {
    $dir = app_path('Http/Controllers');
    $log = [];
    if (file_exists($dir . '/admin') && !file_exists($dir . '/Admin')) {
        @rename($dir . '/admin', $dir . '/Admin');
        $log[] = 'Renamed admin -> Admin';
    }
    if (file_exists($dir . '/pegawai') && !file_exists($dir . '/Pegawai')) {
        @rename($dir . '/pegawai', $dir . '/Pegawai');
        $log[] = 'Renamed pegawai -> Pegawai';
    }

    \Illuminate\Support\Facades\DB::table('pegawai')->update(['sisa_cuti_tahunan' => 12]);
    $log[] = 'Sisa cuti tahunan SELURUH PEGAWAI BERHASIL DI-RESET MENJADI 12 HARI!';

    return '<h2>✅ FIX CONTROLLER COMPLETED!</h2><p>' . (empty($log) ? 'Folder Admin & Pegawai status: OK' : implode('<br>', $log)) . '</p><br><a href="/login">Buka Halaman Login / Dashboard</a>';
});

Route::get('/update-admin', function () {
    $admin = \App\Models\User::where('role', 'admin')->first();
    if ($admin) {
        $admin->update([
            'nip'            => 'admindkp2026',
            'password'       => \Illuminate\Support\Facades\Hash::make('1991'),
            'password_plain' => '1991',
        ]);
        return '<h2>✅ CREDENTIAL ADMIN BERHASIL DIPERBARUI!</h2><p>Username: <b>admindkp2026</b><br>Password: <b>1991</b></p><br><a href="/admin/login">Buka Halaman Login Admin</a>';
    }
    return 'User Admin tidak ditemukan.';
});

// ─── Storage File Fallback Route (100% Symlink Free untuk Hostinger) ───────────
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) {
        $fullPath = storage_path('app/' . $path);
    }
    if (!file_exists($fullPath) || is_dir($fullPath)) {
        abort(404, 'File dokumen tidak ditemukan.');
    }
    $mime = @mime_content_type($fullPath) ?: 'application/octet-stream';
    return response()->file($fullPath, [
        'Content-Type' => $mime,
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('path', '.*');

Route::get('/home', function () {
    if (Auth::check()) {
        return match (Auth::user()->role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'pegawai' => redirect()->route('pegawai.dashboard'),
            default   => redirect()->route('login'),
        };
    }
    return redirect()->route('login');
})->name('home');

// ─── Authentication ────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.post');
    Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
});

Route::post('/logout', [AuthController::class, 'logout'])
     ->middleware('auth')
     ->name('logout');

// Shared Auth Routes (Admin & Pegawai)
Route::middleware('auth')->group(function () {
    Route::get('/kalender/events', [Admin\DashboardController::class, 'calendarEvents'])->name('calendar.events');
    Route::get('/dokumen/view/{dokumen}', [\App\Http\Controllers\DokumenController::class, 'view'])->name('dokumen.view');
    Route::get('/dokumen/download/{dokumen}', [\App\Http\Controllers\DokumenController::class, 'download'])->name('dokumen.download');
    Route::get('/foto/view/{pegawai}', [\App\Http\Controllers\DokumenController::class, 'viewFoto'])->name('foto.view');
});

// ─── Admin Routes ──────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])
     ->prefix('admin')
     ->name('admin.')
     ->group(function () {

    // Dashboard & Kalender
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/kalender',  [Admin\DashboardController::class, 'calendar'])->name('calendar');
    Route::get('/check-notifications', [Admin\DashboardController::class, 'checkNotifications'])->name('check-notifications');

    // Master: Bidang
    Route::resource('bidang', Admin\BidangController::class);

    // Master: Jabatan
    Route::resource('jabatan', Admin\JabatanController::class)->except(['show']);

    // Master: Pegawai
    Route::post('pegawai/import', [Admin\PegawaiController::class, 'import'])->name('pegawai.import');
    Route::get('pegawai/download-template', [Admin\PegawaiController::class, 'downloadTemplate'])->name('pegawai.download-template');
    Route::get('pegawai/export', [Admin\PegawaiController::class, 'export'])->name('pegawai.export');
    Route::get('pegawai/export-akun', [Admin\PegawaiController::class, 'exportAkun'])->name('pegawai.export-akun');
    Route::resource('pegawai', Admin\PegawaiController::class);

    // Master: Jenis Cuti
    Route::resource('jenis-cuti', Admin\JenisCutiController::class)->except(['show']);

    // Master: Hari Libur
    Route::resource('hari-libur', Admin\HariLiburController::class)->except(['show']);

    // Master: Pejabat Penandatangan
    Route::post('penandatangan/{penandatangan}/set-default', [Admin\PenandatanganController::class, 'setDefault'])->name('penandatangan.set-default');
    Route::resource('penandatangan', Admin\PenandatanganController::class)->except(['show']);

    // Pengajuan Cuti
    Route::prefix('pengajuan')->name('pengajuan.')->group(function () {
        Route::get('/',                                    [Admin\PengajuanController::class, 'index'])->name('index');
        Route::get('/{pengajuan}',                         [Admin\PengajuanController::class, 'show'])->name('show');
        Route::post('/{pengajuan}/verifikasi',             [Admin\PengajuanController::class, 'verifikasi'])->name('verifikasi');
        Route::post('/{pengajuan}/upload-scan',            [Admin\PengajuanController::class, 'uploadScan'])->name('upload-scan');
        Route::get('/{pengajuan}/preview-pdf',             [Admin\PengajuanController::class, 'previewPdf'])->name('preview-pdf');
        Route::delete('/{pengajuan}',                      [Admin\PengajuanController::class, 'destroy'])->name('destroy');
    });

    // Laporan
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/',               [Admin\PengajuanController::class, 'laporan'])->name('index');
        Route::get('/export-pdf',     [Admin\PengajuanController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/export-excel',   [Admin\PengajuanController::class, 'exportExcel'])->name('export-excel');
        Route::get('/export-zip',     [Admin\PengajuanController::class, 'exportZip'])->name('export-zip');
    });

    // Activity Log
    Route::get('/activity-log', [Admin\ActivityLogController::class, 'index'])->name('activity-log.index');

    // Panduan Sistem Admin
    Route::get('/panduan', [Admin\DashboardController::class, 'panduan'])->name('panduan');
});

// ─── Pegawai Routes ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:pegawai'])
     ->prefix('pegawai')
     ->name('pegawai.')
     ->group(function () {

    // Dashboard & Kalender
    Route::get('/dashboard', [Pegawai\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/kalender',  [Pegawai\DashboardController::class, 'calendar'])->name('calendar');
    Route::get('/kalender/events', [Pegawai\DashboardController::class, 'calendarEvents'])->name('calendar.events');
    Route::get('/panduan',   [Pegawai\DashboardController::class, 'panduan'])->name('panduan');

    // Pengajuan Cuti
    Route::prefix('pengajuan')->name('pengajuan.')->group(function () {
        Route::get('/buat',                      [Pegawai\PengajuanController::class, 'create'])->name('create');
        Route::post('/buat',                     [Pegawai\PengajuanController::class, 'store'])->name('store');
        Route::get('/{pengajuan}',               [Pegawai\PengajuanController::class, 'show'])->name('show');
        Route::get('/{pengajuan}/preview',       [Pegawai\PengajuanController::class, 'preview'])->name('preview');
        Route::get('/{pengajuan}/cetak',         [Pegawai\PengajuanController::class, 'cetak'])->name('cetak');
        Route::post('/{pengajuan}/batal',         [Pegawai\PengajuanController::class, 'batal'])->name('batal');
        Route::post('/hitung-hari',              [Pegawai\PengajuanController::class, 'hitungHari'])->name('hitung-hari');
    });

    // Riwayat
    Route::get('/riwayat', [Pegawai\PengajuanController::class, 'index'])->name('riwayat.index');

    // Profil
    Route::get('/profil',         [Pegawai\ProfilController::class, 'index'])->name('profil.index');
    Route::post('/profil',        [Pegawai\ProfilController::class, 'update'])->name('profil.update');
    Route::post('/profil/hapus-foto', [Pegawai\ProfilController::class, 'hapusFoto'])->name('profil.hapus-foto');
    Route::post('/ganti-password',[Pegawai\ProfilController::class, 'gantiPassword'])->name('profil.ganti-password');
});