@extends('layouts.app')
@section('title', 'Data Pegawai')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-item active">Data Pegawai</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Data Pegawai</h1>
        <p class="page-subtitle">Kelola data seluruh pegawai DKP Provinsi Lampung</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.pegawai.export') }}" class="btn btn-outline-primary">
            <i class="bi bi-download me-1"></i>Export CSV / Excel
        </a>
        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-file-earmark-excel me-1"></i>Import CSV / Excel
        </button>
        <a href="{{ route('admin.pegawai.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i>Tambah Pegawai
        </a>
    </div>
</div>

<!-- Filter -->
<div class="card card-custom mb-4" style="overflow: visible;">
    <div class="card-body" style="overflow: visible;">
        <form method="GET">
            <div class="row g-3">
                <div class="col-12 col-md-3 position-relative">
                    <input type="text" name="search" id="searchInput" class="form-control" placeholder="Cari nama atau NIP..." value="{{ request('search') }}" autocomplete="off">
                    <div id="autocompleteSuggestions" class="list-group position-absolute w-100 shadow-lg d-none" style="z-index: 1050; max-height: 250px; overflow-y: auto; top: 100%; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px;"></div>
                </div>
                <div class="col-6 col-md-3">
                    <select name="bidang_id" class="form-select">
                        <option value="">Semua Bidang</option>
                        @foreach($bidang as $b)
                            <option value="{{ $b->id }}" {{ request('bidang_id') == $b->id ? 'selected' : '' }}>{{ $b->nama_bidang }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="jenis_pegawai" class="form-select">
                        <option value="">Semua Jenis</option>
                        <option value="PNS"    {{ request('jenis_pegawai') === 'PNS'    ? 'selected' : '' }}>PNS</option>
                        <option value="PPPK"   {{ request('jenis_pegawai') === 'PPPK'   ? 'selected' : '' }}>PPPK</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>
                <div class="col-auto d-flex gap-2">
                    <button class="btn btn-primary"><i class="bi bi-search"></i></button>
                    <a href="{{ route('admin.pegawai.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card card-custom">
    <div class="card-header-custom">
        <div>
            <h5 class="card-title-custom">Daftar Pegawai</h5>
            <p class="card-subtitle-custom">Total: {{ $pegawai->total() }} pegawai</p>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-head">
                    <tr>
                        <th>No</th>
                        <th>Pegawai</th>
                        <th>NIP</th>
                        <th>Bidang / Jabatan</th>
                        <th>Jenis</th>
                        <th>Sisa Cuti</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pegawai as $i => $p)
                        <tr>
                            <td class="text-muted">{{ $pegawai->firstItem() + $i }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-sm">
                                        @if($p->foto)
                                            <img src="{{ asset('storage/'.$p->foto) }}" alt="" class="avatar-img">
                                        @else
                                            <span class="avatar-initial">{{ substr($p->nama_lengkap, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $p->nama_lengkap }}</div>
                                        <small class="text-muted">{{ $p->jenis_kelamin_label }}</small>
                                    </div>
                                </div>
                            </td>
                            <td><code class="small">{{ $p->nip }}</code></td>
                            <td>
                                <div class="small fw-semibold">{{ $p->bidang->nama_bidang ?? '-' }}</div>
                                @if($p->sub_bagian)
                                    <div class="small text-muted" style="font-size: 0.75rem;">{{ $p->sub_bagian }}</div>
                                @endif
                                <div class="small text-muted">{{ $p->jabatan->nama_jabatan ?? '-' }}</div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $p->jenis_pegawai }}</span></td>
                            <td>
                                <span class="badge {{ $p->sisa_cuti_tahunan > 6 ? 'bg-success' : ($p->sisa_cuti_tahunan > 0 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                    {{ $p->sisa_cuti_tahunan }} hr
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $p->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $p->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.pegawai.show', $p) }}" class="btn btn-sm btn-info text-white" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.pegawai.edit', $p) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.pegawai.destroy', $p) }}">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" title="Hapus"
                                            onclick="konfirmasiHapus(this.form, '{{ $p->nama_lengkap }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-people fs-2 d-block mb-2"></i>Tidak ada data pegawai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($pegawai->hasPages())
        <div class="card-footer bg-transparent">{{ $pegawai->links() }}</div>
    @endif
</div>

<!-- Modal Import -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel"><i class="bi bi-file-earmark-excel me-2"></i>Import Pegawai dari CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.pegawai.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info py-2" style="font-size: 0.85rem;">
                        <i class="bi bi-info-circle me-1"></i><strong>Informasi Akun:</strong><br>
                        * ID/Username login pegawai baru adalah <strong>NIP</strong>.<br>
                        * Password default otomatis diatur ke <strong>4 digit pertama NIP</strong>.
                    </div>
                    
                    <div class="mb-3">
                        <label for="file_csv" class="form-label fw-semibold">Pilih Berkas CSV</label>
                        <input class="form-control" type="file" id="file_csv" name="file_csv" accept=".csv,.txt" required>
                        <div class="form-text small mt-1">Gunakan pemisah titik koma (;) atau koma (,). Ukuran berkas maks. 2MB.</div>
                    </div>

                    <div class="bg-light p-3 rounded mb-2">
                        <h6 class="fw-bold mb-2" style="font-size: 0.85rem;"><i class="bi bi-download me-1"></i>Unduh Panduan & Template</h6>
                        <p class="text-muted small mb-2">Untuk menghindari kegagalan import, silakan unduh berkas template di bawah ini sebagai acuan pengisian data Excel.</p>
                        <a href="{{ route('admin.pegawai.download-template') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-download me-1"></i>Unduh Template CSV
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-upload me-1"></i>Mulai Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Autocomplete Live Search
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const suggestionBox = document.getElementById('autocompleteSuggestions');
    const list = @json($autocompleteList ?? []);

    if (searchInput && suggestionBox && list.length > 0) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();
            suggestionBox.innerHTML = '';

            if (query.length < 2) {
                suggestionBox.classList.add('d-none');
                return;
            }

            const matches = list.filter(item => 
                item.nama.toLowerCase().includes(query) || 
                item.nip.includes(query)
            ).slice(0, 8); // Batasi maks 8 hasil

            if (matches.length === 0) {
                suggestionBox.classList.add('d-none');
                return;
            }

            matches.forEach(item => {
                const row = document.createElement('a');
                row.href = item.url;
                row.className = 'list-group-item list-group-item-action py-2';
                row.style.cursor = 'pointer';
                row.innerHTML = `
                    <div class="fw-semibold text-dark" style="font-size: 0.85rem;">${item.nama}</div>
                    <div class="text-muted small" style="font-size: 0.75rem;">NIP: ${item.nip}</div>
                `;
                suggestionBox.appendChild(row);
            });

            suggestionBox.classList.remove('d-none');
        });

        // Sembunyikan sugesti saat mengklik di luar area input
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !suggestionBox.contains(e.target)) {
                suggestionBox.classList.add('d-none');
            }
        });
    }
});

function konfirmasiHapus(form, nama) {
    Swal.fire({
        title: 'Hapus Pegawai?',
        text: `Data pegawai "${nama}" beserta akun login akan dihapus.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#ef4444',
    }).then(r => { if (r.isConfirmed) form.submit(); });
}
</script>
@endpush