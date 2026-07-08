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
                $this->deleteFile($scanLama->path_file);
                $this->repo->delete($scanLama);
            }

            // Simpan file baru
            $folder   = "pengajuan/{$pengajuan->id}/scan";
            $path     = $this->saveFile($file, $folder);
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
            // Log upload activity
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
        $path    = $this->saveFile($file, $folder);

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
        $this->deleteFile($dokumen->path_file);
        $this->repo->delete($dokumen);
        $this->logService->logDelete('dokumen', "Menghapus file: {$dokumen->nama_file}");
    }

    private function saveFile(UploadedFile $file, string $folder): string
    {
        $disk = config('filesystems.upload_disk', 'public');
        if ($disk === 'google') {
            return $this->uploadToGoogleDrive($file, $folder);
        }
        return $file->store($folder, 'public');
    }

    private function deleteFile(string $path): void
    {
        $disk = config('filesystems.upload_disk', 'public');
        if ($disk === 'google') {
            $this->deleteFromGoogleDrive($path);
        } else {
            Storage::disk('public')->delete($path);
        }
    }

    private function uploadToGoogleDrive(UploadedFile $file, string $folderName): string
    {
        $client = new \Google\Client();
        
        $serviceAccountJson = config('filesystems.disks.google.serviceAccountJson');
        if (!empty($serviceAccountJson)) {
            $client->setAuthConfig(base_path($serviceAccountJson));
        } else {
            $client->setClientId(config('filesystems.disks.google.clientId'));
            $client->setClientSecret(config('filesystems.disks.google.clientSecret'));
            $client->refreshToken(config('filesystems.disks.google.refreshToken'));
        }
        
        $client->addScope(\Google\Service\Drive::DRIVE);
        $service = new \Google\Service\Drive($client);
        
        $parentFolderId = config('filesystems.disks.google.folderId') ?: 'root';
        
        $fileMetadata = new \Google\Service\Drive\DriveFile([
            'name' => time() . '_' . $file->getClientOriginalName(),
            'parents' => [$parentFolderId]
        ]);
        
        $content = file_get_contents($file->getRealPath());
        $mimeType = $file->getClientMimeType();
        
        $uploadedFile = $service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $mimeType,
            'uploadType' => 'multipart',
            'fields' => 'id'
        ]);
        
        try {
            $permission = new \Google\Service\Drive\Permission([
                'type' => 'anyone',
                'role' => 'reader'
            ]);
            $service->permissions->create($uploadedFile->id, $permission);
        } catch (\Exception $e) {
            // Abaikan jika gagal membagikan secara publik, yang penting file terupload
        }
        
        return $uploadedFile->id;
    }

    private function deleteFromGoogleDrive(string $fileId): void
    {
        try {
            $client = new \Google\Client();
            $serviceAccountJson = config('filesystems.disks.google.serviceAccountJson');
            if (!empty($serviceAccountJson)) {
                $client->setAuthConfig(base_path($serviceAccountJson));
            } else {
                $client->setClientId(config('filesystems.disks.google.clientId'));
                $client->setClientSecret(config('filesystems.disks.google.clientSecret'));
                $client->refreshToken(config('filesystems.disks.google.refreshToken'));
            }
            $client->addScope(\Google\Service\Drive::DRIVE);
            $service = new \Google\Service\Drive($client);
            
            $service->files->delete($fileId);
        } catch (\Exception $e) {
            // Abaikan jika gagal hapus
        }
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