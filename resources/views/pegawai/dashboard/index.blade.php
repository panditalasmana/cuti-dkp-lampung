@extends('layouts.app')
@section('title', 'Dashboard Pegawai')

@section('breadcrumb')
    <span class="breadcrumb-item active">Dashboard</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Selamat Datang, {{ explode(',', $pegawai->nama_lengkap)[0] }}!</h1>
        <p class="page-subtitle">{{ $pegawai->jabatan->nama_jabatan ?? '-' }} — {{ $pegawai->bidang->nama_bidang ?? '-' }} @if($pegawai->sub_bagian) ({{ $pegawai->sub_bagian }}) @endif</p>
    </div>
    <a href="{{ route('pegawai.pengajuan.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Ajukan Cuti
    </a>
</div>

<!-- Profil Singkat -->
<div class="card card-custom mb-4" style="background: linear-gradient(135deg, #0B5FA5, #1976D2); color:white; border:none; overflow: visible !important;">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-auto">
                <div class="avatar-lg">
                    @if($pegawai->foto)
                        <img src="{{ asset('storage/'.$pegawai->foto) }}" class="avatar-img-lg" alt="foto">
                    @else
                        <span class="avatar-initial-lg">{{ substr($pegawai->nama_lengkap, 0, 1) }}</span>
                    @endif
                </div>
            </div>
            
            <div class="col-12 col-sm">
                <h4 class="mb-1 text-white fw-bold mt-2 mt-sm-0">{{ $pegawai->nama_lengkap }}</h4>
                <div class="d-flex flex-wrap gap-3 text-white-50 small">
                    <span><i class="bi bi-person-badge me-1"></i>{{ $pegawai->nip }}</span>
                    <span><i class="bi bi-briefcase me-1"></i>{{ $pegawai->pangkat ?? '-' }}</span>
                    <span>
    <i class="bi bi-calendar3 me-1"></i>
    TMT:
    {{ \Carbon\Carbon::parse($pegawai->tanggal_masuk)->translatedFormat('d F Y') }}
</span>
                </div>
            </div>
            <div class="col-12 col-md-auto text-start text-md-end mt-3 mt-md-0 pt-3 pt-md-0 profile-sisa-cuti" style="min-width: 220px;">
                <div class="d-flex align-items-center justify-content-start justify-content-md-end gap-2 mb-2">
                    <span class="text-white-50 small">Sisa Kuota:</span>
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-sm dropdown-toggle text-white p-0 border-0 fw-semibold quota-select-btn" type="button" id="quotaDropdownMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            Cuti Tahunan
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="quotaDropdownMenu" style="font-size: 0.85rem; font-family: 'Poppins', sans-serif; max-height: 220px; overflow-y: auto;">
                            @foreach($quotas as $q)
                                <li>
                                    <a class="dropdown-item quota-option {{ $q['kode'] === 'CT' ? 'active' : '' }}" href="#" data-value="{{ $q['sisa'] }}" data-name="{{ $q['nama'] }}">
                                        {{ $q['nama'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="display-4 fw-bold text-white mb-1" id="quotaValue">{{ $pegawai->sisa_cuti_tahunan }}</div>
                <div class="text-white-50 small">hari tersisa</div>
            </div>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card stat-card--info">
            <div class="stat-card__icon"><i class="bi bi-file-earmark-text-fill"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__value">{{ $statistik['total'] }}</div>
                <div class="stat-card__label">Total Pengajuan</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-card--warning">
            <div class="stat-card__icon"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__value">{{ $statistik['menunggu'] }}</div>
                <div class="stat-card__label">Menunggu</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-card--success">
            <div class="stat-card__icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__value">{{ $statistik['disetujui'] }}</div>
                <div class="stat-card__label">Disetujui</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-card--danger">
            <div class="stat-card__icon"><i class="bi bi-x-circle-fill"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__value">{{ $statistik['ditolak'] }}</div>
                <div class="stat-card__label">Ditolak</div>
            </div>
        </div>
    </div>
</div>

<!-- Riwayat Terbaru -->
<div class="card card-custom">
    <div class="card-header-custom">
        <div>
            <h5 class="card-title-custom">Riwayat Pengajuan Terbaru</h5>
            <p class="card-subtitle-custom">5 pengajuan terakhir Anda</p>
        </div>
        <a href="{{ route('pegawai.riwayat.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-head">
                    <tr>
                        <th>Tanggal</th>
                        <th>Jenis Cuti</th>
                        <th>Periode</th>
                        <th>Lama</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuanList as $item)
                        <tr>
                            <td>{{ $item->tanggal_pengajuan->isoFormat('D MMM Y') }}</td>
                            <td>{{ $item->jenisCuti->nama_cuti }}</td>
                            <td>
                                <div class="small">{{ \Illuminate\Support\Carbon::parse($item->tanggal_mulai)->translatedFormat('d M Y') }}</div>
                                <div class="small text-muted">s.d. {{ $item->tanggal_selesai->isoFormat('D MMM Y') }}</div>
                            </td>
                            <td class="fw-semibold">{{ $item->lama_cuti }} hr</td>
                            <td>@include('components.status-badge', ['status' => $item->status])</td>
                            <td>
                                <a href="{{ route('pegawai.pengajuan.show', $item) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                Anda belum pernah mengajukan cuti.
                                <br><a href="{{ route('pegawai.pengajuan.create') }}">Ajukan sekarang</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const valueDisplay = document.getElementById('quotaValue');
    const dropdownBtn = document.getElementById('quotaDropdownMenu');
    const options = document.querySelectorAll('.quota-option');
    
    if (dropdownBtn && valueDisplay && options.length > 0) {
        options.forEach(opt => {
            opt.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Hapus kelas active dari opsi lain, tambahkan ke yang di-klik
                options.forEach(o => o.classList.remove('active'));
                this.classList.add('active');
                
                // Ubah teks tombol dropdown
                dropdownBtn.textContent = this.dataset.name;
                
                // Ubah angka kuota
                valueDisplay.textContent = this.dataset.value;
            });
        });
    }
});
</script>
@endpush