@extends('layouts.app')
@section('title', 'Master Hari Libur Nasional & Cuti Bersama')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-item active">Hari Libur Nasional</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Hari Libur Nasional & Cuti Bersama</h1>
        <p class="page-subtitle">Kelola kalender libur resmi pemerintah agar durasi cuti terpotong secara otomatis & akurat</p>
    </div>
    <div>
        <a href="{{ route('admin.hari-libur.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Hari Libur
        </a>
    </div>
</div>

<!-- Filter Tahun -->
<div class="card card-custom mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.hari-libur.index') }}">
            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <label class="col-form-label fw-semibold">Tahun Kalender:</label>
                </div>
                <div class="col-auto">
                    <select name="tahun" class="form-select" onchange="this.form.submit()">
                        @foreach($tahunList as $thn)
                            <option value="{{ $thn }}" {{ $tahun == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-head">
                    <tr>
                        <th width="5%">No</th>
                        <th>Tanggal</th>
                        <th>Keterangan Hari Libur</th>
                        <th width="20%">Kategori</th>
                        <th width="15%" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hariLibur as $index => $item)
                        <tr>
                            <td class="text-muted">{{ $hariLibur->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-semibold text-primary">
                                    <i class="bi bi-calendar-event me-1"></i>{{ $item->tanggal->isoFormat('D MMMM Y') }}
                                </div>
                                <small class="text-muted">{{ $item->tanggal->isoFormat('dddd') }}</small>
                            </td>
                            <td class="fw-medium">{{ $item->keterangan }}</td>
                            <td>
                                @if($item->is_cuti_bersama)
                                    <span class="badge bg-warning text-dark"><i class="bi bi-person-workspace me-1"></i>Cuti Bersama</span>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-flag-fill me-1"></i>Libur Nasional</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.hari-libur.edit', $item) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger" onclick="hapusHariLibur('{{ $item->id }}', '{{ $item->keterangan }}')" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <form id="formHapus-{{ $item->id }}" method="POST" action="{{ route('admin.hari-libur.destroy', $item) }}">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                                Belum ada data hari libur pada tahun {{ $tahun }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($hariLibur->hasPages())
        <div class="card-footer bg-transparent">
            {{ $hariLibur->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function hapusHariLibur(id, nama) {
    Swal.fire({
        title: 'Hapus Hari Libur?',
        text: `Hari Libur "${nama}" akan dihapus dari sistem.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formHapus-' + id).submit();
        }
    });
}
</script>
@endpush
