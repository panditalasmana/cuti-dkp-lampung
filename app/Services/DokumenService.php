<?php

namespace App\Services;

use App\Models\Dokumen;
use App\Models\PengajuanCuti;
use App\Repositories\DokumenRepository;
use App\Repositories\PegawaiRepository;
use App\Repositories\PengajuanCutiRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class DokumenService
{
    const ALLOWED_MIMES   = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
    const MAX_SIZE_BYTES  = 5 * 1024 * 1024; // 5MB

    public function __construct(
        private DokumenRepository $repo,
        private PengajuanCutiRepository $pengajuanRepo,
        private PegawaiRepository $pegawaiRepo,
        private ActivityLogService $logService,
    ) {}

    public function uploadScanSurat(PengajuanCuti $pengajuan, UploadedFile $file, ?string $keterangan = null): Dokumen
    {
        return DB::transaction(function () use ($pengajuan, $file, $keterangan) {
            // Validasi file
            $this->validasiFile($file);

            if ($pengajuan->status !== PengajuanCuti::STATUS_MENUNGGU) {
                throw ValidationException::withMessages(['file' => 'Upload scan hanya dapat dilakukan pada pengajuan berstatus Menunggu.']);
            }

            // Hapus scan lama jika ada
            $scanLama = $this->repo->getScanByPengajuan($pengajuan->id);
            if ($scanLama) {
                Storage::disk('public')->delete($scanLama->path_file);
                $this->repo->delete($scanLama);
            }

            // Simpan file baru
            $folder   = "pengajuan/{$pengajuan->id}/scan";
            $path     = $file->store($folder, 'public');
            $namaFile = $file->getClientOriginalName();

            $dokumen = $this->repo->create([
                'pengajuan_cuti_id' => $pengajuan->id,
                'uploaded_by'       => Auth::id(),
                'jenis_dokumen'     => 'scan_surat_ditandatangani',
                'nama_file'         => $namaFile,
                'path_file'         => $path,
                'mime_type'         => $file->getMimeType(),
                'ukuran_file'       => $file->getSize(),
                'keterangan'        => $keterangan,
            ]);

            // Jika jenis cuti memotong kuota, kurangi sisa cuti
            $jenisCuti = $pengajuan->jenisCuti;
            if ($jenisCuti->potong_kuota) {
                $pegawai = $pengajuan->pegawai;
                if ($pegawai->sisa_cuti_tahunan < $pengajuan->lama_cuti) {
                    throw ValidationException::withMessages(['file' => 'Sisa cuti tahunan pegawai tidak mencukupi untuk pengajuan ini.']);
                }
                $this->pegawaiRepo->kurangiSisaCuti($pegawai, $pengajuan->lama_cuti);
            }

            // Update status pengajuan menjadi Disetujui
            $this->pengajuanRepo->updateStatus(
                $pengajuan,
                PengajuanCuti::STATUS_DISETUJUI,
                'Scan surat telah diunggah dan diverifikasi oleh admin.',
                Auth::id()
            );

            $this->logService->logUpload(
                'dokumen',
                "Upload scan surat pengajuan {$pengajuan->nomor_surat} oleh Admin",
                $dokumen
            );

            return $dokumen;
        });
    }

    public function uploadLampiran(PengajuanCuti $pengajuan, UploadedFile $file, ?string $keterangan = null): Dokumen
    {
        $this->validasiFile($file);

        $folder  = "pengajuan/{$pengajuan->id}/lampiran";
        $path    = $file->store($folder, 'public');

        return $this->repo->create([
            'pengajuan_cuti_id' => $pengajuan->id,
            'uploaded_by'       => Auth::id(),
            'jenis_dokumen'     => 'lampiran_pendukung',
            'nama_file'         => $file->getClientOriginalName(),
            'path_file'         => $path,
            'mime_type'         => $file->getMimeType(),
            'ukuran_file'       => $file->getSize(),
            'keterangan'        => $keterangan,
        ]);
    }

    public function delete(Dokumen $dokumen): void
    {
        Storage::disk('public')->delete($dokumen->path_file);
        $this->repo->delete($dokumen);
        $this->logService->logDelete('dokumen', "Menghapus file: {$dokumen->nama_file}");
    }

    private function validasiFile(UploadedFile $file): void
    {
        if (!in_array($file->getMimeType(), self::ALLOWED_MIMES)) {
            throw ValidationException::withMessages(['file' => 'Format file tidak didukung. Gunakan PDF, JPG, atau PNG.']);
        }

        if ($file->getSize() > self::MAX_SIZE_BYTES) {
            throw ValidationException::withMessages(['file' => 'Ukuran file maksimum 5MB.']);
        }
    }
}