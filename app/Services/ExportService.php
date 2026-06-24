<?php

namespace App\Services;

use App\Repositories\PengajuanCutiRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;

class ExportService
{
    public function __construct(
        private PengajuanCutiRepository $repo
    ) {}

    public function exportPdfLaporan(array $filters = []): \Illuminate\Http\Response
    {
        $data = $this->repo->getForExport($filters);

        $pdf = Pdf::loadView('pdf.laporan-cuti', [
            'pengajuan' => $data,
            'filters'   => $filters,
            'generated' => now()->isoFormat('dddd, D MMMM Y HH:mm'),
        ])->setPaper('a4', 'landscape');

        $filename = 'laporan-cuti-' . now()->format('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    public function exportExcelLaporan(array $filters = []): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $data = $this->repo->getForExport($filters);

        $filename = 'laporan-cuti-' . now()->format('Y-m-d') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($data) {
            $handle = fopen('php://output', 'w');

            // BOM untuk Excel agar bisa baca UTF-8
            fputs($handle, "\xEF\xBB\xBF");

            // Header kolom
            fputcsv($handle, [
                'No',
                'Nomor Surat',
                'NIP',
                'Nama Pegawai',
                'Bidang',
                'Jabatan',
                'Jenis Cuti',
                'Tanggal Mulai',
                'Tanggal Selesai',
                'Lama Cuti (Hari)',
                'Alasan',
                'Status',
                'Tanggal Pengajuan',
                'Diverifikasi Oleh',
                'Tanggal Verifikasi',
            ], ';');

            foreach ($data as $index => $item) {
                fputcsv($handle, [
                    $index + 1,
                    $item->nomor_surat,
                    $item->pegawai->nip ?? '-',
                    $item->pegawai->nama_lengkap ?? '-',
                    $item->pegawai->bidang->nama_bidang ?? '-',
                    $item->pegawai->jabatan->nama_jabatan ?? '-',
                    $item->jenisCuti->nama_cuti ?? '-',
                    $item->tanggal_mulai?->format('d/m/Y') ?? '-',
                    $item->tanggal_selesai?->format('d/m/Y') ?? '-',
                    $item->lama_cuti,
                    $item->alasan_cuti,
                    $item->status_label,
                    $item->tanggal_pengajuan?->format('d/m/Y H:i') ?? '-',
                    $item->verifikator->name ?? '-',
                    $item->tanggal_verifikasi?->format('d/m/Y H:i') ?? '-',
                ], ';');
            }

            fclose($handle);
        }, 200, $headers);
    }
}