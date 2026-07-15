<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Pegawai;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// ─── Root Redirect & Home Fallback ──────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

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
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
});

Route::post('/logout', [AuthController::class, 'logout'])
     ->middleware('auth')
     ->name('logout');

// Shared Auth Routes (Admin & Pegawai)
Route::middleware('auth')->group(function () {
    Route::get('/kalender/events', [Admin\DashboardController::class, 'calendarEvents'])->name('calendar.events');
});

// ─── Admin Routes ──────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])
     ->prefix('admin')
     ->name('admin.')
     ->group(function () {

    // Dashboard & Kalender
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/kalender',  [Admin\DashboardController::class, 'calendar'])->name('calendar');
    Route::get('/gdrive/auth',     [Admin\DashboardController::class, 'gdriveAuth'])->name('gdrive.auth');
    Route::get('/gdrive/callback', [Admin\DashboardController::class, 'gdriveCallback'])->name('gdrive.callback');

    // Master: Bidang
    Route::resource('bidang', Admin\BidangController::class);

    // Master: Jabatan
    Route::resource('jabatan', Admin\JabatanController::class)->except(['show']);

    // Master: Pegawai
    Route::post('pegawai/import', [Admin\PegawaiController::class, 'import'])->name('pegawai.import');
    Route::get('pegawai/download-template', [Admin\PegawaiController::class, 'downloadTemplate'])->name('pegawai.download-template');
    Route::resource('pegawai', Admin\PegawaiController::class);

    // Master: Jenis Cuti
    Route::resource('jenis-cuti', Admin\JenisCutiController::class)->except(['show']);

    // Pengajuan Cuti
    Route::prefix('pengajuan')->name('pengajuan.')->group(function () {
        Route::get('/',                                    [Admin\PengajuanController::class, 'index'])->name('index');
        Route::get('/{pengajuan}',                         [Admin\PengajuanController::class, 'show'])->name('show');
        Route::post('/{pengajuan}/verifikasi',             [Admin\PengajuanController::class, 'verifikasi'])->name('verifikasi');
        Route::post('/{pengajuan}/upload-scan',            [Admin\PengajuanController::class, 'uploadScan'])->name('upload-scan');
        Route::get('/{pengajuan}/preview-pdf',             [Admin\PengajuanController::class, 'previewPdf'])->name('preview-pdf');
    });

    // Laporan
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/',               [Admin\PengajuanController::class, 'laporan'])->name('index');
        Route::get('/export-pdf',     [Admin\PengajuanController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/export-excel',   [Admin\PengajuanController::class, 'exportExcel'])->name('export-excel');
    });

    // Activity Log
    Route::get('/activity-log', [Admin\ActivityLogController::class, 'index'])->name('activity-log.index');
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