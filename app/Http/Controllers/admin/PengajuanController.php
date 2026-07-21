<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanCuti;
use App\Repositories\BidangRepository;
use App\Repositories\PengajuanCutiRepository;
use App\Services\DokumenService;
use App\Services\ExportService;
use App\Services\PdfService;
use App\Services\PengajuanCutiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PengajuanController extends Controller
{
    public function __construct(
        private PengajuanCutiService $service,
        private DokumenService $dokumenService,
        private PdfService $pdfService,
        private ExportService $exportService,
        private BidangRepository $bidangRepo,
    ) {}

    public function index(Request $request): View
    {
        $filters   = $request->only(['search', 'status', 'jenis_cuti_id', 'bidang_id', 'bulan', 'tahun']);
        $pengajuan = $this->service->paginateForAdmin(15, $filters);
        $bidang    = $this->bidangRepo->all();
        $startYear = 2026;
        $currentYear = max($startYear, now()->year);
        $tahunList = range($currentYear, $startYear);

        return view('admin.pengajuan.index', compact('pengajuan', 'bidang', 'filters', 'tahunList'));
    }

    public function show(PengajuanCuti $pengajuan): View
    {
        $pengajuan = $this->service->findById($pengajuan->id);
        return view('admin.pengajuan.show', compact('pengajuan'));
    }

    public function verifikasi(Request $request, PengajuanCuti $pengajuan): RedirectResponse
    {
        $data = $request->validate([
            'status'        => ['required', 'in:disetujui,ditolak'],
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ]);

        $this->service->verifikasi($pengajuan, $data['status'], $data['catatan_admin'] ?? null);

        $label = $data['status'] === 'disetujui' ? 'disetujui' : 'ditolak';
        return redirect()->route('admin.pengajuan.show', $pengajuan)
                         ->with('success', "Pengajuan berhasil {$label}.");
    }

    public function uploadScan(Request $request, PengajuanCuti $pengajuan): RedirectResponse
    {
        $request->validate([
            'file_scan'   => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'keterangan'  => ['nullable', 'string', 'max:300'],
        ]);

        $this->dokumenService->uploadScanSurat(
            $pengajuan,
            $request->file('file_scan'),
            $request->keterangan
        );

        return redirect()->route('admin.pengajuan.show', $pengajuan)
                         ->with('success', 'Scan surat berhasil diunggah.');
    }

    public function previewPdf(PengajuanCuti $pengajuan): Response
    {
        $pengajuan = $this->service->findById($pengajuan->id);
        return $this->pdfService->streamSuratCuti($pengajuan);
    }

    public function exportPdf(Request $request): \Illuminate\Http\Response
    {
        $filters = $request->only(['status', 'tahun', 'bulan', 'bidang_id']);
        return $this->exportService->exportPdfLaporan($filters);
    }

    public function exportExcel(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $filters = $request->only(['status', 'tahun', 'bulan', 'bidang_id']);
        return $this->exportService->exportExcelLaporan($filters);
    }

    public function laporan(Request $request): View
    {
        $tahun     = $request->tahun ?? now()->year;
        $bulan     = $request->bulan ?? 'tahunan';
        
        $perBulan  = $this->service->statistikBulanan($tahun);
        $perBidang = $this->service->statistikPerBidang($tahun);
        $perJenisCuti = $this->service->statistikPerJenisCuti($tahun);
        
        $startYear = 2026;
        $currentYear = max($startYear, now()->year);
        $tahunList = range($currentYear, $startYear);

        // Hitung statistik bulanan/tahunan secara dinamis
        $queryStats = \App\Models\PengajuanCuti::query()
            ->whereYear('tanggal_pengajuan', $tahun);
            
        if ($bulan !== 'tahunan') {
            $queryStats->whereMonth('tanggal_pengajuan', $bulan);
        }

        $allStats = $queryStats->get();

        $statistik = [
            'total_pengajuan' => $allStats->count(),
            'total_menunggu'  => $allStats->where('status', \App\Models\PengajuanCuti::STATUS_MENUNGGU)->count(),
            'total_disetujui' => $allStats->where('status', \App\Models\PengajuanCuti::STATUS_DISETUJUI)->count(),
            'total_ditolak'   => $allStats->where('status', \App\Models\PengajuanCuti::STATUS_DITOLAK)->count(),
        ];

        $namaBulanList = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        $namaBulan = $bulan !== 'tahunan' ? ($namaBulanList[$bulan] ?? '') : '';
        $periodeLabel = $bulan === 'tahunan' ? "Tahun {$tahun}" : "{$namaBulan} {$tahun}";

        // Fetch report preview data
        if ($bulan === 'tahunan') {
            // Yearly summary: Rekap per pegawai
            $pegawaiList = \App\Models\Pegawai::with(['bidang', 'jabatan', 'pengajuanCuti' => function($q) use ($tahun) {
                $q->where('status', 'disetujui')
                  ->whereYear('tanggal_mulai', $tahun);
            }, 'pengajuanCuti.jenisCuti'])->orderBy('nama_lengkap')->get();

            $reportData = [];
            foreach ($pegawaiList as $p) {
                $sum = ['CT' => 0, 'CB' => 0, 'CS' => 0, 'CM' => 0, 'CAK' => 0, 'CLN' => 0];
                foreach ($p->pengajuanCuti as $c) {
                    $kode = $c->jenisCuti->kode_cuti ?? '';
                    $lamaCuti = $c->lama_cuti;
                    if ($kode === 'CB_HAJI') {
                        $lamaCuti = $lamaCuti * 30; // 3 bulan = 90 hari
                    }
                    if ($kode === 'CB_UMROH' || $kode === 'CB_HAJI') {
                        $kode = 'CB';
                    }
                    if (array_key_exists($kode, $sum)) {
                        $sum[$kode] += $lamaCuti;
                    }
                }
                $reportData[] = [
                    'pegawai' => $p,
                    'cuti' => $sum,
                ];
            }
        } else {
            // Monthly details
            $reportData = \App\Models\PengajuanCuti::with(['pegawai.bidang', 'pegawai.jabatan', 'jenisCuti'])
                ->whereYear('tanggal_mulai', $tahun)
                ->whereMonth('tanggal_mulai', $bulan)
                ->orderBy('tanggal_pengajuan', 'desc')
                ->get();
        }

        return view('admin.laporan.index', compact('statistik', 'perBulan', 'perBidang', 'perJenisCuti', 'tahun', 'bulan', 'tahunList', 'reportData', 'periodeLabel'));
    }

    public function exportZip(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan', 'tahunan');

        $query = \App\Models\PengajuanCuti::with(['pegawai', 'jenisCuti', 'scanSurat'])
            ->whereYear('tanggal_pengajuan', $tahun)
            ->whereHas('scanSurat');

        if ($bulan !== 'tahunan') {
            $query->whereMonth('tanggal_pengajuan', $bulan);
        }

        $pengajuanList = $query->get();

        if ($pengajuanList->isEmpty()) {
            return back()->with('error', 'Tidak ada dokumen bukti scan pada periode yang dipilih.');
        }

        $zipFileName = "Rekap_Bukti_Scan_Cuti_{$tahun}_" . ($bulan !== 'tahunan' ? "Bulan_{$bulan}" : "Tahunan") . ".zip";
        $zipPath = storage_path("app/public/{$zipFileName}");

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Gagal membuat file ZIP rekap.');
        }

        $count = 0;
        foreach ($pengajuanList as $item) {
            if ($item->scanSurat && !empty($item->scanSurat->path_file)) {
                $filePath = storage_path("app/public/" . $item->scanSurat->path_file);
                if (file_exists($filePath)) {
                    $ext = pathinfo($filePath, PATHINFO_EXTENSION);
                    $cleanNama = \Illuminate\Support\Str::slug($item->pegawai->nama_lengkap, '_');
                    $cleanNip = $item->pegawai->nip;
                    $cleanCuti = $item->jenisCuti->kode_cuti;
                    $tanggal = $item->tanggal_pengajuan->format('Ymd');
                    
                    $entryName = "{$cleanNip}_{$cleanNama}_{$cleanCuti}_{$tanggal}.{$ext}";
                    $zip->addFile($filePath, $entryName);
                    $count++;
                }
            }
        }

        $zip->close();

        if ($count === 0) {
            return back()->with('error', 'Berkas fisik bukti scan belum diunggah atau tidak ditemukan di penyimpanan server.');
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}