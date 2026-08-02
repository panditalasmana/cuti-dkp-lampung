@extends('layouts.app')
@section('title', 'Panduan Penggunaan — SIPENCUTI')

@section('breadcrumb')
    <a href="{{ route('pegawai.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-item active">Panduan Penggunaan</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Panduan Penggunaan Sistem</h1>
        <p class="page-subtitle">Petunjuk alur pengajuan cuti digital untuk pegawai Dinas Kelautan dan Perikanan Provinsi Lampung</p>
    </div>
    <a href="{{ route('pegawai.pengajuan.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Ajukan Cuti
    </a>
</div>

<!-- 5 Langkah Pengajuan Cuti -->
<div class="card card-custom mb-4">
    <div class="card-header-custom">
        <h5 class="card-title-custom">
            <i class="bi bi-diagram-3 me-2"></i>Alur 5 Langkah Pengajuan Cuti
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12 col-md">
                <div class="p-3 border rounded-3 bg-light h-100">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary rounded-circle p-2">1</span>
                        <h6 class="fw-bold mb-0 text-primary">Cek Kuota</h6>
                    </div>
                    <p class="small text-muted mb-0">Lihat sisa kuota cuti tahunan pada kartu profil di Dashboard.</p>
                </div>
            </div>
            <div class="col-12 col-md">
                <div class="p-3 border rounded-3 bg-light h-100">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary rounded-circle p-2">2</span>
                        <h6 class="fw-bold mb-0 text-primary">Isi Formulir</h6>
                    </div>
                    <p class="small text-muted mb-0">Menu <strong>Ajukan Cuti</strong> → tentukan tanggal, jenis cuti & atasan.</p>
                </div>
            </div>
            <div class="col-12 col-md">
                <div class="p-3 border rounded-3 bg-light h-100">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary rounded-circle p-2">3</span>
                        <h6 class="fw-bold mb-0 text-primary">Cetak PDF</h6>
                    </div>
                    <p class="small text-muted mb-0">Klik <strong>Cetak Surat PDF</strong> di halaman detail pengajuan Anda.</p>
                </div>
            </div>
            <div class="col-12 col-md">
                <div class="p-3 border rounded-3 bg-light h-100">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary rounded-circle p-2">4</span>
                        <h6 class="fw-bold mb-0 text-primary">Tanda Tangan</h6>
                    </div>
                    <p class="small text-muted mb-0">Minta tanda tangan Atasan Langsung & serahkan fisik ke Admin DKP.</p>
                </div>
            </div>
            <div class="col-12 col-md">
                <div class="p-3 border rounded-3 bg-light h-100">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-success rounded-circle p-2">5</span>
                        <h6 class="fw-bold mb-0 text-success">Verifikasi</h6>
                    </div>
                    <p class="small text-muted mb-0">Admin menyetujui & mengunggah scan surat resmi ber-stempel di menu Riwayat.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Informasi & FAQ -->
<div class="row g-4">
    <!-- Tabel Jenis Cuti -->
    <div class="col-12 col-lg-7">
        <div class="card card-custom h-100">
            <div class="card-header-custom">
                <h5 class="card-title-custom">
                    <i class="bi bi-info-circle me-2"></i>Ketentuan Jenis Cuti (PP No. 11 Th. 2017)
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Jenis Cuti</th>
                                <th>Maksimal Hari</th>
                                <th>Sifat Kuota</th>
                                <th>Lampiran</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Cuti Tahunan (CT)</strong></td>
                                <td>12 hari kerja</td>
                                <td><span class="badge bg-secondary">Potong Kuota</span></td>
                                <td><span class="text-muted small">Tidak wajib</span></td>
                            </tr>
                            <tr>
                                <td><strong>Cuti Sakit (CS)</strong></td>
                                <td>Sesuai rekomendasi</td>
                                <td><span class="badge bg-success">Kuota Terpisah</span></td>
                                <td><span class="badge bg-danger"><i class="bi bi-paperclip"></i> Surat Dokter</span></td>
                            </tr>
                            <tr>
                                <td><strong>Cuti Melahirkan (CM)</strong></td>
                                <td>3 bulan</td>
                                <td><span class="badge bg-success">Kuota Terpisah</span></td>
                                <td><span class="badge bg-danger"><i class="bi bi-paperclip"></i> Surat Dokter/Bidan</span></td>
                            </tr>
                            <tr>
                                <td><strong>Cuti Alasan Penting (CAK)</strong></td>
                                <td>1 bulan</td>
                                <td><span class="badge bg-success">Kuota Terpisah</span></td>
                                <td><span class="badge bg-danger"><i class="bi bi-paperclip"></i> Bukti Alasan</span></td>
                            </tr>
                            <tr>
                                <td><strong>Cuti Besar (Haji/Umroh)</strong></td>
                                <td>30–90 hari</td>
                                <td><span class="badge bg-success">Kuota Terpisah</span></td>
                                <td><span class="badge bg-danger"><i class="bi bi-paperclip"></i> Surat Travel/Kemenag</span></td>
                            </tr>
                            <tr>
                                <td><strong>Cuti di Luar Tanggungan (CLN)</strong></td>
                                <td>3 tahun</td>
                                <td><span class="badge bg-success">Kuota Terpisah</span></td>
                                <td><span class="badge bg-danger"><i class="bi bi-paperclip"></i> Dokumen Pendukung</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Accordion -->
    <div class="col-12 col-lg-5">
        <div class="card card-custom h-100">
            <div class="card-header-custom">
                <h5 class="card-title-custom">
                    <i class="bi bi-question-circle me-2"></i>Pertanyaan Umum (FAQ)
                </h5>
            </div>
            <div class="card-body">
                <div class="accordion accordion-flush" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                Bagaimana memantau status pengajuan?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted small">
                                Status dapat dilihat di menu <strong>Riwayat Pengajuan</strong>. Jika disetujui, Admin akan mengunggah file scan PDF surat resmi yang dapat dipratinjau.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Apakah hari libur memotong kuota?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted small">
                                <strong>Tidak.</strong> Hari Sabtu, Minggu, dan Hari Libur Nasional secara otomatis tidak dihitung dalam jumlah hari cuti.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Bagaimana jika salah isi pengajuan?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted small">
                                Selama status masih <strong>Menunggu</strong>, Anda dapat menekan tombol <strong>Batalkan</strong> di halaman detail pengajuan, lalu buat pengajuan baru.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Bantuan teknis & lupa password?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted small">
                                Hubungi <strong>Sub Bagian Umum & Kepegawaian DKP Lampung</strong> untuk reset password atau perubahan data profil.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
