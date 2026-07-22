@extends('layouts.app')
@section('title', 'Tambah Hari Libur / Cuti Bersama')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <a href="{{ route('admin.hari-libur.index') }}" class="breadcrumb-item">Hari Libur</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-item active">Tambah</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Tambah Hari Libur / Cuti Bersama</h1>
        <p class="page-subtitle">Input tanggal libur resmi pemerintah</p>
    </div>
    <div>
        <a href="{{ route('admin.hari-libur.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
            <span class="d-none d-sm-inline ms-1">Kembali</span>
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card card-custom">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.hari-libur.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Libur <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal') }}" required>
                        @error('tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan / Nama Libur <span class="text-danger">*</span></label>
                        <input type="text" name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" value="{{ old('keterangan') }}" placeholder="Contoh: Hari Kemerdekaan RI, Cuti Bersama Idul Fitri" required>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_cuti_bersama" id="isCutiBersama" value="1" {{ old('is_cuti_bersama') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isCutiBersama">
                                Tandai sebagai Cuti Bersama Pemerintah
                            </label>
                        </div>
                        <div class="form-text">Centang jika ini merupakan Cuti Bersama resmi keputusan SKB 3 Menteri.</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.hari-libur.index') }}" class="btn btn-light">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Simpan Hari Libur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
