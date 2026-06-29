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
        $statistik = $this->service->getStatistik();
        $perBulan  = $this->service->statistikBulanan($tahun);
        $perBidang = $this->service->statistikPerBidang($tahun);
        $startYear = 2026;
        $currentYear = max($startYear, now()->year);
        $tahunList = range($currentYear, $startYear);

        return view('admin.laporan.index', compact('statistik', 'perBulan', 'perBidang', 'tahun', 'tahunList'));
    }
}