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
        <a href="{{ route('admin.laporan.export-pdf', ['tahun' => $tahun, 'bulan' => $bulan]) }}" class="btn btn-danger" target="_blank">
            <i class="bi bi-file-pdf me-1"></i>Export PDF
        </a>
        <a href="{{ route('admin.laporan.export-excel', ['tahun' => $tahun, 'bulan' => $bulan]) }}" class="btn btn-success">
            <i class="bi bi-file-excel me-1"></i>Export Excel
        </a>
    </div>
</div>

<!-- Filter -->
<div class="card card-custom mb-4">
    <div class="card-body">
        <form method="GET">
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <label class="form-label fw-semibold small">Rekapan</label>
                    <select name="bulan" class="form-select" onchange="this.form.submit()">
                        <option value="tahunan" {{ $bulan === 'tahunan' ? 'selected' : '' }}>Tahunan (Rekap Pegawai)</option>
                        @foreach([
                            '01' => 'Januari',
                            '02' => 'Februari',
                            '03' => 'Maret',
                            '04' => 'April',
                            '05' => 'Mei',
                            '06' => 'Juni',
                            '07' => 'Juli',
                            '08' => 'Agustus',
                            '09' => 'September',
                            '10' => 'Oktober',
                            '11' => 'November',
                            '12' => 'Desember'
                        ] as $num => $monthName)
                            <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>Bulanan - {{ $monthName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label fw-semibold small">Tahun</label>
                    <select name="tahun" class="form-select" onchange="this.form.submit()">
                        @foreach($tahunList as $thn)
                            <option value="{{ $thn }}" {{ $tahun == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                        @endforeach
                    </select>
                </div>
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
                <div class="stat-card__value">{{ $statistik['total_pengajuan'] }}</div>
                <div class="stat-card__label">Total Pengajuan {{ $periodeLabel }}</div>
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
    <!-- 1. Grafik Bulanan (Lebar 50%) -->
    <div class="col-12 col-xl-6">
        <div class="card card-custom h-100">
            <div class="card-header-custom">
                <div>
                    <h5 class="card-title-custom">Grafik Bulanan {{ $tahun }}</h5>
                    <p class="card-subtitle-custom">Status pengajuan per bulan</p>
                </div>
            </div>
            <div class="card-body d-flex align-items-center" style="min-height: 250px;">
                <canvas id="chartBulanan"></canvas>
            </div>
        </div>
    </div>
    
    <!-- 2. Grafik Per Jenis Cuti (Lebar 25%) -->
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card card-custom h-100">
            <div class="card-header-custom">
                <div>
                    <h5 class="card-title-custom">Per Jenis Cuti</h5>
                </div>
            </div>
            <div class="card-body d-flex flex-column justify-content-between">
                <div style="height: 140px;" class="mx-auto position-relative w-100">
                    <canvas id="chartJenisCuti"></canvas>
                </div>
                <div class="mt-3" style="max-height: 120px; overflow-y: auto;">
                    @php
                        $jenisColors = ['#0d6efd','#fd7e14','#dc3545','#ec4899','#6f42c1','#14b8a6'];
                    @endphp
                    @foreach($perJenisCuti as $index => $jc)
                        @php
                            $color = $jenisColors[$index % count($jenisColors)];
                        @endphp
                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                            <span class="small text-truncate" style="max-width:110px; font-size: 11px;">{{ $jc->nama_cuti }}</span>
                            <span class="badge" style="font-size: 10px; background-color: {{ $color }}; color: #fff;">{{ $jc->total }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Grafik Per Bidang (Lebar 25%) -->
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card card-custom h-100">
            <div class="card-header-custom">
                <div>
                    <h5 class="card-title-custom">Per Bidang</h5>
                </div>
            </div>
            <div class="card-body d-flex flex-column justify-content-between">
                <div style="height: 140px;" class="mx-auto position-relative w-100">
                    <canvas id="chartBidang"></canvas>
                </div>
                <div class="mt-3" style="max-height: 120px; overflow-y: auto;">
                    @php
                        $bidangColors = ['#0B5FA5','#1976D2','#4FC3F7','#0288D1','#01579B','#29B6F6'];
                    @endphp
                    @foreach($perBidang as $index => $b)
                        @php
                            $color = $bidangColors[$index % count($bidangColors)];
                        @endphp
                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                            <span class="small text-truncate" style="max-width:110px; font-size: 11px;">{{ $b->nama_bidang }}</span>
                            <span class="badge" style="font-size: 10px; background-color: {{ $color }}; color: #fff;">{{ $b->total }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Laporan Table -->
<div class="card card-custom mt-4">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
        <div>
            <h5 class="card-title-custom">Preview Rekapitulasi Cuti ({{ $bulan === 'tahunan' ? 'Tahunan' : 'Bulanan' }})</h5>
            <p class="card-subtitle-custom">Menampilkan data cuti disetujui pada periode yang dipilih</p>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            @if($bulan === 'tahunan')
                <!-- Table Tahunan (Rekap per Pegawai) -->
                <table class="table table-hover mb-0">
                    <thead class="table-head">
                        <tr>
                            <th>No</th>
                            <th>NIP</th>
                            <th>Nama Pegawai</th>
                            <th>Bidang / Jabatan</th>
                            <th class="text-center">Tahunan (Hari)</th>
                            <th class="text-center">Besar (Hari)</th>
                            <th class="text-center">Sakit (Hari)</th>
                            <th class="text-center">Melahirkan (Bulan)</th>
                            <th class="text-center">Alasan Penting (Hari)</th>
                            <th class="text-center">CLTN (Tahun)</th>
                            <th class="text-center">Total Terpakai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData as $index => $item)
                            <tr>
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td><code class="small">{{ $item['pegawai']->nip }}</code></td>
                                <td class="fw-semibold">{{ $item['pegawai']->nama_lengkap }}</td>
                                <td>
                                    <div class="small fw-semibold">{{ $item['pegawai']->bidang->nama_bidang ?? '-' }}</div>
                                    <div class="small text-muted" style="font-size:0.75rem;">{{ $item['pegawai']->jabatan->nama_jabatan ?? '-' }}</div>
                                </td>
                                <td class="text-center">{{ $item['cuti']['CT'] }}</td>
                                <td class="text-center">{{ $item['cuti']['CB'] }}</td>
                                <td class="text-center">{{ $item['cuti']['CS'] }}</td>
                                <td class="text-center">{{ $item['cuti']['CM'] }}</td>
                                <td class="text-center">{{ $item['cuti']['CAK'] }}</td>
                                <td class="text-center">{{ $item['cuti']['CLN'] }}</td>
                                <td class="text-center fw-bold">
                                    @php
                                        $hari = $item['cuti']['CT'] + $item['cuti']['CB'] + $item['cuti']['CS'] + $item['cuti']['CAK'];
                                        $bulanCuti = $item['cuti']['CM'];
                                        $tahunCuti = $item['cuti']['CLN'];
                                        
                                        $parts = [];
                                        if ($hari > 0) $parts[] = $hari . ' hr';
                                        if ($bulanCuti > 0) $parts[] = $bulanCuti . ' bln';
                                        if ($tahunCuti > 0) $parts[] = $tahunCuti . ' thn';
                                        
                                        echo empty($parts) ? '0' : implode(', ', $parts);
                                    @endphp
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4 text-muted">Tidak ada data pegawai.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @else
                <!-- Table Bulanan (Detail Pengajuan) -->
                <table class="table table-hover mb-0">
                    <thead class="table-head">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Pengajuan</th>
                            <th>NIP</th>
                            <th>Nama Pegawai</th>
                            <th>Bidang / Jabatan</th>
                            <th>Jenis Cuti</th>
                            <th>Tanggal Cuti</th>
                            <th class="text-center">Lama Cuti</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData as $index => $item)
                            <tr>
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td>{{ $item->tanggal_pengajuan?->format('d/m/Y') ?? '-' }}</td>
                                <td><code class="small">{{ $item->pegawai->nip ?? '-' }}</code></td>
                                <td class="fw-semibold">{{ $item->pegawai->nama_lengkap ?? '-' }}</td>
                                <td>
                                    <div class="small fw-semibold">{{ $item->pegawai->bidang->nama_bidang ?? '-' }}</div>
                                    <div class="small text-muted" style="font-size:0.75rem;">{{ $item->pegawai->jabatan->nama_jabatan ?? '-' }}</div>
                                </td>
                                <td>{{ $item->jenisCuti->nama_cuti ?? '-' }}</td>
                                <td>
                                    <div class="small">{{ $item->tanggal_mulai?->format('d/m/Y') }} s.d. {{ $item->tanggal_selesai?->format('d/m/Y') }}</div>
                                </td>
                                <td class="text-center fw-semibold">{{ $item->lama_cuti_display }}</td>
                                <td>
                                    @include('components.status-badge', ['status' => $item->status])
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">Tidak ada data pengajuan cuti pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const perBulan  = @json($perBulan);
const perJenisCuti = @json($perJenisCuti);
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
        maintainAspectRatio: false,
        plugins: { legend: { position: 'top' } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
    },
});

if (perJenisCuti.length > 0) {
    new Chart(document.getElementById('chartJenisCuti'), {
        type: 'doughnut',
        data: {
            labels: perJenisCuti.map(jc => jc.nama_cuti),
            datasets: [{
                data: perJenisCuti.map(jc => jc.total),
                backgroundColor: ['#0d6efd','#fd7e14','#dc3545','#ec4899','#6f42c1','#14b8a6'],
                borderWidth: 2, borderColor: '#fff',
            }],
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } },
    });
}

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
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } },
    });
}
</script>
@endpush