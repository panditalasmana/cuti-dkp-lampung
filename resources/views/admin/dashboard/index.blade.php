@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('breadcrumb')
    <span class="breadcrumb-item active">Dashboard</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Ringkasan aktivitas pengajuan cuti pegawai</p>
    </div>
    <div class="page-header-actions">
        <span class="badge bg-primary px-3 py-2">
            <i class="bi bi-calendar3 me-1"></i>{{ now()->isoFormat('D MMMM Y') }}
        </span>
    </div>
</div>

<!-- ═══ STAT CARDS ══════════════════════════════════════════════════════════ -->
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card stat-card--primary">
            <div class="stat-card__icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__value">{{ number_format($totalPegawai) }}</div>
                <div class="stat-card__label">Total Pegawai Aktif</div>
            </div>
            <div class="stat-card__footer">
                <a href="{{ route('admin.pegawai.index') }}" class="stat-card__link">Lihat semua <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card stat-card--warning">
            <div class="stat-card__icon"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__value">{{ number_format($statistik['total_menunggu']) }}</div>
                <div class="stat-card__label">Menunggu Verifikasi</div>
            </div>
            <div class="stat-card__footer">
                <a href="{{ route('admin.pengajuan.index', ['status' => 'menunggu']) }}" class="stat-card__link">Proses sekarang <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card stat-card--success">
            <div class="stat-card__icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__value">{{ number_format($statistik['total_disetujui']) }}</div>
                <div class="stat-card__label">Disetujui</div>
            </div>
            <div class="stat-card__footer">
                <a href="{{ route('admin.pengajuan.index', ['status' => 'disetujui']) }}" class="stat-card__link">Lihat detail <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card stat-card--info">
            <div class="stat-card__icon"><i class="bi bi-calendar-check-fill"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__value">{{ number_format($statistik['total_tahun_ini']) }}</div>
                <div class="stat-card__label">Pengajuan Tahun {{ $tahun }}</div>
            </div>
            <div class="stat-card__footer">
                <a href="{{ route('admin.laporan.index') }}" class="stat-card__link">Lihat laporan <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- ═══ CHARTS ══════════════════════════════════════════════════════════════ -->
<div class="row g-4 mb-4">
    <div class="col-12 col-xl-8">
        <div class="card card-custom h-100">
            <div class="card-header-custom">
                <div>
                    <h5 class="card-title-custom">Grafik Pengajuan Cuti Bulanan</h5>
                    <p class="card-subtitle-custom">Tahun {{ $tahun }}</p>
                </div>
                <i class="bi bi-bar-chart-fill text-primary"></i>
            </div>
            <div class="card-body">
                <canvas id="chartBulanan" height="90"></canvas>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="card card-custom h-100">
            <div class="card-header-custom">
                <div>
                    <h5 class="card-title-custom">Pengajuan per Bidang</h5>
                    <p class="card-subtitle-custom">Tahun {{ $tahun }}</p>
                </div>
                <i class="bi bi-pie-chart-fill text-primary"></i>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="chartBidang"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- ═══ PENGAJUAN TERBARU ════════════════════════════════════════════════════ -->
<div class="card card-custom">
    <div class="card-header-custom">
        <div>
            <h5 class="card-title-custom">Pengajuan Menunggu Verifikasi</h5>
            <p class="card-subtitle-custom">Daftar pengajuan terbaru yang perlu diproses</p>
        </div>
        <a href="{{ route('admin.pengajuan.index', ['status' => 'menunggu']) }}" class="btn btn-sm btn-outline-primary">
            Lihat Semua
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-head">
                    <tr>
                        <th>Nomor Surat</th>
                        <th>Pegawai</th>
                        <th>Bidang</th>
                        <th>Jenis Cuti</th>
                        <th>Tanggal Mulai</th>
                        <th>Lama</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuanBaru as $item)
                        <tr>
                            <td><code class="text-primary">{{ $item->nomor_surat }}</code></td>
                            <td>
                                <div class="fw-semibold">{{ $item->pegawai->nama_lengkap }}</div>
                                <small class="text-muted">{{ $item->pegawai->nip }}</small>
                            </td>
                            <td><span class="text-muted">{{ $item->pegawai->bidang->nama_bidang ?? '-' }}</span></td>
                            <td><span class="badge badge-cuti">{{ $item->jenisCuti->nama_cuti }}</span></td>
                            <td>{{ $item->tanggal_mulai->isoFormat('D MMM Y') }}</td>
                            <td><span class="fw-semibold">{{ $item->lama_cuti }} hari</span></td>
                            <td>
                                <a href="{{ route('admin.pengajuan.show', $item) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> Proses
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                Tidak ada pengajuan yang menunggu verifikasi.
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
// Data dari controller
const perBulan = @json($perBulan);
const perBidang = @json($perBidang);
const bulanLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

// Chart Bulanan
new Chart(document.getElementById('chartBulanan'), {
    type: 'bar',
    data: {
        labels: bulanLabels,
        datasets: [
            {
                label: 'Menunggu',
                data: Object.values(perBulan).map(b => b.menunggu),
                backgroundColor: '#f59e0b',
                borderRadius: 6,
            },
            {
                label: 'Disetujui',
                data: Object.values(perBulan).map(b => b.disetujui),
                backgroundColor: '#10b981',
                borderRadius: 6,
            },
            {
                label: 'Ditolak',
                data: Object.values(perBulan).map(b => b.ditolak),
                backgroundColor: '#ef4444',
                borderRadius: 6,
            },
        ],
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } },
        },
    },
});

// Chart per Bidang
const bidangLabels = perBidang.map(b => b.nama_bidang);
const bidangData   = perBidang.map(b => b.total);
const colors = ['#0B5FA5','#1976D2','#4FC3F7','#0288D1','#01579B','#29B6F6'];

if (bidangData.length > 0) {
    new Chart(document.getElementById('chartBidang'), {
        type: 'doughnut',
        data: {
            labels: bidangLabels,
            datasets: [{
                data: bidangData,
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: '#fff',
            }],
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } },
            },
        },
    });
} else {
    document.getElementById('chartBidang').closest('.card-body').innerHTML =
        '<p class="text-muted text-center">Belum ada data.</p>';
}
</script>
@endpush