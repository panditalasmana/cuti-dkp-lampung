anti@extends('layouts.app')
@section('title', 'Tambah Pegawai')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <a href="{{ route('admin.pegawai.index') }}" class="breadcrumb-item">Data Pegawai</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-item active">Tambah</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Tambah Pegawai</h1>
        <p class="page-subtitle">Daftarkan pegawai baru beserta akun login</p>
    </div>
    <a href="{{ route('admin.pegawai.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="card card-custom">
    <div class="card-header-custom">
        <h5 class="card-title-custom"><i class="bi bi-person-plus me-2"></i>Form Data Pegawai</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.pegawai.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.pegawai._form')
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan Pegawai
                </button>
                <a href="{{ route('admin.pegawai.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection