@extends('layouts.app')
@section('title', 'Edit Pejabat Penandatangan')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <a href="{{ route('admin.penandatangan.index') }}" class="breadcrumb-item">Pejabat Penandatangan</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-item active">Edit</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Pejabat Penandatangan</h1>
        <p class="page-subtitle">Ubah data nama, NIP, dan jabatan pejabat penandatangan</p>
    </div>
    <div>
        <a href="{{ route('admin.penandatangan.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card card-custom">
            <div class="card-body p-4">
                <form action="{{ route('admin.penandatangan.update', $penandatangan) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label required fw-semibold">Kategori Pejabat</label>
                        <select name="kategori" class="form-select @error('kategori') is-invalid @enderror" required>
                            <option value="pejabat_wenang" {{ old('kategori', $penandatangan->kategori) === 'pejabat_wenang' ? 'selected' : '' }}>Pejabat Berwenang (Kepala Dinas / Sekda / BKD / Gubernur)</option>
                            <option value="atasan_langsung" {{ old('kategori', $penandatangan->kategori) === 'atasan_langsung' ? 'selected' : '' }}>Atasan Langsung (Sekretaris / Kabid / Kepala UPTD)</option>
                            <option value="eselon_4" {{ old('kategori', $penandatangan->kategori) === 'eselon_4' ? 'selected' : '' }}>Pejabat Pengawas (Kasubbag / Kasie)</option>
                        </select>
                        @error('kategori')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label required fw-semibold">Nama Lengkap & Gelar</label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $penandatangan->nama) }}" required>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label required fw-semibold">NIP (Nomor Induk Pegawai)</label>
                        <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip', $penandatangan->nip) }}" maxlength="20" required>
                        @error('nip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label required fw-semibold">Nama Jabatan Kedinasan</label>
                        <input type="text" name="jabatan" class="form-control @error('jabatan') is-invalid @enderror" value="{{ old('jabatan', $penandatangan->jabatan) }}" required>
                        @error('jabatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pangkat / Golongan Ruang (Opsional)</label>
                        <input type="text" name="pangkat_golongan" class="form-control @error('pangkat_golongan') is-invalid @enderror" value="{{ old('pangkat_golongan', $penandatangan->pangkat_golongan) }}">
                        @error('pangkat_golongan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_default" value="1" id="is_default" {{ old('is_default', $penandatangan->is_default) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_default">Jadikan Default (Pilihan Utama Kategori Ini)</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $penandatangan->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">Status Aktif</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.penandatangan.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Perbarui Data Pejabat</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
