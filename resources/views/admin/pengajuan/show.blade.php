@extends('layouts.app')
@section('title', 'Detail Pengajuan — ' . $pengajuan->tanggal_pengajuan->format('d/m/Y'))

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <a href="{{ route('admin.pengajuan.index') }}" class="breadcrumb-item">Pengajuan Cuti</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-item active">Detail</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Detail Pengajuan</h1>
        <p class="page-subtitle">Tanggal Pengajuan: {{ $pengajuan->tanggal_pengajuan->isoFormat('D MMMM Y') }}</p>
    </div>
    <div class="d-flex gap-2 align-items-center flex-wrap">
        <a href="{{ route('admin.pengajuan.preview-pdf', $pengajuan) }}" class="btn btn-outline-danger" target="_blank" title="Preview PDF">
            <i class="bi bi-file-pdf me-1"></i><span class="d-none d-sm-inline">Preview PDF</span><span class="d-inline d-sm-none">Preview</span>
        </a>
        <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-outline-secondary px-2 px-sm-3" title="Kembali">
            <i class="bi bi-arrow-left"></i><span class="d-none d-sm-inline ms-1">Kembali</span>
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Detail Info -->
    <div class="col-12 col-xl-8">
        <div class="card card-custom mb-4">
            <div class="card-header-custom">
                <h5 class="card-title-custom">Informasi Pengajuan</h5>
                @include('components.status-badge', ['status' => $pengajuan->status])
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="detail-label">Tanggal Pengajuan</label>
                        <div class="detail-value">{{ $pengajuan->tanggal_pengajuan->isoFormat('D MMMM Y') }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Jenis Cuti</label>
                        <div class="detail-value fw-semibold">{{ $pengajuan->jenisCuti->nama_cuti }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Tanggal Mulai</label>
                        <div class="detail-value">{{ $pengajuan->tanggal_mulai->isoFormat('dddd, D MMMM Y') }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Tanggal Selesai</label>
                        <div class="detail-value">{{ $pengajuan->tanggal_selesai->isoFormat('dddd, D MMMM Y') }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Lama Cuti</label>
                        <div class="detail-value">
                            <span class="badge bg-primary fs-6">{{ $pengajuan->lama_cuti_display }}</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Tanggal Pengajuan</label>
                        <div class="detail-value">{{ $pengajuan->tanggal_pengajuan->isoFormat('D MMMM Y, HH:mm') }}</div>
                    </div>
                    <div class="col-12">
                        <label class="detail-label">Alasan Cuti</label>
                        <div class="detail-value">{{ $pengajuan->alasan_cuti }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Alamat Selama Cuti</label>
                        <div class="detail-value">{{ $pengajuan->alamat_selama_cuti }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">No. Telepon Selama Cuti</label>
                        <div class="detail-value">{{ $pengajuan->no_telp_selama_cuti ?? '-' }}</div>
                    </div>
                    @if($pengajuan->eselon_3)
                        <div class="col-12">
                            <label class="detail-label">Paraf Eselon 4</label>
                            <div class="detail-value">
                                @if(str_contains($pengajuan->eselon_3, '|'))
                                    @php
                                        $eselonParts = explode('|', $pengajuan->eselon_3);
                                    @endphp
                                    {{ $eselonParts[0] }} ({{ $eselonParts[2] }} - NIP. {{ $eselonParts[1] }})
                                @else
                                    {{ $pengajuan->eselon_3 }}
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                @if($pengajuan->catatan_admin)
                    <div class="alert alert-info mt-3">
                        <strong><i class="bi bi-info-circle me-1"></i>Catatan Admin:</strong><br>
                        {{ $pengajuan->catatan_admin }}
                    </div>
                @endif

                @if($pengajuan->tanggal_verifikasi)
                    <div class="mt-3 p-3 bg-light rounded">
                        <small class="text-muted">
                            Diverifikasi oleh <strong>{{ $pengajuan->verifikator->name ?? '-' }}</strong>
                            pada {{ $pengajuan->tanggal_verifikasi->isoFormat('D MMMM Y, HH:mm') }}
                        </small>
                    </div>
                @endif
            </div>
        </div>

        <!-- Data Pegawai -->
        <div class="card card-custom mb-4">
            <div class="card-header-custom">
                <h5 class="card-title-custom">Data Pegawai</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="detail-label">Nama Lengkap</label>
                        <div class="detail-value fw-semibold">{{ $pengajuan->pegawai->nama_lengkap }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">NIP</label>
                        <div class="detail-value"><code>{{ $pengajuan->pegawai->nip }}</code></div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Jabatan</label>
                        <div class="detail-value">{{ $pengajuan->pegawai->jabatan->nama_jabatan ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Bidang / UPTD</label>
                        <div class="detail-value">{{ $pengajuan->pegawai->bidang->nama_bidang ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Sub Bagian / Seksi</label>
                        <div class="detail-value">{{ $pengajuan->pegawai->sub_bagian ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Pangkat/Golongan</label>
                        <div class="detail-value">{{ $pengajuan->pegawai->pangkat ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="detail-label">Sisa Cuti Tahunan</label>
                        <div class="detail-value">
                            <span class="badge {{ $pengajuan->pegawai->sisa_cuti_tahunan > 0 ? 'bg-success' : 'bg-danger' }} fs-6">
                                {{ $pengajuan->pegawai->sisa_cuti_tahunan }} hari
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scan Dokumen -->
        @if($pengajuan->scanSurat)
            <div class="card card-custom">
                <div class="card-header-custom">
                    <h5 class="card-title-custom">Scan Surat Ditandatangani</h5>
                    <span class="badge bg-success">Sudah Diupload</span>
                </div>
                <div class="card-body">
                    <div class="dokumen-item d-flex align-items-center gap-3 p-3 bg-light rounded">
                        <div class="dokumen-icon">
                            @if($pengajuan->scanSurat->isImage())
                                <i class="bi bi-image-fill text-info fs-2"></i>
                            @else
                                <i class="bi bi-file-pdf-fill text-danger fs-2"></i>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $pengajuan->scanSurat->nama_file }}</div>
                            <small class="text-muted">{{ $pengajuan->scanSurat->ukuran_format }} — {{ $pengajuan->scanSurat->created_at->isoFormat('D MMM Y, HH:mm') }}</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="previewDokumen('{{ $pengajuan->scanSurat->file_url }}', '{{ $pengajuan->scanSurat->nama_file }}', '{{ $pengajuan->scanSurat->mime_type }}')">
                                <i class="bi bi-eye"></i> Lihat
                            </button>
                            <a href="{{ $pengajuan->scanSurat->file_url }}" class="btn btn-sm btn-primary" target="_blank" download>
                                <i class="bi bi-download"></i> Unduh
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Actions Panel -->
    <div class="col-12 col-xl-4">

        {{-- Verifikasi (hanya jika masih menunggu) --}}
        @if($pengajuan->status === 'menunggu')
            <div class="card card-custom mb-4 border-warning">
                <div class="card-header-custom" style="background: linear-gradient(135deg, #fef3c7, #fde68a);">
                    <h5 class="card-title-custom text-warning-emphasis">
                        <i class="bi bi-shield-check me-1"></i>Verifikasi Pengajuan
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Pilih tindakan untuk pengajuan ini setelah memeriksa formulir fisik yang telah ditandatangani Kepala Bidang.</p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan Admin (opsional)</label>
                        <textarea class="form-control" id="catatanAdmin" rows="3" placeholder="Tuliskan catatan jika ada..."></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-success" onclick="konfirmasiVerifikasi('disetujui')">
                            <i class="bi bi-check-circle me-1"></i>Setujui Pengajuan
                        </button>
                        <button class="btn btn-danger" onclick="konfirmasiVerifikasi('ditolak')">
                            <i class="bi bi-x-circle me-1"></i>Tolak Pengajuan
                        </button>
                    </div>

                    <form id="formVerifikasi" method="POST" action="{{ route('admin.pengajuan.verifikasi', $pengajuan) }}">
                        @csrf
                        <input type="hidden" name="status" id="inputStatus">
                        <input type="hidden" name="catatan_admin" id="inputCatatan">
                    </form>
                </div>
            </div>

            {{-- Upload Scan --}}
            <div class="card card-custom mb-4">
                <div class="card-header-custom">
                    <h5 class="card-title-custom">
                        <i class="bi bi-cloud-upload me-1"></i>Upload Scan Surat
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Upload scan/foto formulir cuti yang sudah ditandatangani Kepala Bidang.</p>
                    <form method="POST" action="{{ route('admin.pengajuan.upload-scan', $pengajuan) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">File Scan <span class="text-danger">*</span></label>
                            
                            <!-- Tombol Kamera Scan Baru -->
                            <button type="button" class="btn btn-outline-info btn-sm w-100 mb-2" id="btnBukaKamera">
                                <i class="bi bi-camera me-1"></i>Ambil dari Kamera
                            </button>
                            
                            <!-- Thumbnail Preview dari Tangkapan Kamera -->
                            <div id="previewKameraContainer" class="d-none mb-2 text-center p-2 border rounded bg-light position-relative">
                                <img id="previewKameraImg" style="max-height: 120px; border-radius: 6px;" alt="preview scan">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" id="btnHapusPreviewKamera" title="Hapus foto">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>

                            <input type="file" name="file_scan" id="fileScanInput" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                            <div class="form-text">Format: PDF, JPG, PNG. Maks. 5MB.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <input type="text" name="keterangan" class="form-control" placeholder="Keterangan tambahan...">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-upload me-1"></i>Upload Scan
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <!-- Info Dasar Hukum -->
        <div class="card card-custom">
            <div class="card-header-custom">
                <h5 class="card-title-custom"><i class="bi bi-book me-1"></i>Dasar Hukum</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-1"><strong>{{ $pengajuan->jenisCuti->nama_cuti }}</strong></p>
                <p class="text-muted small">{{ $pengajuan->jenisCuti->dasar_hukum ?? 'Tidak ada keterangan dasar hukum.' }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kamera Scan -->
<div class="modal fade" id="modalKamera" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-camera me-2"></i>Kamera Scan Bukti Cuti</h5>
                <button type="button" class="btn-close" id="btnTutupModalKamera" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Dropdown Pilih Kamera (jika ada lebih dari 1) -->
                <div class="mb-3 d-none" id="selectKameraContainer">
                    <label class="form-label small fw-semibold">Pilih Kamera Devices</label>
                    <select class="form-select form-select-sm" id="selectKameraDevice"></select>
                </div>

                <!-- Video Stream Area -->
                <div class="position-relative border rounded bg-dark overflow-hidden d-flex align-items-center justify-content-center" style="min-height: 350px;">
                    <!-- Video Live Feed -->
                    <video id="videoFeed" class="w-100 h-auto" autoplay playsinline style="object-fit: cover;"></video>
                    
                    <!-- Captured Canvas (tersembunyi) -->
                    <canvas id="canvasCapture" class="d-none"></canvas>

                    <!-- Preview Hasil Foto -->
                    <img id="capturePreview" class="w-100 h-auto d-none" style="object-fit: cover;" alt="preview capture">

                    <!-- Loading overlay -->
                    <div id="cameraLoading" class="position-absolute d-flex flex-column align-items-center justify-content-center text-white bg-dark bg-opacity-75 w-100 h-100 d-none">
                        <div class="spinner-border text-primary mb-2" role="status"></div>
                        <span>Mengaktifkan kamera...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCancelKamera">Batal</button>
                <div class="d-flex gap-2">
                    <!-- Tombol Aksi Kamera -->
                    <button type="button" class="btn btn-warning d-none" id="btnFotoUlang"><i class="bi bi-arrow-counterclockwise me-1"></i>Foto Ulang</button>
                    <button type="button" class="btn btn-success" id="btnAmbilFoto"><i class="bi bi-camera-fill me-1"></i>Ambil Foto</button>
                    <button type="button" class="btn btn-primary d-none" id="btnGunakanFoto"><i class="bi bi-check-circle me-1"></i>Gunakan Foto</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function konfirmasiVerifikasi(status) {
    const catatan = document.getElementById('catatanAdmin').value;
    const label   = status === 'disetujui' ? 'menyetujui' : 'menolak';
    const color   = status === 'disetujui' ? 'success' : 'error';

    Swal.fire({
        title: `Konfirmasi ${status === 'disetujui' ? 'Persetujuan' : 'Penolakan'}`,
        text: `Anda yakin ingin ${label} pengajuan ini?`,
        icon: color,
        showCancelButton: true,
        confirmButtonText: 'Ya, Lanjutkan',
        cancelButtonText: 'Batal',
        confirmButtonColor: status === 'disetujui' ? '#10b981' : '#ef4444',
    }).then(result => {
        if (result.isConfirmed) {
            document.getElementById('inputStatus').value  = status;
            document.getElementById('inputCatatan').value = catatan;
            document.getElementById('formVerifikasi').submit();
        }
    });
}

// Logika Kamera Scan
let mediaStream = null;
let currentDeviceId = null;
const modalKamera = new bootstrap.Modal(document.getElementById('modalKamera'));

const videoFeed = document.getElementById('videoFeed');
const canvasCapture = document.getElementById('canvasCapture');
const capturePreview = document.getElementById('capturePreview');
const selectKameraDevice = document.getElementById('selectKameraDevice');
const selectKameraContainer = document.getElementById('selectKameraContainer');
const cameraLoading = document.getElementById('cameraLoading');

const btnBukaKamera = document.getElementById('btnBukaKamera');
const btnTutupModalKamera = document.getElementById('btnTutupModalKamera');
const btnCancelKamera = document.getElementById('btnCancelKamera');
const btnAmbilFoto = document.getElementById('btnAmbilFoto');
const btnFotoUlang = document.getElementById('btnFotoUlang');
const btnGunakanFoto = document.getElementById('btnGunakanFoto');

const fileScanInput = document.getElementById('fileScanInput');
const previewKameraContainer = document.getElementById('previewKameraContainer');
const previewKameraImg = document.getElementById('previewKameraImg');
const btnHapusPreviewKamera = document.getElementById('btnHapusPreviewKamera');

let capturedBlob = null;

// Membuka modal dan menginisiasi kamera
btnBukaKamera.addEventListener('click', async () => {
    modalKamera.show();
    resetStateKamera();
    await inisialisasiKamera();
});

// Tutup kamera ketika modal ditutup
document.getElementById('modalKamera').addEventListener('hidden.bs.modal', stopKameraStream);

async function inisialisasiKamera(deviceId = null) {
    cameraLoading.classList.remove('d-none');
    stopKameraStream();

    const constraints = {
        video: deviceId ? { deviceId: { exact: deviceId } } : { facingMode: 'environment' }
    };

    try {
        mediaStream = await navigator.mediaDevices.getUserMedia(constraints);
        videoFeed.srcObject = mediaStream;
        
        // Memuat daftar kamera pendukung
        const devices = await navigator.mediaDevices.enumerateDevices();
        const videoDevices = devices.filter(device => device.kind === 'videoinput');
        
        selectKameraDevice.innerHTML = '';
        if (videoDevices.length > 1) {
            selectKameraContainer.classList.remove('d-none');
            videoDevices.forEach(device => {
                const option = document.createElement('option');
                option.value = device.deviceId;
                option.text = device.label || `Kamera ${selectKameraDevice.length + 1}`;
                if (deviceId && device.deviceId === deviceId) {
                    option.selected = true;
                } else if (!deviceId && mediaStream.getVideoTracks()[0].getSettings().deviceId === device.deviceId) {
                    option.selected = true;
                }
                selectKameraDevice.appendChild(option);
            });
        } else {
            selectKameraContainer.classList.add('d-none');
        }
    } catch (error) {
        console.error("Gagal membuka kamera:", error);
        Swal.fire({
            icon: 'error',
            title: 'Gagal Akses Kamera',
            text: 'Pastikan Anda telah memberikan izin akses kamera ke browser ini.'
        });
        modalKamera.hide();
    } finally {
        cameraLoading.classList.add('d-none');
    }
}

// Berpindah kamera jika dipilih dari dropdown
selectKameraDevice.addEventListener('change', (e) => {
    inisialisasiKamera(e.target.value);
});

function stopKameraStream() {
    if (mediaStream) {
        mediaStream.getTracks().forEach(track => track.stop());
        mediaStream = null;
    }
}

function resetStateKamera() {
    videoFeed.classList.remove('d-none');
    capturePreview.classList.add('d-none');
    btnAmbilFoto.classList.remove('d-none');
    btnFotoUlang.classList.add('d-none');
    btnGunakanFoto.classList.add('d-none');
    capturedBlob = null;
}

// Tombol Ambil Foto
btnAmbilFoto.addEventListener('click', () => {
    const videoWidth = videoFeed.videoWidth || 640;
    const videoHeight = videoFeed.videoHeight || 480;

    canvasCapture.width = videoWidth;
    canvasCapture.height = videoHeight;

    const ctx = canvasCapture.getContext('2d');
    // Mirror handling jika kamera menghadap pengguna
    const settings = mediaStream.getVideoTracks()[0].getSettings();
    if (settings.facingMode === 'user') {
        ctx.translate(videoWidth, 0);
        ctx.scale(-1, 1);
    }
    ctx.drawImage(videoFeed, 0, 0, videoWidth, videoHeight);

    canvasCapture.toBlob((blob) => {
        capturedBlob = blob;
        const imgUrl = URL.createObjectURL(blob);
        capturePreview.src = imgUrl;
        
        videoFeed.classList.add('d-none');
        capturePreview.classList.remove('d-none');

        btnAmbilFoto.classList.add('d-none');
        btnFotoUlang.classList.remove('d-none');
        btnGunakanFoto.classList.remove('d-none');
    }, 'image/jpeg', 0.9);
});

// Tombol Foto Ulang
btnFotoUlang.addEventListener('click', () => {
    resetStateKamera();
});

// Tombol Gunakan Foto
btnGunakanFoto.addEventListener('click', () => {
    if (!capturedBlob) return;

    // Convert blob to File object
    const file = new File([capturedBlob], "scan_kamera.jpg", { type: "image/jpeg" });
    
    // Injeksi file ke input file HTML menggunakan DataTransfer API
    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(file);
    fileScanInput.files = dataTransfer.files;

    // Tampilkan thumbnail preview di form utama
    previewKameraImg.src = URL.createObjectURL(capturedBlob);
    previewKameraContainer.classList.remove('d-none');
    
    // Hapus tanda required jika sebelumnya ada
    fileScanInput.required = false;

    modalKamera.hide();
    stopKameraStream();
});

// Tombol Hapus Preview di form utama
btnHapusPreviewKamera.addEventListener('click', () => {
    fileScanInput.value = '';
    fileScanInput.required = true;
    previewKameraContainer.classList.add('d-none');
    previewKameraImg.src = '';
    capturedBlob = null;
});

function previewDokumen(url, filename, mimeType) {
    const modal = new bootstrap.Modal(document.getElementById('modalPreviewDokumen'));
    document.getElementById('previewModalTitle').innerText = filename;
    document.getElementById('previewDownloadBtn').href = url;
    
    const imgEl = document.getElementById('previewImage');
    const iframeEl = document.getElementById('previewIframe');
    
    if (mimeType.includes('image') || url.match(/\.(jpg|jpeg|png|webp|gif)$/i)) {
        imgEl.src = url;
        imgEl.classList.remove('d-none');
        iframeEl.classList.add('d-none');
        iframeEl.src = '';
    } else {
        iframeEl.src = url;
        iframeEl.classList.remove('d-none');
        imgEl.classList.add('d-none');
        imgEl.src = '';
    }
    
    modal.show();
}
</script>

<!-- Modal Preview Dokumen Website -->
<div class="modal fade" id="modalPreviewDokumen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fs-6 fw-semibold" id="previewModalTitle"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Pratinjau Berkas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3 bg-dark-subtle" style="min-height: 450px; display: flex; align-items: center; justify-content: center;">
                <img id="previewImage" src="" class="img-fluid rounded shadow d-none" style="max-height: 75vh;" alt="preview berkas">
                <iframe id="previewIframe" src="" class="w-100 rounded border-0 d-none" style="height: 75vh;"></iframe>
            </div>
            <div class="modal-footer bg-light">
                <a id="previewDownloadBtn" href="" class="btn btn-primary" download target="_blank">
                    <i class="bi bi-download me-1"></i>Unduh File
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endpush