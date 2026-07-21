@extends('layouts.app')
@section('title', 'Daftar Pengajuan Cuti')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-item active">Pengajuan Cuti</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Pengajuan Cuti</h1>
        <p class="page-subtitle">Kelola dan verifikasi seluruh pengajuan cuti pegawai</p>
    </div>
</div>

<!-- Filter -->
<div class="card card-custom mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.pengajuan.index') }}">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama / NIP..." value="{{ $filters['search'] ?? '' }}">
                </div>
                <div class="col-6 col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="menunggu"  {{ ($filters['status'] ?? '') === 'menunggu'  ? 'selected' : '' }}>Menunggu</option>
                        <option value="disetujui" {{ ($filters['status'] ?? '') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak"   {{ ($filters['status'] ?? '') === 'ditolak'   ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="bidang_id" class="form-select">
                        <option value="">Semua Bidang</option>
                        @foreach($bidang as $b)
                            <option value="{{ $b->id }}" {{ ($filters['bidang_id'] ?? '') == $b->id ? 'selected' : '' }}>
                                {{ $b->nama_bidang }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="tahun" class="form-select">
                        <option value="">Semua Tahun</option>
                        @foreach($tahunList as $thn)
                            <option value="{{ $thn }}" {{ ($filters['tahun'] ?? '') == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card card-custom">
    <div class="card-header-custom">
        <div>
            <h5 class="card-title-custom">Daftar Pengajuan</h5>
            <p class="card-subtitle-custom">Total: {{ $pengajuan->total() }} pengajuan</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.laporan.export-pdf', request()->query()) }}" class="btn btn-sm btn-danger" target="_blank">
                <i class="bi bi-file-pdf me-1"></i>PDF
            </a>
            <a href="{{ route('admin.laporan.export-excel', request()->query()) }}" class="btn btn-sm btn-success">
                <i class="bi bi-file-excel me-1"></i>Excel
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-head">
                    <tr>
                        <th width="5%">No</th>
                        <th>Tanggal</th>
                        <th>Pegawai / Bidang</th>
                        <th>Jenis Cuti</th>
                        <th>Periode</th>
                        <th width="8%">Lama</th>
                        <th width="12%">Status</th>
                        <th width="12%">Scan Surat</th>
                        <th width="8%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuan as $index => $item)
                        <tr>
                            <td class="text-muted">{{ $pengajuan->firstItem() + $index }}</td>
                            <td>
                                <div>{{ $item->tanggal_pengajuan->isoFormat('D MMM Y') }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $item->pegawai->nama_lengkap }}</div>
                                <small class="text-muted">{{ $item->pegawai->bidang->nama_bidang ?? '-' }}</small>
                            </td>
                            <td>{{ $item->jenisCuti->nama_cuti }}</td>
                            <td>
                                <div class="small">{{ $item->tanggal_mulai->isoFormat('D MMM Y') }}</div>
                                <div class="small text-muted">s.d. {{ $item->tanggal_selesai->isoFormat('D MMM Y') }}</div>
                            </td>
                            <td class="text-center fw-semibold">{{ $item->lama_cuti_display }}</td>
                            <td>
                                @include('components.status-badge', ['status' => $item->status])
                            </td>
                            <td>
                                @if($item->scanSurat)
                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="previewDokumenIndex('{{ $item->scanSurat->file_url }}', '{{ $item->scanSurat->nama_file }}', '{{ $item->scanSurat->mime_type }}')" title="Lihat Scan">
                                        <i class="bi bi-file-earmark-check me-1"></i>Lihat
                                    </button>
                                @else
                                    <span class="badge bg-light text-muted border">Belum ada</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.pengajuan.show', $item) }}" class="btn btn-sm btn-primary" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                Tidak ada data pengajuan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($pengajuan->hasPages())
        <div class="card-footer bg-transparent">
            {{ $pengajuan->links() }}
        </div>
    @endif
</div>

<!-- Modal Preview Dokumen Website -->
<div class="modal fade" id="modalPreviewDokumenIndex" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fs-6 fw-semibold" id="previewModalTitleIndex"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Pratinjau Scan Surat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3 bg-dark-subtle" style="min-height: 450px; display: flex; align-items: center; justify-content: center;">
                <img id="previewImageIndex" src="" class="img-fluid rounded shadow d-none" style="max-height: 75vh;" alt="preview scan">
                <iframe id="previewIframeIndex" src="" class="w-100 rounded border-0 d-none" style="height: 75vh;"></iframe>
            </div>
            <div class="modal-footer bg-light">
                <a id="previewDownloadBtnIndex" href="" class="btn btn-primary" download target="_blank">
                    <i class="bi bi-download me-1"></i>Unduh File
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function previewDokumenIndex(url, filename, mimeType) {
    const modal = new bootstrap.Modal(document.getElementById('modalPreviewDokumenIndex'));
    document.getElementById('previewModalTitleIndex').innerText = filename;
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