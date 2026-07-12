@extends('layouts.app')
@section('title', 'Kalender Cuti Bersama')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-item active">Kalender Cuti</span>
@endsection

@push('styles')
<style>
    .fc {
        font-family: 'Poppins', sans-serif !important;
    }
    .fc-header-title h2 {
        font-size: 1.25rem !important;
        font-weight: 600 !important;
        color: var(--dark);
    }
    .fc-button-primary {
        background-color: var(--primary) !important;
        border-color: var(--primary) !important;
        text-transform: capitalize !important;
        font-weight: 500 !important;
    }
    .fc-button-primary:hover, .fc-button-primary:focus {
        background-color: #0b5ed7 !important;
        border-color: #0a58ca !important;
    }
    .fc-button-active {
        background-color: #0a58ca !important;
        border-color: #0a58ca !important;
    }
    .fc-event {
        cursor: pointer;
        padding: 2px 4px;
        border-radius: 4px;
        font-size: 0.8rem !important;
        font-weight: 500;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .fc-daygrid-day-number {
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--dark);
        text-decoration: none !important;
    }
    .calendar-legend-item {
        display: inline-flex;
        align-items: center;
        margin-right: 15px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 3px;
        margin-right: 6px;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Kalender Cuti Bersama</h1>
        <p class="page-subtitle">Jadwal cuti seluruh pegawai Dinas Kelautan dan Perikanan Provinsi Lampung</p>
    </div>
</div>

<div class="card card-custom mb-4">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
        <h5 class="card-title-custom"><i class="bi bi-calendar3 me-2"></i>Kalender Jadwal Cuti</h5>
        <div class="text-end">
            <span class="badge bg-light text-dark py-2 px-3 border"><i class="bi bi-info-circle me-1"></i>Klik event untuk detail</span>
        </div>
    </div>
    <div class="card-body">
        <!-- Legend warna -->
        <div class="mb-4 p-3 bg-light rounded d-flex flex-wrap align-items-center">
            <span class="fw-semibold me-3 small text-muted">Legenda Warna:</span>
            <div class="calendar-legend-item">
                <div class="legend-color" style="background-color: #0d6efd;"></div>
                <span>Cuti Tahunan</span>
            </div>
            <div class="calendar-legend-item">
                <div class="legend-color" style="background-color: #dc3545;"></div>
                <span>Cuti Sakit</span>
            </div>
            <div class="calendar-legend-item">
                <div class="legend-color" style="background-color: #ec4899;"></div>
                <span>Cuti Melahirkan</span>
            </div>
            <div class="calendar-legend-item">
                <div class="legend-color" style="background-color: #6f42c1;"></div>
                <span>Cuti Besar</span>
            </div>
            <div class="calendar-legend-item">
                <div class="legend-color" style="background-color: #fd7e14;"></div>
                <span>Cuti Alasan Penting</span>
            </div>
            <div class="calendar-legend-item">
                <div class="legend-color" style="background-color: #14b8a6;"></div>
                <span>Cuti Lainnya</span>
            </div>
        </div>

        <!-- Calendar Container -->
        <div id="calendar" style="min-height: 600px;"></div>
    </div>
</div>
@endsection

@push('scripts')
<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/locales/id.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listMonth'
        },
        events: '{{ route('calendar.events') }}',
        eventClick: function(info) {
            const props = info.event.extendedProps;
            
            Swal.fire({
                title: '<span style="font-weight: 600; color: var(--primary);">Detail Pengajuan Cuti</span>',
                html: `
                    <div class="text-start mt-2" style="font-family: 'Poppins', sans-serif;">
                        <div class="text-center mb-3">
                            <span class="badge py-2 px-3 text-white fs-6" style="background-color: ${info.event.backgroundColor}">
                                <i class="bi bi-calendar2-check me-1"></i>${props.jenis_cuti}
                            </span>
                        </div>
                        <table class="table table-bordered table-striped table-sm" style="font-size: 0.9rem;">
                            <tbody>
                                <tr>
                                    <th style="width: 35%;" class="bg-light">Nama Pegawai</th>
                                    <td><strong>${props.nama}</strong></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">NIP</th>
                                    <td>${props.nip}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Bidang / Unit</th>
                                    <td>${props.bidang}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Tanggal Mulai</th>
                                    <td>${props.tanggal_mulai}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Tanggal Selesai</th>
                                    <td>${props.tanggal_selesai}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Durasi Cuti</th>
                                    <td><strong class="text-primary">${props.jumlah_hari}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: '<i class="bi bi-check-lg me-1"></i>Tutup',
                confirmButtonColor: '#0d6efd',
                customClass: {
                    popup: 'border-radius-15'
                }
            });
        }
    });
    
    calendar.render();
});
</script>
@endpush
