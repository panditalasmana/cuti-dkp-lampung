@extends('layouts.app')
@section('title', 'Activity Log')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-item active">Activity Log</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Activity Log</h1>
        <p class="page-subtitle">Rekam jejak seluruh aktivitas pengguna dalam sistem</p>
    </div>
</div>

<!-- Filter -->
<div class="card card-custom mb-4">
    <div class="card-body">
        <form method="GET">
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <select name="user_id" class="form-select">
                        <option value="">Semua Pengguna</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ ($filters['user_id'] ?? '') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="module" class="form-select">
                        <option value="">Semua Modul</option>
                        @foreach($modules as $m)
                            <option value="{{ $m }}" {{ ($filters['module'] ?? '') === $m ? 'selected' : '' }}>
                                {{ ucfirst($m) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <input type="date" name="tanggal_dari" class="form-control" value="{{ $filters['tanggal_dari'] ?? '' }}" placeholder="Dari tanggal">
                </div>
                <div class="col-6 col-md-2">
                    <input type="date" name="tanggal_sampai" class="form-control" value="{{ $filters['tanggal_sampai'] ?? '' }}" placeholder="Sampai tanggal">
                </div>
                <div class="col-auto d-flex gap-2">
                    <button class="btn btn-primary"><i class="bi bi-search"></i></button>
                    <a href="{{ route('admin.activity-log.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-head">
                    <tr>
                        <th>Waktu</th>
                        <th>Pengguna</th>
                        <th>Modul</th>
                        <th>Aksi</th>
                        <th>Deskripsi</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="small text-muted text-nowrap">{{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d M Y H:i:s') }}</td>
                            <td>
                                <div class="small fw-semibold">{{ $log->user->name ?? 'Sistem' }}</div>
                                <small class="text-muted">{{ $log->user->nip ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border small">{{ $log->module }}</span>
                            </td>
                            <td>
                                @php
                                    $actionColors = [
                                        'login'         => 'bg-success',
                                        'logout'        => 'bg-secondary',
                                        'create'        => 'bg-primary',
                                        'update'        => 'bg-warning text-dark',
                                        'delete'        => 'bg-danger',
                                        'upload'        => 'bg-info text-dark',
                                        'status_change' => 'bg-purple',
                                    ];
                                    $color = $actionColors[$log->action] ?? 'bg-secondary';
                                @endphp
                                <span class="badge {{ $color }} small">{{ $log->action }}</span>
                            </td>
                            <td class="small">{{ Str::limit($log->description, 80) }}</td>
                            <td class="small text-muted">{{ $log->ip_address ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="bi bi-clock-history fs-2 d-block mb-2"></i>
                                Tidak ada log aktivitas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($logs->hasPages())
        <div class="card-footer bg-transparent">{{ $logs->links() }}</div>
    @endif
</div>
@endsection