@extends('layouts.app')
@section('title', 'Profil Saya')

@section('breadcrumb')
    <a href="{{ route('pegawai.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-item active">Profil Saya</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Profil Saya</h1>
        <p class="page-subtitle">Lihat dan perbarui data diri Anda</p>
    </div>
</div>

<div class="row g-4">
    <!-- Data Pegawai (readonly) -->
    <div class="col-12 col-xl-8">
        <div class="card card-custom mb-4">
            <div class="card-header-custom">
                <h5 class="card-title-custom"><i class="bi bi-person-circle me-2"></i>Data Kepegawaian</h5>
                <span class="badge bg-light text-muted border">Hanya dapat diubah Admin</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="detail-label">Nama Lengkap</label>
                        <div class="detail-value fw-bold">{{ $pegawai->nama_lengkap }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">NIP</label>
                        <div class="detail-value"><code>{{ $pegawai->nip }}</code></div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Bidang / UPTD</label>
                        <div class="detail-value">{{ $pegawai->bidang->nama_bidang ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Sub Bagian / Seksi</label>
                        <div class="detail-value">{{ $pegawai->sub_bagian ?? '-' }}</div>
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
                        <div class="detail-value"><span class="badge bg-primary">{{ $pegawai->jenis_pegawai }}</span></div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Tempat, Tanggal Lahir</label>
                        <div class="detail-value">{{ $pegawai->tempat_lahir }}, {{ $pegawai->tanggal_lahir ? \Carbon\Carbon::parse($pegawai->tanggal_lahir)->translatedFormat('d F Y'): '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Tanggal Masuk (TMT)</label>
                        <div class="detail-value">{{ $pegawai->tanggal_masuk ? \Carbon\Carbon::parse($pegawai->tanggal_masuk)->translatedFormat('d F Y') : '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Masa Kerja</label>
                        <div class="detail-value">{{ $pegawai->masa_kerja }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Sisa Cuti Tahunan</label>
                        <div class="detail-value">
                            <span class="badge {{ $pegawai->sisa_cuti_tahunan > 0 ? 'bg-success' : 'bg-danger' }} fs-6">
                                {{ $pegawai->sisa_cuti_tahunan }} hari
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Ganti Password -->
        <div class="card card-custom">
            <div class="card-header-custom">
                <h5 class="card-title-custom"><i class="bi bi-lock me-2"></i>Ganti Password</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('pegawai.profil.ganti-password') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Password Lama <span class="text-danger">*</span></label>
                            <input type="password" name="password_lama"
                                   class="form-control @error('password_lama') is-invalid @enderror" required>
                            @error('password_lama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Password Baru <span class="text-danger">*</span></label>
                            <input type="password" name="password_baru"
                                   class="form-control @error('password_baru') is-invalid @enderror"
                                   minlength="8" required>
                            @error('password_baru')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <input type="password" name="password_baru_confirmation" class="form-control" minlength="8" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning mt-3">
                        <i class="bi bi-key me-1"></i>Ganti Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Foto Profil Sidebar -->
    <div class="col-12 col-xl-4">
        <div class="card card-custom text-center">
            <div class="card-body py-4">
                <div class="avatar-xl mx-auto mb-3">
                    @if($pegawai->foto)
                        <img src="{{ asset('storage/'.$pegawai->foto) }}" class="avatar-img-xl" alt="foto">
                    @else
                        <span class="avatar-initial-xl">{{ substr($pegawai->nama_lengkap, 0, 1) }}</span>
                    @endif
                </div>
                <h5 class="fw-bold mb-1">{{ $pegawai->nama_lengkap }}</h5>
                <p class="text-muted small mb-2">{{ $pegawai->jabatan->nama_jabatan ?? '-' }}</p>
                <span class="badge bg-primary mb-1 d-inline-block">{{ $pegawai->bidang->nama_bidang ?? '-' }}</span>
                @if($pegawai->sub_bagian)
                    <span class="badge bg-secondary mb-1 d-inline-block">{{ $pegawai->sub_bagian }}</span>
                @endif
                <hr>
                <div class="text-start">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">NIP</span>
                        <code class="small">{{ $pegawai->nip }}</code>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Jenis</span>
                        <span class="small fw-semibold">{{ $pegawai->jenis_pegawai }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Sisa Cuti</span>
                        <span class="small fw-bold text-success">{{ $pegawai->sisa_cuti_tahunan }} hari</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted small">Login Terakhir</span>
                       <span class="small"> {{ Auth::user()->last_login_at ? \Carbon\Carbon::parse(Auth::user()->last_login_at)->translatedFormat('d M Y H:i') : '-' }} </span> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection