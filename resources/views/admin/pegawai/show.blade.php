@extends('layouts.app')
@section('title', 'Detail Pegawai — ' . $pegawai->nama_lengkap)

@push('styles')
<style>
    .nav-tabs .nav-link {
        color: #64748b;
        background: none;
        border: none;
        border-bottom: 3px solid transparent;
        transition: all 0.2s ease-in-out;
    }
    .nav-tabs .nav-link:hover {
        color: #1e293b;
        border-color: #cbd5e1;
    }
    .nav-tabs .nav-link.active {
        color: #0d6efd !important;
        border-color: #0d6efd !important;
        background: none !important;
    }
</style>
@endpush

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
    <div class="d-flex gap-2 align-items-center flex-wrap">
        <a href="{{ route('admin.pegawai.edit', $pegawai) }}" class="btn btn-warning" title="Edit">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        <a href="{{ route('admin.pegawai.index') }}" class="btn btn-outline-secondary px-2 px-sm-3" title="Kembali">
            <i class="bi bi-arrow-left"></i><span class="d-none d-sm-inline ms-1">Kembali</span>
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
                @if($pegawai->sub_bagian)
                    <span class="badge bg-secondary">{{ $pegawai->sub_bagian }}</span>
                @endif

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

    <!-- Data Lengkap (Tabbed Layout) -->
    <div class="col-12 col-xl-8">
        <div class="card card-custom mb-4">
            <div class="card-header-custom p-0" style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                <ul class="nav nav-tabs px-3 pt-2" id="profileTab" role="tablist" style="border-bottom: none;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-semibold" id="kepegawaian-tab" data-bs-toggle="tab" data-bs-target="#kepegawaian" type="button" role="tab" aria-controls="kepegawaian" aria-selected="true" style="border: none; padding: 12px 16px; border-bottom: 3px solid transparent;">
                            <i class="bi bi-briefcase me-2 text-primary"></i>Data Kepegawaian
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="pribadi-tab" data-bs-toggle="tab" data-bs-target="#pribadi" type="button" role="tab" aria-controls="pribadi" aria-selected="false" style="border: none; padding: 12px 16px; border-bottom: 3px solid transparent;">
                            <i class="bi bi-person me-2 text-success"></i>Data Pribadi
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="kuota-tab" data-bs-toggle="tab" data-bs-target="#kuota" type="button" role="tab" aria-controls="kuota" aria-selected="false" style="border: none; padding: 12px 16px; border-bottom: 3px solid transparent;">
                            <i class="bi bi-pie-chart me-2 text-info"></i>Sisa Kuota Cuti
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="riwayat-tab" data-bs-toggle="tab" data-bs-target="#riwayat" type="button" role="tab" aria-controls="riwayat" aria-selected="false" style="border: none; padding: 12px 16px; border-bottom: 3px solid transparent;">
                            <i class="bi bi-calendar-check me-2 text-warning"></i>Riwayat Cuti
                        </button>
                    </li>
                </ul>
            </div>
            
            <div class="card-body p-4">
                <div class="tab-content" id="profileTabContent">
                    <!-- Tab Data Kepegawaian -->
                    <div class="tab-pane fade show active" id="kepegawaian" role="tabpanel" aria-labelledby="kepegawaian-tab">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="detail-label text-muted small fw-semibold">NIP</label>
                                <div class="detail-value fs-6 mt-1"><code>{{ $pegawai->nip }}</code></div>
                            </div>
                            <div class="col-sm-6">
                                <label class="detail-label text-muted small fw-semibold">Nama Lengkap</label>
                                <div class="detail-value fs-6 mt-1 fw-bold">{{ $pegawai->nama_lengkap }}</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="detail-label text-muted small fw-semibold">Bidang / UPTD</label>
                                <div class="detail-value fs-6 mt-1">{{ $pegawai->bidang->nama_bidang ?? '-' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="detail-label text-muted small fw-semibold">Sub Bagian / Seksi</label>
                                <div class="detail-value fs-6 mt-1">{{ $pegawai->sub_bagian ?? '-' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="detail-label text-muted small fw-semibold">Jabatan</label>
                                <div class="detail-value fs-6 mt-1">{{ $pegawai->jabatan->nama_jabatan ?? '-' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="detail-label text-muted small fw-semibold">Pangkat/Golongan</label>
                                <div class="detail-value fs-6 mt-1">{{ $pegawai->pangkat ?? '-' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="detail-label text-muted small fw-semibold">Jenis Pegawai</label>
                                <div class="detail-value fs-6 mt-1">
                                    <span class="badge bg-primary">{{ $pegawai->jenis_pegawai }}</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="detail-label text-muted small fw-semibold">TMT (Tanggal Masuk)</label>
                                <div class="detail-value fs-6 mt-1">
                                    {{ $pegawai->tanggal_masuk->isoFormat('D MMMM Y') }}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="detail-label text-muted small fw-semibold">Masa Kerja</label>
                                <div class="detail-value fs-6 mt-1">{{ $pegawai->masa_kerja }}</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="detail-label text-muted small fw-semibold">Sisa Cuti Tahunan</label>
                                <div class="detail-value fs-6 mt-1">
                                    <span class="badge fs-6 {{ $pegawai->sisa_cuti_tahunan > 6 ? 'bg-success' : ($pegawai->sisa_cuti_tahunan > 0 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ $pegawai->sisa_cuti_tahunan }} hari
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="detail-label text-muted small fw-semibold">Akun Login</label>
                                <div class="detail-value fs-6 mt-1">
                                    <code class="small">{{ $pegawai->user->nip ?? '-' }}</code>
                                    <span class="badge {{ ($pegawai->user->is_active ?? false) ? 'bg-success' : 'bg-danger' }} ms-1">
                                        {{ ($pegawai->user->is_active ?? false) ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Data Pribadi -->
                    <div class="tab-pane fade" id="pribadi" role="tabpanel" aria-labelledby="pribadi-tab">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="detail-label text-muted small fw-semibold">Jenis Kelamin</label>
                                <div class="detail-value fs-6 mt-1">{{ $pegawai->jenis_kelamin_label }}</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="detail-label text-muted small fw-semibold">Tempat, Tanggal Lahir</label>
                                <div class="detail-value fs-6 mt-1">
                                    {{ $pegawai->tempat_lahir }},
                                    {{ $pegawai->tanggal_lahir->isoFormat('D MMMM Y') }}
                                    ({{ $pegawai->umur }} tahun)
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="detail-label text-muted small fw-semibold">No. Telepon</label>
                                <div class="detail-value fs-6 mt-1">{{ $pegawai->no_telepon ?? '-' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="detail-label text-muted small fw-semibold">Email</label>
                                <div class="detail-value fs-6 mt-1">{{ $pegawai->email ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Sisa Kuota Cuti -->
                    <div class="tab-pane fade" id="kuota" role="tabpanel" aria-labelledby="kuota-tab">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.95rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Jenis Cuti</th>
                                        <th class="text-center">Jatah Maksimal</th>
                                        <th class="text-center">Sisa Kuota</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quotas as $q)
                                        <tr>
                                            <td class="fw-semibold text-dark">{{ $q['nama'] }}</td>
                                            <td class="text-center text-muted">
                                                @if(is_null($q['maks']))
                                                    Tidak Terbatas
                                                @else
                                                    {{ $q['maks'] }} {{ $q['satuan'] }}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge fs-7 px-3 py-2 {{ $q['sisa'] > 0 ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $q['sisa'] }} {{ $q['satuan'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab Riwayat Cuti -->
                    <div class="tab-pane fade" id="riwayat" role="tabpanel" aria-labelledby="riwayat-tab">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.95rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tgl Pengajuan</th>
                                        <th>Jenis Cuti</th>
                                        <th>Rentang Tanggal</th>
                                        <th class="text-center">Lama Cuti</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($riwayatCuti as $rc)
                                        <tr>
                                            <td>{{ $rc->tanggal_pengajuan->isoFormat('D MMM Y') }}</td>
                                            <td class="fw-semibold text-dark">{{ $rc->jenisCuti->nama_cuti }}</td>
                                            <td>
                                                <div class="small text-dark">{{ $rc->tanggal_mulai->isoFormat('D MMM Y') }}</div>
                                                <div class="small text-muted">s.d. {{ $rc->tanggal_selesai->isoFormat('D MMM Y') }}</div>
                                            </td>
                                            <td class="text-center fw-semibold text-dark">{{ $rc->lama_cuti_display }}</td>
                                            <td class="text-center">
                                                @include('components.status-badge', ['status' => $rc->status])
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.pengajuan.show', $rc) }}" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-5">
                                                <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                                                Belum ada riwayat pengajuan cuti.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection