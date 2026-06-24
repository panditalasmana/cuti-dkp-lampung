@extends('layouts.app')
@section('title', 'Data Pegawai')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-item active">Data Pegawai</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Data Pegawai</h1>
        <p class="page-subtitle">Kelola data seluruh pegawai DKP Provinsi Lampung</p>
    </div>
    <a href="{{ route('admin.pegawai.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i>Tambah Pegawai
    </a>
</div>

<!-- Filter -->
<div class="card card-custom mb-4">
    <div class="card-body">
        <form method="GET">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau NIP..." value="{{ request('search') }}">
                </div>
                <div class="col-6 col-md-3">
                    <select name="bidang_id" class="form-select">
                        <option value="">Semua Bidang</option>
                        @foreach($bidang as $b)
                            <option value="{{ $b->id }}" {{ request('bidang_id') == $b->id ? 'selected' : '' }}>{{ $b->nama_bidang }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <select name="jenis_pegawai" class="form-select">
                        <option value="">Semua Jenis</option>
                        <option value="PNS"    {{ request('jenis_pegawai') === 'PNS'    ? 'selected' : '' }}>PNS</option>
                        <option value="PPPK"   {{ request('jenis_pegawai') === 'PPPK'   ? 'selected' : '' }}>PPPK</option>
                        <option value="Honorer"{{ request('jenis_pegawai') === 'Honorer' ? 'selected' : '' }}>Honorer</option>
                    </select>
                </div>
                <div class="col-auto d-flex gap-2">
                    <button class="btn btn-primary"><i class="bi bi-search"></i></button>
                    <a href="{{ route('admin.pegawai.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card card-custom">
    <div class="card-header-custom">
        <div>
            <h5 class="card-title-custom">Daftar Pegawai</h5>
            <p class="card-subtitle-custom">Total: {{ $pegawai->total() }} pegawai</p>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-head">
                    <tr>
                        <th>No</th>
                        <th>Pegawai</th>
                        <th>NIP</th>
                        <th>Bidang / Jabatan</th>
                        <th>Jenis</th>
                        <th>Sisa Cuti</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pegawai as $i => $p)
                        <tr>
                            <td class="text-muted">{{ $pegawai->firstItem() + $i }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-sm">
                                        @if($p->foto)
                                            <img src="{{ asset('storage/'.$p->foto) }}" alt="" class="avatar-img">
                                        @else
                                            <span class="avatar-initial">{{ substr($p->nama_lengkap, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $p->nama_lengkap }}</div>
                                        <small class="text-muted">{{ $p->jenis_kelamin_label }}</small>
                                    </div>
                                </div>
                            </td>
                            <td><code class="small">{{ $p->nip }}</code></td>
                            <td>
                                <div class="small fw-semibold">{{ $p->bidang->nama_bidang ?? '-' }}</div>
                                <div class="small text-muted">{{ $p->jabatan->nama_jabatan ?? '-' }}</div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $p->jenis_pegawai }}</span></td>
                            <td>
                                <span class="badge {{ $p->sisa_cuti_tahunan > 6 ? 'bg-success' : ($p->sisa_cuti_tahunan > 0 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                    {{ $p->sisa_cuti_tahunan }} hr
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $p->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.pegawai.show', $p) }}" class="btn btn-sm btn-info text-white" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.pegawai.edit', $p) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.pegawai.destroy', $p) }}">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" title="Hapus"
                                            onclick="konfirmasiHapus(this.form, '{{ $p->nama_lengkap }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-people fs-2 d-block mb-2"></i>Tidak ada data pegawai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($pegawai->hasPages())
        <div class="card-footer bg-transparent">{{ $pegawai->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function konfirmasiHapus(form, nama) {
    Swal.fire({
        title: 'Hapus Pegawai?',
        text: `Data pegawai "${nama}" beserta akun login akan dihapus.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#ef4444',
    }).then(r => { if (r.isConfirmed) form.submit(); });
}
</script>
@endpush