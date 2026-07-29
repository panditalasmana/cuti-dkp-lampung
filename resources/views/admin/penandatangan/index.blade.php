@extends('layouts.app')
@section('title', 'Master Pejabat Penandatangan')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-item active">Pejabat Penandatangan</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Pejabat Penandatangan & Paraf</h1>
        <p class="page-subtitle">Kelola nama, NIP, dan jabatan pejabat penandatangan permohonan & surat cuti</p>
    </div>
    <div>
        <a href="{{ route('admin.penandatangan.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i>Tambah Pejabat
        </a>
    </div>
</div>

<!-- Filter -->
<div class="card card-custom mb-4">
    <div class="card-body">
        <form method="GET">
            <div class="row g-3">
                <div class="col-12 col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, NIP, atau jabatan..." value="{{ request('search') }}">
                </div>
                <div class="col-12 col-md-4">
                    <select name="kategori" class="form-select">
                        <option value="">-- Semua Kategori --</option>
                        <option value="pejabat_wenang" {{ request('kategori') === 'pejabat_wenang' ? 'selected' : '' }}>Pejabat Berwenang</option>
                        <option value="atasan_langsung" {{ request('kategori') === 'atasan_langsung' ? 'selected' : '' }}>Atasan Langsung</option>
                        <option value="eselon_4" {{ request('kategori') === 'eselon_4' ? 'selected' : '' }}>Pejabat Pengawas</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Filter</button>
                    <a href="{{ route('admin.penandatangan.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Kategori</th>
                        <th>Nama Pejabat</th>
                        <th>NIP</th>
                        <th>Jabatan & Pangkat</th>
                        <th class="text-center" style="width: 120px;">Default</th>
                        <th class="text-center" style="width: 100px;">Status</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penandatangan as $item)
                        <tr>
                            <td>{{ $loop->iteration + ($penandatangan->currentPage() - 1) * $penandatangan->perPage() }}</td>
                            <td>
                                @if($item->kategori === 'pejabat_wenang')
                                    <span class="badge bg-primary">Pejabat Berwenang</span>
                                @elseif($item->kategori === 'atasan_langsung')
                                    <span class="badge bg-info text-dark">Atasan Langsung</span>
                                @else
                                    <span class="badge bg-secondary">Pejabat Pengawas</span>
                                @endif
                            </td>
                            <td class="fw-semibold text-dark">{{ $item->nama }}</td>
                            <td><code>{{ $item->nip }}</code></td>
                            <td>
                                <div class="fw-semibold small">{{ $item->jabatan }}</div>
                                @if($item->pangkat_golongan)
                                    <small class="text-muted">{{ $item->pangkat_golongan }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item->is_default)
                                    <span class="badge bg-success" title="Penandatangan utama kategori ini"><i class="bi bi-check-circle-fill me-1"></i>Default</span>
                                @else
                                    <form action="{{ route('admin.penandatangan.set-default', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary py-0" title="Jadikan Default">Set Default</button>
                                    </form>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('admin.penandatangan.edit', $item) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.penandatangan.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pejabat penandatangan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-pen fs-2 d-block mb-2"></i>
                                Belum ada data pejabat penandatangan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($penandatangan->hasPages())
        <div class="card-footer bg-transparent">
            {{ $penandatangan->links() }}
        </div>
    @endif
</div>
@endsection
