@extends('layouts.app')
@section('title', 'Edit Jabatan — ' . $jabatan->nama_jabatan)

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <a href="{{ route('admin.jabatan.index') }}" class="breadcrumb-item">Data Jabatan</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-item active">Edit</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Jabatan</h1>
        <p class="page-subtitle">{{ $jabatan->nama_jabatan }}</p>
    </div>
    <a href="{{ route('admin.jabatan.index') }}" class="btn btn-outline-secondary px-2 px-sm-3" title="Kembali">
        <i class="bi bi-arrow-left"></i><span class="d-none d-sm-inline ms-1">Kembali</span>
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-xl-8">
        <div class="card card-custom">
            <div class="card-header-custom">
                <h5 class="card-title-custom">
                    <i class="bi bi-pencil me-2"></i>Form Edit Jabatan
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.jabatan.update', $jabatan) }}">
                    @csrf @method('PUT')
                    @include('admin.jabatan._form')
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Perbarui
                        </button>
                        <a href="{{ route('admin.jabatan.index') }}" class="btn btn-outline-secondary">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection