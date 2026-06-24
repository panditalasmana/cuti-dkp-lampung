@extends('layouts.app')
@section('title', 'Detail Pengajuan — ' . $pengajuan->nomor_surat)

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <a href="{{ route('admin.pengajuan.index') }}" class="breadcrumb-item">Pengajuan Cuti</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-item active">Detail</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Detail Pengajuan</h1>
        <p class="page-subtitle">Nomor: {{ $pengajuan->nomor_surat }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.pengajuan.preview-pdf', $pengajuan) }}" class="btn btn-outline-danger" target="_blank">
            <i class="bi bi-file-pdf me-1"></i>Preview PDF
        </a>
        <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-outline-secondary">
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
                        <label class="detail-label">Nomor Surat</label>
                        <div class="detail-value"><code>{{ $pengajuan->nomor_surat }}</code></div>
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
                            Diverifikasi oleh <strong>{{ $pengajuan->verifikator->name ?? '-' }}</strong>
                            pada {{ $pengajuan->tanggal_verifikasi->isoFormat('D MMMM Y, HH:mm') }}
                        </small>
                    </div>
                @endif
            </div>
        </div>

        <!-- Data Pegawai -->
        <div class="card card-custom mb-4">
            <div class="card-header-custom">
                <h5 class="card-title-custom">Data Pegawai</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="detail-label">Nama Lengkap</label>
                        <div class="detail-value fw-semibold">{{ $pengajuan->pegawai->nama_lengkap }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">NIP</label>
                        <div class="detail-value"><code>{{ $pengajuan->pegawai->nip }}</code></div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Jabatan</label>
                        <div class="detail-value">{{ $pengajuan->pegawai->jabatan->nama_jabatan ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Bidang</label>
                        <div class="detail-value">{{ $pengajuan->pegawai->bidang->nama_bidang ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Pangkat/Golongan</label>
                        <div class="detail-value">{{ $pengajuan->pegawai->pangkat ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Sisa Cuti Tahunan</label>
                        <div class="detail-value">
                            <span class="badge {{ $pengajuan->pegawai->sisa_cuti_tahunan > 0 ? 'bg-success' : 'bg-danger' }} fs-6">
                                {{ $pengajuan->pegawai->sisa_cuti_tahunan }} hari
                            </span>
                        </div>
                    </div>
                </div>
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

    <!-- Actions Panel -->
    <div class="col-12 col-xl-4">

        {{-- Verifikasi (hanya jika masih menunggu) --}}
        @if($pengajuan->status === 'menunggu')
            <div class="card card-custom mb-4 border-warning">
                <div class="card-header-custom" style="background: linear-gradient(135deg, #fef3c7, #fde68a);">
                    <h5 class="card-title-custom text-warning-emphasis">
                        <i class="bi bi-shield-check me-1"></i>Verifikasi Pengajuan
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Pilih tindakan untuk pengajuan ini setelah memeriksa formulir fisik yang telah ditandatangani Kepala Bidang.</p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan Admin (opsional)</label>
                        <textarea class="form-control" id="catatanAdmin" rows="3" placeholder="Tuliskan catatan jika ada..."></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-success" onclick="konfirmasiVerifikasi('disetujui')">
                            <i class="bi bi-check-circle me-1"></i>Setujui Pengajuan
                        </button>
                        <button class="btn btn-danger" onclick="konfirmasiVerifikasi('ditolak')">
                            <i class="bi bi-x-circle me-1"></i>Tolak Pengajuan
                        </button>
                    </div>

                    <form id="formVerifikasi" method="POST" action="{{ route('admin.pengajuan.verifikasi', $pengajuan) }}">
                        @csrf
                        <input type="hidden" name="status" id="inputStatus">
                        <input type="hidden" name="catatan_admin" id="inputCatatan">
                    </form>
                </div>
            </div>

            {{-- Upload Scan --}}
            <div class="card card-custom mb-4">
                <div class="card-header-custom">
                    <h5 class="card-title-custom">
                        <i class="bi bi-cloud-upload me-1"></i>Upload Scan Surat
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Upload scan/foto formulir cuti yang sudah ditandatangani Kepala Bidang. Status akan otomatis berubah menjadi <strong>Disetujui</strong>.</p>
                    <form method="POST" action="{{ route('admin.pengajuan.upload-scan', $pengajuan) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">File Scan <span class="text-danger">*</span></label>
                            <input type="file" name="file_scan" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                            <div class="form-text">Format: PDF, JPG, PNG. Maks. 5MB.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <input type="text" name="keterangan" class="form-control" placeholder="Keterangan tambahan...">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-upload me-1"></i>Upload & Setujui
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <!-- Info Dasar Hukum -->
        <div class="card card-custom">
            <div class="card-header-custom">
                <h5 class="card-title-custom"><i class="bi bi-book me-1"></i>Dasar Hukum</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-1"><strong>{{ $pengajuan->jenisCuti->nama_cuti }}</strong></p>
                <p class="text-muted small">{{ $pengajuan->jenisCuti->dasar_hukum ?? 'Tidak ada keterangan dasar hukum.' }}</p>
                @if($pengajuan->jenisCuti->maks_hari)
                    <div class="alert alert-info p-2 small">
                        <i class="bi bi-info-circle me-1"></i>Maks. {{ $pengajuan->jenisCuti->maks_hari }} hari kerja
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function konfirmasiVerifikasi(status) {
    const catatan = document.getElementById('catatanAdmin').value;
    const label   = status === 'disetujui' ? 'menyetujui' : 'menolak';
    const color   = status === 'disetujui' ? 'success' : 'error';

    Swal.fire({
        title: `Konfirmasi ${status === 'disetujui' ? 'Persetujuan' : 'Penolakan'}`,
        text: `Anda yakin ingin ${label} pengajuan ini?`,
        icon: color,
        showCancelButton: true,
        confirmButtonText: 'Ya, Lanjutkan',
        cancelButtonText: 'Batal',
        confirmButtonColor: status === 'disetujui' ? '#10b981' : '#ef4444',
    }).then(result => {
        if (result.isConfirmed) {
            document.getElementById('inputStatus').value  = status;
            document.getElementById('inputCatatan').value = catatan;
            document.getElementById('formVerifikasi').submit();
        }
    });
}
</script>
@endpush