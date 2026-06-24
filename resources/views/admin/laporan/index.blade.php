@extends('layouts.app')
@section('title', 'Laporan & Statistik')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-item active">Laporan & Statistik</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Laporan & Statistik</h1>
        <p class="page-subtitle">Rekap pengajuan cuti pegawai DKP Provinsi Lampung</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.laporan.export-pdf', ['tahun' => $tahun]) }}" class="btn btn-danger" target="_blank">
            <i class="bi bi-file-pdf me-1"></i>Export PDF
        </a>
        <a href="{{ route('admin.laporan.export-excel', ['tahun' => $tahun]) }}" class="btn btn-success">
            <i class="bi bi-file-excel me-1"></i>Export Excel
        </a>
    </div>
</div>

<!-- Tahun Filter -->
<div class="card card-custom mb-4">
    <div class="card-body">
        <form method="GET" class="d-flex gap-3 align-items-end flex-wrap">
            <div>
                <label class="form-label fw-semibold small">Tahun</label>
                <select name="tahun" class="form-select" onchange="this.form.submit()">
                    @foreach($tahunList as $thn)
                        <option value="{{ $thn }}" {{ $tahun == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

<!-- Stat Summary -->
<div class="row g-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card stat-card--info">
            <div class="stat-card__icon"><i class="bi bi-file-earmark-text-fill"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__value">{{ array_sum(array_map(fn($b) => $b['menunggu'] + $b['disetujui'] + $b['ditolak'], $perBulan)) }}</div>
                <div class="stat-card__label">Total Pengajuan {{ $tahun }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-card--warning">
            <div class="stat-card__icon"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__value">{{ $statistik['total_menunggu'] }}</div>
                <div class="stat-card__label">Menunggu Verifikasi</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-card--success">
            <div class="stat-card__icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__value">{{ $statistik['total_disetujui'] }}</div>
                <div class="stat-card__label">Disetujui</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-card--danger">
            <div class="stat-card__icon"><i class="bi bi-x-circle-fill"></i></div>
            <div class="stat-card__body">
                <div class="stat-card__value">{{ $statistik['total_ditolak'] }}</div>
                <div class="stat-card__label">Ditolak</div>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row g-4">
    <div class="col-12 col-xl-8">
        <div class="card card-custom">
            <div class="card-header-custom">
                <div>
                    <h5 class="card-title-custom">Grafik Bulanan {{ $tahun }}</h5>
                    <p class="card-subtitle-custom">Status pengajuan per bulan</p>
                </div>
            </div>
            <div class="card-body">
                <canvas id="chartBulanan" height="90"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-4">
        <div class="card card-custom">
            <div class="card-header-custom">
                <div>
                    <h5 class="card-title-custom">Per Bidang {{ $tahun }}</h5>
                </div>
            </div>
            <div class="card-body">
                <canvas id="chartBidang"></canvas>
                <div class="mt-3">
                    @foreach($perBidang as $b)
                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                            <span class="small text-truncate" style="max-width:160px">{{ $b->nama_bidang }}</span>
                            <span class="badge bg-primary">{{ $b->total }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const perBulan  = @json($perBulan);
const perBidang = @json($perBidang);
const labels    = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

new Chart(document.getElementById('chartBulanan'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            { label: 'Menunggu',  data: Object.values(perBulan).map(b => b.menunggu),  backgroundColor: '#f59e0b', borderRadius: 5 },
            { label: 'Disetujui', data: Object.values(perBulan).map(b => b.disetujui), backgroundColor: '#10b981', borderRadius: 5 },
            { label: 'Ditolak',   data: Object.values(perBulan).map(b => b.ditolak),   backgroundColor: '#ef4444', borderRadius: 5 },
        ],
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
    },
});

if (perBidang.length > 0) {
    new Chart(document.getElementById('chartBidang'), {
        type: 'doughnut',
        data: {
            labels: perBidang.map(b => b.nama_bidang),
            datasets: [{
                data: perBidang.map(b => b.total),
                backgroundColor: ['#0B5FA5','#1976D2','#4FC3F7','#0288D1','#01579B','#29B6F6'],
                borderWidth: 2, borderColor: '#fff',
            }],
        },
        options: { responsive: true, plugins: { legend: { display: false } } },
    });
}
</script>
@endpush