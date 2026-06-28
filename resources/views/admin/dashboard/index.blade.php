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

<!-- ═══ CHARTS & MONITORING ════════════════════════════════════════════════════ -->
<div class="row g-4 mb-4">
    <!-- Kolom Kiri: Chart dan Tabel Monitoring -->
    <div class="col-12 col-xl-8 d-flex flex-column gap-4">
        <!-- Card 1: Grafik Bulanan -->
        <div class="card card-custom mb-0">
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

        <!-- Card 2: Monitoring Harian (Tabel) -->
        <div class="card card-custom mb-0">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title-custom" id="monitoring-title">Pegawai Cuti — Hari Ini</h5>
                    <p class="card-subtitle-custom">Daftar pegawai yang sedang aktif cuti pada tanggal terpilih</p>
                </div>
                <span class="badge bg-primary px-3 py-2" id="monitoring-count" style="font-size: 11px;">0 Orang</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                    <table class="table table-hover mb-0">
                        <thead class="table-head">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="30%">Nama / NIP</th>
                                <th width="25%">Bidang / Subbag</th>
                                <th width="15%">Jenis Cuti</th>
                                <th width="15%">Periode Cuti</th>
                                <th width="10%">Status</th>
                            </tr>
                        </thead>
                        <tbody id="monitoring-table-body">
                            <!-- Baris pegawai cuti terupdate dinamis -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Kalender -->
    <div class="col-12 col-xl-4">
        <div class="card card-custom h-100">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title-custom">Kalender Cuti Pegawai</h5>
                    <p class="card-subtitle-custom">Pilih tanggal untuk melihat detail di sebelah kiri</p>
                </div>
                <i class="bi bi-calendar3 text-primary fs-5"></i>
            </div>
            <div class="card-body p-3">
                <!-- Kalender Header -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <button type="button" class="btn btn-sm btn-outline-primary px-2 py-1" id="prev-month-btn">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <h6 class="m-0 fw-semibold text-primary" id="calendar-month-year">Juni 2026</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary px-2 py-1" id="next-month-btn">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>

                <!-- Kalender Weekdays -->
                <div class="calendar-weekdays mb-2">
                    <div>Min</div>
                    <div>Sen</div>
                    <div>Sel</div>
                    <div>Rab</div>
                    <div>Kam</div>
                    <div>Jum</div>
                    <div>Sab</div>
                </div>

                <!-- Kalender Days -->
                <div class="calendar-days" id="calendar-days-grid">
                    <!-- Sel tanggal dinamis -->
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Weekdays Header */
    .calendar-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        text-align: center;
        font-weight: 600;
        font-size: 11px;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .calendar-weekdays div {
        padding: 4px 0;
    }

    /* Days Grid */
    .calendar-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 5px;
    }

    /* Day Cell */
    .day-cell {
        aspect-ratio: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 500;
        color: #2d3748;
        border-radius: 8px;
        cursor: pointer;
        position: relative;
        transition: all 0.2s ease;
        background-color: #f7fafc;
        border: 1px solid #edf2f7;
    }
    .day-cell:hover {
        background-color: #ebf8ff;
        border-color: #bee3f8;
        color: #0b5fa5;
        transform: translateY(-1px);
    }
    
    /* Other Month Days */
    .day-cell.other-month {
        color: #cbd5e0;
        background-color: transparent;
        border-color: transparent;
        cursor: default;
    }
    .day-cell.other-month:hover {
        background-color: transparent;
        border-color: transparent;
        transform: none;
        color: #cbd5e0;
    }

    /* Today Highlight */
    .day-cell.today {
        border: 1.5px solid #0b5fa5;
        color: #0b5fa5;
        font-weight: 700;
    }

    /* Selected Day Highlight */
    .day-cell.selected {
        background-color: #0b5fa5 !important;
        border-color: #0b5fa5 !important;
        color: #fff !important;
    }
    .day-cell.selected .day-badge {
        background-color: #fff !important;
        color: #0b5fa5 !important;
    }

    /* Day Badge for active leaves */
    .day-badge {
        position: absolute;
        top: 2px;
        right: 2px;
        min-width: 14px;
        height: 14px;
        padding: 0 3px;
        font-size: 8px;
        font-weight: 700;
        color: #fff;
        border-radius: 7px;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    .day-badge.badge-approved {
        background-color: #10b981; /* Hijau */
    }
    .day-badge.badge-pending {
        background-color: #f59e0b; /* Kuning/Oranye */
        color: #fff;
    }
    .day-badge.badge-both {
        background-color: #3b82f6; /* Biru */
    }
</style>
@endpush

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
                        <th>Tanggal</th>
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
                            <td>{{ $item->tanggal_pengajuan->isoFormat('D MMM Y') }}</td>
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

// Calendar Cuti Pegawai Logic
const allCuti = @json($allCuti);
let currentDate = new Date();
let selectedDate = new Date();

function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();

    const monthNames = [
        "Januari", "Februari", "Maret", "April", "Mei", "Juni",
        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
    ];

    document.getElementById('calendar-month-year').innerText = `${monthNames[month]} ${year}`;

    const grid = document.getElementById('calendar-days-grid');
    grid.innerHTML = '';

    const firstDayIndex = new Date(year, month, 1).getDay();
    const totalDays = new Date(year, month + 1, 0).getDate();
    const prevTotalDays = new Date(year, month, 0).getDate();

    // Render days from previous month
    for (let i = firstDayIndex; i > 0; i--) {
        const prevDay = prevTotalDays - i + 1;
        const cell = document.createElement('div');
        cell.className = 'day-cell other-month';
        cell.innerText = prevDay;
        grid.appendChild(cell);
    }

    // Render current month days
    for (let day = 1; day <= totalDays; day++) {
        const cell = document.createElement('div');
        cell.className = 'day-cell';
        cell.innerText = day;

        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        
        const today = new Date();
        if (year === today.getFullYear() && month === today.getMonth() && day === today.getDate()) {
            cell.classList.add('today');
        }

        if (year === selectedDate.getFullYear() && month === selectedDate.getMonth() && day === selectedDate.getDate()) {
            cell.classList.add('selected');
        }

        // Find active leaves on this day
        const activeLeaves = allCuti.filter(cuti => {
            const start = new Date(cuti.tanggal_mulai);
            const end = new Date(cuti.tanggal_selesai);
            start.setHours(0,0,0,0);
            end.setHours(0,0,0,0);
            const current = new Date(year, month, day);
            current.setHours(0,0,0,0);
            return current >= start && current <= end;
        });

        if (activeLeaves.length > 0) {
            const badge = document.createElement('span');
            badge.className = 'day-badge';
            badge.innerText = activeLeaves.length;

            const hasApproved = activeLeaves.some(l => l.status === 'disetujui');
            const hasPending = activeLeaves.some(l => l.status === 'menunggu');

            if (hasApproved && hasPending) {
                badge.classList.add('badge-both');
            } else if (hasApproved) {
                badge.classList.add('badge-approved');
            } else {
                badge.classList.add('badge-pending');
            }
            cell.appendChild(badge);
        }

        cell.addEventListener('click', () => {
            selectedDate = new Date(year, month, day);
            renderCalendar();
            updateMonitoringList(dateStr);
        });

        grid.appendChild(cell);
    }

    // Fill remaining cells for standard 6-row layout
    const totalCellsSoFar = firstDayIndex + totalDays;
    const remainingCells = 42 - totalCellsSoFar;
    for (let i = 1; i <= remainingCells; i++) {
        const cell = document.createElement('div');
        cell.className = 'day-cell other-month';
        cell.innerText = i;
        grid.appendChild(cell);
    }
}

