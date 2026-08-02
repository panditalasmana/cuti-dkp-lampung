@extends('layouts.app')
@section('title', 'Panduan Sistem — SIPENCUTI Admin')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-item active">Panduan Sistem</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Panduan Sistem Administrator</h1>
        <p class="page-subtitle">Petunjuk operasional pengelolaan data master, verifikasi pengajuan cuti, dan penerbitan laporan</p>
    </div>
    <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-primary">
        <i class="bi bi-check2-square me-1"></i>Verifikasi Cuti
    </a>
</div>

<!-- 3 Pilar Tugas Admin -->
<div class="row g-4 mb-4">
    <div class="col-12 col-md-4">
        <div class="card card-custom h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="p-2 bg-primary bg-opacity-10 text-primary rounded">
                        <i class="bi bi-check2-circle fs-4"></i>
                    </div>
                    <h6 class="fw-bold mb-0 text-dark">1. Verifikasi Cuti</h6>
                </div>
                <p class="small text-muted mb-0">Cek berkas fisik, setujui/tolak pengajuan di sistem, lalu unggah scan PDF surat resmi yang sudah di-stempel.</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card card-custom h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="p-2 bg-success bg-opacity-10 text-success rounded">
                        <i class="bi bi-pen fs-4"></i>
                    </div>
                    <h6 class="fw-bold mb-0 text-dark">2. Pejabat Penandatangan</h6>
                </div>
                <p class="small text-muted mb-0">Kelola data Kadin, Sekda, BKD, Gubernur, Kabid & Pejabat Pengawas aktif serta pejabat default secara mandiri.</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card card-custom h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="p-2 bg-info bg-opacity-10 text-info rounded">
                        <i class="bi bi-file-earmark-spreadsheet fs-4"></i>
                    </div>
                    <h6 class="fw-bold mb-0 text-dark">3. Import & Laporan</h6>
                </div>
                <p class="small text-muted mb-0">Impor data pegawai massal via CSV, ekspor akun login, dan cetak Laporan Rekapitulasi Cuti (PDF & Excel).</p>
            </div>
        </div>
    </div>
</div>

<!-- Petunjuk Operasional Accordion -->
<div class="card card-custom">
    <div class="card-header-custom">
        <h5 class="card-title-custom">
            <i class="bi bi-list-check me-2"></i>Petunjuk Langkah Demi Langkah Admin
        </h5>
    </div>
    <div class="card-body">
        <div class="accordion accordion-flush" id="adminGuideAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#guide1">
                        Cara Mengatur Pejabat Penandatangan & Default
                    </button>
                </h2>
                <div id="guide1" class="accordion-collapse collapse" data-bs-parent="#adminGuideAccordion">
                    <div class="accordion-body text-muted small">
                        <ol class="mb-0 ps-3">
                            <li>Buka menu <strong>Pejabat Penandatangan</strong> di sidebar master data.</li>
                            <li>Klik <strong>Tambah Pejabat</strong> atau tombol <strong>Edit</strong> untuk mengubah data pejabat.</li>
                            <li>Isi Nama Lengkap & Gelar, NIP, Jabatan, dan Pangkat/Golongan.</li>
                            <li>Klik <strong>Set Default</strong> jika ingin menjadikan pejabat tersebut sebagai pilihan utama otomatis pada form pengajuan pegawai.</li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#guide2">
                        Cara Memverifikasi Cuti & Upload Scan Surat Resmi
                    </button>
                </h2>
                <div id="guide2" class="accordion-collapse collapse" data-bs-parent="#adminGuideAccordion">
                    <div class="accordion-body text-muted small">
                        <ol class="mb-0 ps-3">
                            <li>Buka menu <strong>Pengajuan Cuti</strong> dan pilih permohonan berstatus <span class="badge bg-warning text-dark">Menunggu</span>.</li>
                            <li>Cek kesesuaian berkas fisik yang diserahkan pegawai.</li>
                            <li>Klik <strong>Setujui Pengajuan</strong> atau <strong>Tolak Pengajuan</strong> (masukkan alasan jika ditolak).</li>
                            <li>Setelah disetujui, klik <strong>Upload Scan Surat</strong> untuk melampirkan berkas PDF scan ber-stempel resmi.</li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#guide3">
                        Cara Import Data Pegawai Massal via CSV
                    </button>
                </h2>
                <div id="guide3" class="accordion-collapse collapse" data-bs-parent="#adminGuideAccordion">
                    <div class="accordion-body text-muted small">
                        <ol class="mb-0 ps-3">
                            <li>Buka menu <strong>Data Pegawai</strong> → klik tombol <strong>Import CSV</strong>.</li>
                            <li>Unduh template file CSV resmi agar format kolom sesuai.</li>
                            <li>Pilih file CSV yang sudah diisi, lalu klik **Proses Import**. Akun login pegawai akan otomatis dibuat oleh sistem.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
