<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DokumenController extends Controller
{
    /**
     * Tampilkan file dokumen (PDF/Gambar) secara inline di browser/modal.
     */
    public function view(Dokumen $dokumen): BinaryFileResponse
    {
        $filePath = $this->resolveFilePath($dokumen->path_file);

        if (!$filePath || !file_exists($filePath)) {
            abort(404, 'File dokumen tidak ditemukan di server.');
        }

        $mime = $dokumen->mime_type ?: (@mime_content_type($filePath) ?: 'application/pdf');

        return response()->file($filePath, [
            'Content-Type'        => $mime,
            'Content-Disposition' => 'inline; filename="' . rawurlencode($dokumen->nama_file) . '"',
            'Cache-Control'       => 'public, max-age=86400',
        ]);
    }

    /**
     * Unduh file dokumen secara langsung.
     */
    public function download(Dokumen $dokumen): BinaryFileResponse
    {
        $filePath = $this->resolveFilePath($dokumen->path_file);

        if (!$filePath || !file_exists($filePath)) {
            abort(404, 'File dokumen tidak ditemukan di server.');
        }

        return response()->download($filePath, $dokumen->nama_file);
    }

    /**
     * Tampilkan foto profil pegawai.
     */
    public function viewFoto(\App\Models\Pegawai $pegawai): BinaryFileResponse
    {
        if ($pegawai->foto && !in_array(strtolower(trim($pegawai->foto)), ['foto', 'null', 'none', ''])) {
            $filePath = $this->resolveFilePath($pegawai->foto);
            if ($filePath && file_exists($filePath)) {
                $mime = @mime_content_type($filePath) ?: 'image/jpeg';
                return response()->file($filePath, ['Content-Type' => $mime]);
            }
        }

        $defaultPath = public_path('images/default-avatar.svg');
        return response()->file($defaultPath, ['Content-Type' => 'image/svg+xml']);
    }

    /**
     * Resolusi lokasi file fisik secara fleksibel (support storage/app/public dan storage/app).
     */
    private function resolveFilePath(string $pathFile): ?string
    {
        if (str_starts_with($pathFile, 'http://') || str_starts_with($pathFile, 'https://')) {
            return null;
        }

        $cleanPath = ltrim($pathFile, '/');
        $cleanPath = preg_replace('#^(storage|public)/#i', '', $cleanPath);
        $cleanPath = ltrim($cleanPath, '/');

        $possiblePaths = [
            storage_path('app/public/' . $cleanPath),
            storage_path('app/' . $cleanPath),
            public_path('storage/' . $cleanPath),
            public_path($cleanPath),
            base_path('storage/app/public/' . $cleanPath),
            base_path('storage/app/' . $cleanPath),
            $pathFile,
        ];

        foreach ($possiblePaths as $p) {
            if (file_exists($p) && is_file($p)) {
                return $p;
            }
        }

        return null;
    }
}