function updateMonitoringList(dateStr) {
    const [year, month, day] = dateStr.split('-').map(Number);
    const dateObj = new Date(year, month - 1, day);
    
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const formattedDate = dateObj.toLocaleDateString('id-ID', options);
    document.getElementById('monitoring-title').innerText = `Pegawai Cuti — ${formattedDate}`;

    const activeLeaves = allCuti.filter(cuti => {
        const start = new Date(cuti.tanggal_mulai);
        const end = new Date(cuti.tanggal_selesai);
        start.setHours(0,0,0,0);
        end.setHours(0,0,0,0);
        const current = new Date(year, month - 1, day);
        current.setHours(0,0,0,0);
        return current >= start && current <= end;
    });

    document.getElementById('monitoring-count').innerText = `${activeLeaves.length} Orang`;
    
    const tbody = document.getElementById('monitoring-table-body');
    tbody.innerHTML = '';

    if (activeLeaves.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    <i class="bi bi-calendar-x fs-4 d-block mb-2"></i>
                    <span style="font-size: 11px;">Tidak ada pegawai yang cuti pada tanggal ini.</span>
                </td>
            </tr>
        `;
        return;
    }

    activeLeaves.forEach((cuti, index) => {
        const startFormatted = formatDateIndo(cuti.tanggal_mulai);
        const endFormatted = formatDateIndo(cuti.tanggal_selesai);
        
        const statusText = cuti.status === 'disetujui' ? 'Disetujui' : 'Menunggu';
        const statusBadgeClass = cuti.status === 'disetujui' ? 'bg-success' : 'bg-warning text-dark';
        
        const rowHtml = `
            <tr>
                <td class="align-middle text-center" style="font-size: 11px;">${index + 1}</td>
                <td class="align-middle">
                    <div class="fw-semibold text-dark" style="font-size: 11px;">${cuti.nama}</div>
                    <small class="text-muted" style="font-size: 9.5px;">NIP. ${cuti.nip}</small>
                </td>
                <td class="align-middle text-muted" style="font-size: 11px;">${cuti.bidang}</td>
                <td class="align-middle">
                    <span class="badge bg-light text-primary border border-primary-subtle" style="font-size: 8px; padding: 2px 4px;">
                        ${cuti.jenis_cuti}
                    </span>
                </td>
                <td class="align-middle text-secondary" style="font-size: 10px;">
                    <i class="bi bi-clock me-1"></i>${startFormatted} s.d. ${endFormatted}
                </td>
                <td class="align-middle">
                    <span class="badge ${statusBadgeClass}" style="font-size: 8px; padding: 2px 4px;">
                        ${statusText}
                    </span>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', rowHtml);
    });
}

function formatDateIndo(dateStr) {
    const [year, month, day] = dateStr.split('-').map(Number);
    const date = new Date(year, month - 1, day);
    const months = [
        "Jan", "Feb", "Mar", "Apr", "Mei", "Jun",
        "Jul", "Agu", "Sep", "Okt", "Nov", "Des"
    ];
    return `${day} ${months[date.getMonth()]} ${year}`;
}

// Attach navigations
document.getElementById('prev-month-btn').addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
});

document.getElementById('next-month-btn').addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
});

// Initial load
renderCalendar();
const todayStr = `${selectedDate.getFullYear()}-${String(selectedDate.getMonth() + 1).padStart(2, '0')}-${String(selectedDate.getDate()).padStart(2, '0')}`;
updateMonitoringList(todayStr);
</script>
@endpush