<?php

namespace App\Services;

use App\Models\PengajuanCuti;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    /**
     * Generate PDF surat cuti sesuai format ASN resmi
     * Simpan ke storage dan kembalikan path
     */
    public function generateSuratCuti(PengajuanCuti $pengajuan): string
    {
        $pdf = Pdf::loadView('pdf.surat-cuti', [
            'pengajuan' => $pengajuan,
            'pegawai'   => $pengajuan->pegawai,
            'bidang'    => $pengajuan->pegawai->bidang,
            'jabatan'   => $pengajuan->pegawai->jabatan,
            'jenisCuti' => $pengajuan->jenisCuti,
        ])
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
            'defaultFont'          => 'Arial',
        ]);

        $folder   = "pengajuan/{$pengajuan->id}";
        $filename = "surat-cuti-{$pengajuan->nomor_surat}.pdf";
        $filename = str_replace(['/', ' '], ['-', '_'], $filename);
        $path     = "{$folder}/{$filename}";

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Stream PDF untuk preview di browser
     */
    public function streamSuratCuti(PengajuanCuti $pengajuan): \Illuminate\Http\Response
    {
        $pdf = Pdf::loadView('pdf.surat-cuti', [
            'pengajuan' => $pengajuan,
            'pegawai'   => $pengajuan->pegawai,
            'bidang'    => $pengajuan->pegawai->bidang,
            'jabatan'   => $pengajuan->pegawai->jabatan,
            'jenisCuti' => $pengajuan->jenisCuti,
        ])
        ->setPaper('a4', 'portrait');

        $filename = "surat-cuti-{$pengajuan->nomor_surat}.pdf";
        $filename = str_replace(['/', ' '], ['-', '_'], $filename);

        return $pdf->stream($filename);
    }

    /**
     * Download PDF surat cuti
     */
    public function downloadSuratCuti(PengajuanCuti $pengajuan): \Illuminate\Http\Response
    {
        $pdf = Pdf::loadView('pdf.surat-cuti', [
            'pengajuan' => $pengajuan,
            'pegawai'   => $pengajuan->pegawai,
            'bidang'    => $pengajuan->pegawai->bidang,
            'jabatan'   => $pengajuan->pegawai->jabatan,
            'jenisCuti' => $pengajuan->jenisCuti,
        ])
        ->setPaper('a4', 'portrait');

        $filename = "surat-cuti-{$pengajuan->nomor_surat}.pdf";
        $filename = str_replace(['/', ' '], ['-', '_'], $filename);

        return $pdf->download($filename);
    }
}