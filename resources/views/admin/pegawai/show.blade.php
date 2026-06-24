@extends('layouts.app')
@section('title', 'Detail Pegawai — ' . $pegawai->nama_lengkap)

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <a href="{{ route('admin.pegawai.index') }}" class="breadcrumb-item">Data Pegawai</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-item active">Detail</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Detail Pegawai</h1>
        <p class="page-subtitle">{{ $pegawai->nama_lengkap }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.pegawai.edit', $pegawai) }}" class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        <a href="{{ route('admin.pegawai.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Foto & Info Singkat -->
    <div class="col-12 col-xl-4">
        <div class="card card-custom text-center mb-4">
            <div class="card-body py-4">
                <div class="avatar-xl mx-auto mb-3">
                    @if($pegawai->foto)
                        <img src="{{ asset('storage/'.$pegawai->foto) }}"
                             class="avatar-img-xl" alt="foto">
                    @else
                        <span class="avatar-initial-xl">
                            {{ substr($pegawai->nama_lengkap, 0, 1) }}
                        </span>
                    @endif
                </div>
                <h5 class="fw-bold mb-1">{{ $pegawai->nama_lengkap }}</h5>
                <p class="text-muted small mb-1">{{ $pegawai->jabatan->nama_jabatan ?? '-' }}</p>
                <span class="badge bg-primary">{{ $pegawai->bidang->nama_bidang ?? '-' }}</span>

                <div class="mt-3">
                    <span class="badge {{ $pegawai->is_active ? 'bg-success' : 'bg-danger' }} me-1">
                        {{ $pegawai->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                    <span class="badge bg-secondary">{{ $pegawai->jenis_pegawai }}</span>
                </div>

                <hr>

                <div class="text-start">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">NIP</span>
                        <code class="small">{{ $pegawai->nip }}</code>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Pangkat</span>
                        <span class="small">{{ $pegawai->pangkat ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Masa Kerja</span>
                        <span class="small fw-semibold">{{ $pegawai->masa_kerja }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted small">Sisa Cuti</span>
                        <span class="fw-bold {{ $pegawai->sisa_cuti_tahunan > 0 ? 'text-success' : 'text-danger' }}">
                            {{ $pegawai->sisa_cuti_tahunan }} hari
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Pengajuan Singkat -->
        <div class="card card-custom">
            <div class="card-header-custom">
                <h5 class="card-title-custom">Statistik Cuti</h5>
            </div>
            <div class="card-body">
                @php
                    $totalCuti    = $pegawai->pengajuanCuti()->count();
                    $disetujui    = $pegawai->pengajuanCuti()->where('status','disetujui')->count();
                    $menunggu     = $pegawai->pengajuanCuti()->where('status','menunggu')->count();
                @endphp
                <div class="row g-2 text-center">
                    <div class="col-4">
                        <div class="fw-bold fs-4 text-primary">{{ $totalCuti }}</div>
                        <div class="small text-muted">Total</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold fs-4 text-success">{{ $disetujui }}</div>
                        <div class="small text-muted">Disetujui</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold fs-4 text-warning">{{ $menunggu }}</div>
                        <div class="small text-muted">Menunggu</div>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('admin.pengajuan.index', ['search' => $pegawai->nip]) }}"
                       class="btn btn-sm btn-outline-primary w-100">
                        <i class="bi bi-list-ul me-1"></i>Lihat Pengajuan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Lengkap -->
    <div class="col-12 col-xl-8">
        <div class="card card-custom mb-4">
            <div class="card-header-custom">
                <h5 class="card-title-custom">
                    <i class="bi bi-briefcase me-2"></i>Data Kepegawaian
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="detail-label">NIP</label>
                        <div class="detail-value"><code>{{ $pegawai->nip }}</code></div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Nama Lengkap</label>
                        <div class="detail-value fw-bold">{{ $pegawai->nama_lengkap }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Bidang</label>
                        <div class="detail-value">{{ $pegawai->bidang->nama_bidang ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Jabatan</label>
                        <div class="detail-value">{{ $pegawai->jabatan->nama_jabatan ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Pangkat/Golongan</label>
                        <div class="detail-value">{{ $pegawai->pangkat ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Jenis Pegawai</label>
                        <div class="detail-value">
                            <span class="badge bg-primary">{{ $pegawai->jenis_pegawai }}</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">TMT (Tanggal Masuk)</label>
                        <div class="detail-value">
                            {{ $pegawai->tanggal_masuk->isoFormat('D MMMM Y') }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Masa Kerja</label>
                        <div class="detail-value">{{ $pegawai->masa_kerja }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Sisa Cuti Tahunan</label>
                        <div class="detail-value">
                            <span class="badge fs-6 {{ $pegawai->sisa_cuti_tahunan > 6 ? 'bg-success' : ($pegawai->sisa_cuti_tahunan > 0 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                {{ $pegawai->sisa_cuti_tahunan }} hari
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Akun Login</label>
                        <div class="detail-value">
                            <code class="small">{{ $pegawai->user->nip ?? '-' }}</code>
                            <span class="badge {{ $pegawai->user->is_active ? 'bg-success' : 'bg-danger' }} ms-1">
                                {{ $pegawai->user->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-custom">
            <div class="card-header-custom">
                <h5 class="card-title-custom">
                    <i class="bi bi-person me-2"></i>Data Pribadi
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="detail-label">Jenis Kelamin</label>
                        <div class="detail-value">{{ $pegawai->jenis_kelamin_label }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Tempat, Tanggal Lahir</label>
                        <div class="detail-value">
                            {{ $pegawai->tempat_lahir }},
                            {{ $pegawai->tanggal_lahir->isoFormat('D MMMM Y') }}
                            ({{ $pegawai->umur }} tahun)
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Agama</label>
                        <div class="detail-value">{{ $pegawai->agama }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Status Pernikahan</label>
                        <div class="detail-value">{{ $pegawai->status_pernikahan }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">No. Telepon</label>
                        <div class="detail-value">{{ $pegawai->no_telepon ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Email</label>
                        <div class="detail-value">{{ $pegawai->email ?? '-' }}</div>
                    </div>
                    <div class="col-12">
                        <label class="detail-label">Alamat</label>
                        <div class="detail-value">{{ $pegawai->alamat }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection