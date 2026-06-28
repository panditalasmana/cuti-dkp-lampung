@extends('layouts.app')
@section('title', 'Detail Pengajuan — ' . $pengajuan->tanggal_pengajuan->format('d/m/Y'))

@section('breadcrumb')
    <a href="{{ route('pegawai.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <a href="{{ route('pegawai.riwayat.index') }}" class="breadcrumb-item">Riwayat Pengajuan</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-item active">Detail</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Detail Pengajuan</h1>
        <p class="page-subtitle">Tanggal Pengajuan: {{ $pengajuan->tanggal_pengajuan->isoFormat('D MMMM Y') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('pegawai.pengajuan.preview', $pengajuan) }}" class="btn btn-outline-danger" target="_blank">
            <i class="bi bi-file-pdf me-1"></i>Preview PDF
        </a>
        <a href="{{ route('pegawai.pengajuan.cetak', $pengajuan) }}" class="btn btn-outline-primary">
            <i class="bi bi-download me-1"></i>Unduh/Cetak PDF
        </a>
        <a href="{{ route('pegawai.riwayat.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Detail Info -->
    <div class="col-12 col-xl-8">
        <div class="card card-custom mb-4">
            <div class="card-header-custom">
                <h5 class="card-title-custom">Informasi Pengajuan</h5>
                @include('components.status-badge', ['status' => $pengajuan->status])
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="detail-label">Tanggal Pengajuan</label>
                        <div class="detail-value">{{ $pengajuan->tanggal_pengajuan->isoFormat('D MMMM Y') }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Jenis Cuti</label>
                        <div class="detail-value fw-semibold">{{ $pengajuan->jenisCuti->nama_cuti }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Tanggal Mulai</label>
                        <div class="detail-value">{{ $pengajuan->tanggal_mulai->isoFormat('dddd, D MMMM Y') }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Tanggal Selesai</label>
                        <div class="detail-value">{{ $pengajuan->tanggal_selesai->isoFormat('dddd, D MMMM Y') }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Lama Cuti</label>
                        <div class="detail-value">
                            <span class="badge bg-primary fs-6">{{ $pengajuan->lama_cuti }} Hari Kerja</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Tanggal Pengajuan</label>
                        <div class="detail-value">{{ $pengajuan->tanggal_pengajuan->isoFormat('D MMMM Y, HH:mm') }}</div>
                    </div>
                    <div class="col-12">
                        <label class="detail-label">Alasan Cuti</label>
                        <div class="detail-value">{{ $pengajuan->alasan_cuti }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Alamat Selama Cuti</label>
                        <div class="detail-value">{{ $pengajuan->alamat_selama_cuti }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">No. Telepon Selama Cuti</label>
                        <div class="detail-value">{{ $pengajuan->no_telp_selama_cuti ?? '-' }}</div>
                    </div>
                </div>

                @if($pengajuan->catatan_admin)
                    <div class="alert alert-info mt-3">
                        <strong><i class="bi bi-info-circle me-1"></i>Catatan Admin:</strong><br>
                        {{ $pengajuan->catatan_admin }}
                    </div>
                @endif

                @if($pengajuan->tanggal_verifikasi)
                    <div class="mt-3 p-3 bg-light rounded">
                        <small class="text-muted">
                            Diverifikasi oleh <strong>{{ $pengajuan->verifikator->name ?? 'Admin' }}</strong>
                            pada {{ $pengajuan->tanggal_verifikasi->isoFormat('D MMMM Y, HH:mm') }}
                        </small>
                    </div>
                @endif
            </div>
        </div>

        <!-- Scan Dokumen -->
        @if($pengajuan->scanSurat)
            <div class="card card-custom">
                <div class="card-header-custom">
                    <h5 class="card-title-custom">Scan Surat Ditandatangani</h5>
                    <span class="badge bg-success">Sudah Diupload</span>
                </div>
                <div class="card-body">
                    <div class="dokumen-item d-flex align-items-center gap-3 p-3 bg-light rounded">
                        <div class="dokumen-icon">
                            @if($pengajuan->scanSurat->isImage())
                                <i class="bi bi-image-fill text-info fs-2"></i>
                            @else
                                <i class="bi bi-file-pdf-fill text-danger fs-2"></i>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $pengajuan->scanSurat->nama_file }}</div>
                            <small class="text-muted">{{ $pengajuan->scanSurat->ukuran_format }} — {{ $pengajuan->scanSurat->created_at->isoFormat('D MMM Y, HH:mm') }}</small>
                        </div>
                        <a href="{{ $pengajuan->scanSurat->file_url }}" class="btn btn-sm btn-primary" target="_blank" download>
                            <i class="bi bi-download"></i> Unduh
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="col-12 col-xl-4">
        <!-- Info Dasar Hukum -->
        <div class="card card-custom">
            <div class="card-header-custom">
                <h5 class="card-title-custom"><i class="bi bi-book me-1"></i>Dasar Hukum</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-1"><strong>{{ $pengajuan->jenisCuti->nama_cuti }}</strong></p>
                <p class="text-muted small">{{ $pengajuan->jenisCuti->dasar_hukum ?? 'Tidak ada keterangan dasar hukum.' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
