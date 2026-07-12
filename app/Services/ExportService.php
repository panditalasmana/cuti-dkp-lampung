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
        $tahun = $filters['tahun'] ?? now()->year;
        $bulan = $filters['bulan'] ?? 'tahunan';

        if ($bulan === 'tahunan') {
            // Rekap tahunan per pegawai
            $pegawaiList = \App\Models\Pegawai::with(['bidang', 'jabatan', 'pengajuanCuti' => function($q) use ($tahun) {
                $q->where('status', 'disetujui')
                  ->whereYear('tanggal_mulai', $tahun);
            }, 'pengajuanCuti.jenisCuti'])->orderBy('nama_lengkap')->get();

            $reportData = [];
            foreach ($pegawaiList as $p) {
                $sum = ['CT' => 0, 'CB' => 0, 'CS' => 0, 'CM' => 0, 'CAK' => 0, 'CLN' => 0];
                foreach ($p->pengajuanCuti as $c) {
                    $kode = $c->jenisCuti->kode_cuti ?? '';
                    if (array_key_exists($kode, $sum)) {
                        $sum[$kode] += $c->lama_cuti;
                    }
                }
                $reportData[] = [
                    'pegawai' => $p,
                    'cuti' => $sum,
                ];
            }

            $pdf = Pdf::loadView('pdf.laporan-cuti-tahunan', [
                'reportData' => $reportData,
                'filters'    => $filters,
                'generated'  => now()->isoFormat('dddd, D MMMM Y HH:mm'),
            ])->setPaper('a4', 'landscape');
        } else {
            // Detail bulanan
            $data = \App\Models\PengajuanCuti::with(['pegawai.bidang', 'pegawai.jabatan', 'jenisCuti'])
                ->whereYear('tanggal_mulai', $tahun)
                ->whereMonth('tanggal_mulai', $bulan)
                ->orderBy('tanggal_pengajuan', 'desc')
                ->get();

            $pdf = Pdf::loadView('pdf.laporan-cuti-bulanan', [
                'pengajuan' => $data,
                'filters'   => $filters,
                'generated' => now()->isoFormat('dddd, D MMMM Y HH:mm'),
            ])->setPaper('a4', 'landscape');
        }

        $filename = 'laporan-cuti-' . ($bulan === 'tahunan' ? "tahunan-{$tahun}" : "bulanan-{$bulan}-{$tahun}") . '.pdf';
        return $pdf->download($filename);
    }

    public function exportExcelLaporan(array $filters = []): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $tahun = $filters['tahun'] ?? now()->year;
        $bulan = $filters['bulan'] ?? 'tahunan';

        $filename = 'laporan-cuti-' . ($bulan === 'tahunan' ? "tahunan-{$tahun}" : "bulanan-{$bulan}-{$tahun}") . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($tahun, $bulan) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // BOM

            if ($bulan === 'tahunan') {
                fputcsv($handle, [
                    'No',
                    'NIP',
                    'Nama Pegawai',
                    'Bidang',
                    'Jabatan',
                    'Cuti Tahunan (Hari)',
                    'Cuti Besar (Hari)',
                    'Cuti Sakit (Hari)',
                    'Cuti Melahirkan (Bulan)',
                    'Cuti Alasan Penting (Hari)',
                    'Cuti Di Luar Tanggungan Negara (Tahun)',
                ], ';');

                $pegawaiList = \App\Models\Pegawai::with(['bidang', 'jabatan', 'pengajuanCuti' => function($q) use ($tahun) {
                    $q->where('status', 'disetujui')
                      ->whereYear('tanggal_mulai', $tahun);
                }, 'pengajuanCuti.jenisCuti'])->orderBy('nama_lengkap')->get();

                foreach ($pegawaiList as $index => $p) {
                    $sum = ['CT' => 0, 'CB' => 0, 'CS' => 0, 'CM' => 0, 'CAK' => 0, 'CLN' => 0];
                    foreach ($p->pengajuanCuti as $c) {
                        $kode = $c->jenisCuti->kode_cuti ?? '';
                        if (array_key_exists($kode, $sum)) {
                            $sum[$kode] += $c->lama_cuti;
                        }
                    }
                    fputcsv($handle, [
                        $index + 1,
                        $p->nip ?? '-',
                        $p->nama_lengkap ?? '-',
                        $p->bidang->nama_bidang ?? '-',
                        $p->jabatan->nama_jabatan ?? '-',
                        $sum['CT'],
                        $sum['CB'],
                        $sum['CS'],
                        $sum['CM'],
                        $sum['CAK'],
                        $sum['CLN'],
                    ], ';');
                }
            } else {
                fputcsv($handle, [
                    'No',
                    'NIP',
                    'Nama Pegawai',
                    'Bidang',
                    'Jabatan',
                    'Jenis Cuti',
                    'Tanggal Mulai',
                    'Tanggal Selesai',
                    'Lama Cuti',
                    'Alasan',
                    'Status',
                    'Tanggal Pengajuan',
                ], ';');

                $data = \App\Models\PengajuanCuti::with(['pegawai.bidang', 'pegawai.jabatan', 'jenisCuti'])
                    ->whereYear('tanggal_mulai', $tahun)
                    ->whereMonth('tanggal_mulai', $bulan)
                    ->orderBy('tanggal_pengajuan', 'desc')
                    ->get();

                foreach ($data as $index => $item) {
                    fputcsv($handle, [
                        $index + 1,
                        $item->pegawai->nip ?? '-',
                        $item->pegawai->nama_lengkap ?? '-',
                        $item->pegawai->bidang->nama_bidang ?? '-',
                        $item->pegawai->jabatan->nama_jabatan ?? '-',
                        $item->jenisCuti->nama_cuti ?? '-',
                        $item->tanggal_mulai?->format('d/m/Y') ?? '-',
                        $item->tanggal_selesai?->format('d/m/Y') ?? '-',
                        $item->lama_cuti_display,
                        $item->alasan_cuti,
                        $item->status_label,
                        $item->tanggal_pengajuan?->format('d/m/Y H:i') ?? '-',
                    ], ';');
                }
            }

            fclose($handle);
        }, 200, $headers);
    }
}