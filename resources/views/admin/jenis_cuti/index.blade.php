@extends('layouts.app')
@section('title', 'Master Jenis Cuti')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-item active">Jenis Cuti</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Jenis Cuti</h1>
        <p class="page-subtitle">Kelola jenis-jenis cuti ASN sesuai regulasi</p>
    </div>
    <a href="{{ route('admin.jenis-cuti.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Tambah Jenis Cuti
    </a>
</div>

<!-- Search -->
<div class="card card-custom mb-4">
    <div class="card-body">
        <form method="GET">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <input type="text" name="search" class="form-control"
                           placeholder="Cari nama jenis cuti..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary"><i class="bi bi-search me-1"></i>Cari</button>
                    <a href="{{ route('admin.jenis-cuti.index') }}" class="btn btn-outline-secondary ms-2">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-head">
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Cuti</th>
                        <th>Potong Kuota</th>
                        <th>Perlu Lampiran</th>
                        <th>Digunakan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jenisCuti as $i => $item)
                        <tr>
                            <td class="text-muted">{{ $jenisCuti->firstItem() + $i }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $item->kode_cuti }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $item->nama_cuti }}</div>
                                @if($item->dasar_hukum)
                                    <small class="text-muted">{{ Str::limit($item->dasar_hukum, 50) }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item->potong_kuota)
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-check"></i> Potong Cuti Tahunan
                                    </span>
                                @else
                                    <span class="badge bg-success text-white">Kuota Terpisah</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item->perlu_lampiran)
                                    <span class="badge bg-danger">
                                        <i class="bi bi-paperclip"></i> Wajib
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted">Tidak</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $item->pengajuan_cuti_count }} kali</span>
                            </td>
                            <td>
                                <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.jenis-cuti.edit', $item) }}"
                                       class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.jenis-cuti.destroy', $item) }}">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" title="Hapus"
                                            onclick="konfirmasiHapus(this.form, '{{ $item->nama_cuti }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                Tidak ada data jenis cuti.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($jenisCuti->hasPages())
        <div class="card-footer bg-transparent">{{ $jenisCuti->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function konfirmasiHapus(form, nama) {
    Swal.fire({
        title: 'Hapus Jenis Cuti?',
        text: `"${nama}" akan dihapus. Data yang sudah digunakan tidak bisa dihapus.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#ef4444',
    }).then(r => { if (r.isConfirmed) form.submit(); });
}
</script>
@endpush