@extends('layouts.app')
@section('title', 'Riwayat Pengajuan')

@section('breadcrumb')
    <a href="{{ route('pegawai.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-item active">Riwayat Pengajuan</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Riwayat Pengajuan Cuti</h1>
        <p class="page-subtitle">Seluruh riwayat pengajuan cuti Anda</p>
    </div>
    <a href="{{ route('pegawai.pengajuan.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Ajukan Cuti
    </a>
</div>

<!-- Filter -->
<div class="card card-custom mb-4">
    <div class="card-body">
        <form method="GET">
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="menunggu"  {{ ($filters['status'] ?? '') === 'menunggu'  ? 'selected' : '' }}>Menunggu</option>
                        <option value="disetujui" {{ ($filters['status'] ?? '') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak"   {{ ($filters['status'] ?? '') === 'ditolak'   ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <select name="tahun" class="form-select">
                        <option value="">Semua Tahun</option>
                        @foreach($tahunList as $thn)
                            <option value="{{ $thn }}" {{ ($filters['tahun'] ?? '') == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto d-flex gap-2">
                    <button class="btn btn-primary"><i class="bi bi-search"></i></button>
                    <a href="{{ route('pegawai.riwayat.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Sisa Cuti Info -->
<div class="alert alert-info d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-calendar-check-fill fs-5"></i>
    <div>
        Sisa cuti tahunan Anda: <strong class="fs-5">{{ $pegawai->sisa_cuti_tahunan }} hari</strong>
    </div>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-head">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Jenis Cuti</th>
                        <th>Periode</th>
                        <th>Lama</th>
                        <th>Status</th>
                        <th>Scan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $i => $item)
                        <tr>
                            <td class="text-muted">{{ $riwayat->firstItem() + $i }}</td>
                            <td>
                                <div>{{ $item->tanggal_pengajuan->isoFormat('D MMM Y') }}</div>
                            </td>
                            <td class="small">{{ $item->jenisCuti->nama_cuti }}</td>
                            <td>
                                <div class="small">{{ $item->tanggal_mulai->isoFormat('D MMM Y') }}</div>
                                <div class="small text-muted">s.d. {{ $item->tanggal_selesai->isoFormat('D MMM Y') }}</div>
                            </td>
                            <td class="fw-semibold text-center">{{ $item->lama_cuti_display }}</td>
                            <td>@include('components.status-badge', ['status' => $item->status])</td>
                            <td>
                                @if($item->scanSurat)
                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="previewDokumenIndex('{{ $item->scanSurat->file_url }}', '{{ $item->scanSurat->nama_file }}', '{{ $item->scanSurat->mime_type }}')" title="Lihat Scan Surat Resmi">
                                        <i class="bi bi-file-earmark-check me-1"></i>Lihat
                                    </button>
                                @else
                                    <span class="badge bg-light text-muted border">Belum ada</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('pegawai.pengajuan.show', $item) }}" class="btn btn-sm btn-primary" title="Detail Pengajuan">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($item->status !== \App\Models\PengajuanCuti::STATUS_DISETUJUI)
                                        <a href="{{ route('pegawai.pengajuan.cetak', $item) }}" class="btn btn-sm btn-outline-danger" title="Cetak PDF Formulir">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                Belum ada riwayat pengajuan cuti.
                                <br><a href="{{ route('pegawai.pengajuan.create') }}" class="btn btn-sm btn-primary mt-2">Ajukan Sekarang</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($riwayat->hasPages())
        <div class="card-footer bg-transparent">{{ $riwayat->links() }}</div>
    @endif
</div>

<!-- Modal Preview Dokumen Website Pegawai -->
<div class="modal fade" id="modalPreviewDokumenIndex" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fs-6 fw-semibold" id="previewModalTitleIndex"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Pratinjau Scan Surat Resmi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3 bg-dark-subtle" style="min-height: 450px; display: flex; align-items: center; justify-content: center;">
                <img id="previewImageIndex" src="" class="img-fluid rounded shadow d-none" style="max-height: 75vh;" alt="preview scan">
                <iframe id="previewIframeIndex" src="" class="w-100 rounded border-0 d-none" style="height: 75vh;"></iframe>
            </div>
            <div class="modal-footer bg-light">
                <a id="previewDownloadBtnIndex" href="" class="btn btn-primary" download target="_blank">
                    <i class="bi bi-download me-1"></i>Unduh Berkas Scan
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function previewDokumenIndex(url, filename, mimeType) {
    const modal = new bootstrap.Modal(document.getElementById('modalPreviewDokumenIndex'));
    document.getElementById('previewModalTitleIndex').innerText = 'Scan Surat: ' + filename;
    document.getElementById('previewDownloadBtnIndex').href = url;
    
    const imgEl = document.getElementById('previewImageIndex');
    const iframeEl = document.getElementById('previewIframeIndex');
    
    if (mimeType.includes('image') || url.match(/\.(jpg|jpeg|png|webp|gif)$/i)) {
        imgEl.src = url;
        imgEl.classList.remove('d-none');
        iframeEl.classList.add('d-none');
        iframeEl.src = '';
    } else {
        iframeEl.src = url;
        iframeEl.classList.remove('d-none');
        imgEl.classList.add('d-none');
        imgEl.src = '';
    }
    
    modal.show();
}
</script>
@endsection