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
                                    <a href="{{ $item->scanSurat->file_url }}" class="btn btn-sm btn-success" download title="Unduh Scan">
                                        <i class="bi bi-download"></i>
                                    </a>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('pegawai.pengajuan.show', $item) }}" class="btn btn-sm btn-primary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('pegawai.pengajuan.cetak', $item) }}" class="btn btn-sm btn-outline-danger" title="Cetak PDF">
                                        <i class="bi bi-printer"></i>
                                    </a>
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
@endsection