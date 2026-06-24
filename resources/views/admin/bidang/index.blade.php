@extends('layouts.app')
@section('title', 'Master Bidang')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-item active">Data Bidang</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Data Bidang</h1>
        <p class="page-subtitle">Kelola data bidang di DKP Provinsi Lampung</p>
    </div>
    <a href="{{ route('admin.bidang.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Tambah Bidang
    </a>
</div>

<!-- Search -->
<div class="card card-custom mb-4">
    <div class="card-body">
        <form method="GET">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau kode bidang..." value="{{ request('search') }}">
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary"><i class="bi bi-search me-1"></i>Cari</button>
                    <a href="{{ route('admin.bidang.index') }}" class="btn btn-outline-secondary ms-2">Reset</a>
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
                        <th>Nama Bidang</th>
                        <th>Kepala Bidang</th>
                        <th>Jml Pegawai</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bidang as $i => $item)
                        <tr>
                            <td class="text-muted">{{ $bidang->firstItem() + $i }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $item->kode_bidang }}</span></td>
                            <td>
                                <div class="fw-semibold">{{ $item->nama_bidang }}</div>
                                @if($item->keterangan)
                                    <small class="text-muted">{{ Str::limit($item->keterangan, 60) }}</small>
                                @endif
                            </td>
                            <td>
                                <div>{{ $item->kepala_bidang ?? '-' }}</div>
                                @if($item->nip_kepala_bidang)
                                    <small class="text-muted">{{ $item->nip_kepala_bidang }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info text-dark">{{ $item->pegawai_count }} pegawai</span>
                            </td>
                            <td>
                                <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.bidang.edit', $item) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.bidang.destroy', $item) }}">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" title="Hapus"
                                            onclick="konfirmasiHapus(this.form, '{{ $item->nama_bidang }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>Tidak ada data bidang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($bidang->hasPages())
        <div class="card-footer bg-transparent">{{ $bidang->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function konfirmasiHapus(form, nama) {
    Swal.fire({
        title: 'Hapus Bidang?',
        text: `Data bidang "${nama}" akan dihapus secara permanen.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#ef4444',
    }).then(r => { if (r.isConfirmed) form.submit(); });
}
</script>
@endpush