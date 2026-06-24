@extends('layouts.app')
@section('title', 'Tambah Jenis Cuti')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <a href="{{ route('admin.jenis-cuti.index') }}" class="breadcrumb-item">Jenis Cuti</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-item active">Tambah</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Tambah Jenis Cuti</h1>
        <p class="page-subtitle">Tambahkan jenis cuti baru sesuai regulasi ASN</p>
    </div>
    <a href="{{ route('admin.jenis-cuti.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-xl-8">
        <div class="card card-custom">
            <div class="card-header-custom">
                <h5 class="card-title-custom">
                    <i class="bi bi-calendar-check me-2"></i>Form Jenis Cuti
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.jenis-cuti.store') }}">
                    @csrf
                    @include('admin.jenis-cuti._form')
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Simpan
                        </button>
                        <a href="{{ route('admin.jenis-cuti.index') }}" class="btn btn-outline-secondary">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection