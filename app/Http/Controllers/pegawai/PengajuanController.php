<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\JenisCuti;
use App\Models\PengajuanCuti;
use App\Services\PdfService;
use App\Services\PegawaiService;
use App\Services\PengajuanCutiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PengajuanController extends Controller
{
    public function __construct(
        private PengajuanCutiService $service,
        private PegawaiService $pegawaiService,
        private PdfService $pdfService,
    ) {}

    private function getPegawai()
    {
        return $this->pegawaiService->findByUserId(Auth::id());
    }

    public function index(Request $request): View
    {
        $pegawai   = $this->getPegawai();
        $filters   = $request->only(['status', 'tahun']);
        $riwayat   = $this->service->paginateForPegawai($pegawai->id, 10, $filters);
        $tahunList = range(now()->year, now()->year - 5);

        return view('pegawai.riwayat.index', compact('pegawai', 'riwayat', 'filters', 'tahunList'));
    }

    public function create(): View
    {
        $pegawai   = $this->getPegawai();
        $jenisCuti = JenisCuti::active()->orderBy('nama_cuti')->get();

        return view('pegawai.pengajuan.create', compact('pegawai', 'jenisCuti'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'jenis_cuti_id'           => ['required', 'exists:jenis_cuti,id'],
            'tanggal_mulai'           => ['required', 'date'],
            'tanggal_selesai'         => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'alasan_cuti'             => ['required', 'string', 'min:10', 'max:1000'],
            'alamat_selama_cuti'      => ['required', 'string', 'max:500'],
            'no_telp_selama_cuti'     => ['nullable', 'string', 'max:15'],
            'atasan_langsung_select'  => ['required', 'string'],
            'pejabat_wenang_select'   => ['required', 'string'],
        ]);

        $pegawai   = $this->getPegawai();
        $pengajuan = $this->service->ajukan($pegawai, $data);

        return redirect()->route('pegawai.pengajuan.show', $pengajuan)
                         ->with('success', "Pengajuan cuti berhasil dibuat.");
    }

    public function show(PengajuanCuti $pengajuan): View
    {
        $pegawai   = $this->getPegawai();
        $pengajuan = $this->service->findByIdForPegawai($pengajuan->id, $pegawai->id);

        return view('pegawai.pengajuan.show', compact('pengajuan', 'pegawai'));
    }

    public function preview(PengajuanCuti $pengajuan): Response
    {
        $pegawai   = $this->getPegawai();
        $pengajuan = $this->service->findByIdForPegawai($pengajuan->id, $pegawai->id);

        return $this->pdfService->streamSuratCuti($pengajuan);
    }

    public function cetak(PengajuanCuti $pengajuan): Response
    {
        $pegawai   = $this->getPegawai();
        $pengajuan = $this->service->findByIdForPegawai($pengajuan->id, $pegawai->id);

        return $this->pdfService->downloadSuratCuti($pengajuan);
    }

    public function hitungHari(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'tanggal_mulai'   => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        $hari = $this->service->hitungHariKerja(
            $request->tanggal_mulai,
            $request->tanggal_selesai
        );

        return response()->json(['lama_cuti' => $hari]);
    }
}