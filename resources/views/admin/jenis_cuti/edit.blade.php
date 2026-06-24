@extends('layouts.app')
@section('title', 'Edit Jenis Cuti — ' . $jenisCuti->nama_cuti)

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <a href="{{ route('admin.jenis-cuti.index') }}" class="breadcrumb-item">Jenis Cuti</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-item active">Edit</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Jenis Cuti</h1>
        <p class="page-subtitle">{{ $jenisCuti->nama_cuti }}</p>
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
                    <i class="bi bi-pencil me-2"></i>Form Edit Jenis Cuti
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.jenis-cuti.update', $jenisCuti) }}">
                    @csrf @method('PUT')
                    @include('admin.jenis-cuti._form')
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Perbarui
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