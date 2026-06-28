<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\PengajuanCutiRepository;
use App\Services\PegawaiService;
use App\Services\PengajuanCutiService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private PengajuanCutiService $pengajuanService,
        private PegawaiService $pegawaiService,
        private PengajuanCutiRepository $pengajuanRepo,
    ) {}

    public function index(): View
    {
        $tahun        = now()->year;
        $statistik    = $this->pengajuanService->getStatistik();
        $perBulan     = $this->pengajuanService->statistikBulanan($tahun);
        $perBidang    = $this->pengajuanService->statistikPerBidang($tahun);
        $pengajuanBaru= $this->pengajuanRepo->paginateForAdmin(5, ['status' => 'menunggu']);
        $totalPegawai = $this->pegawaiService->countAll();

        $allCuti = \App\Models\PengajuanCuti::whereIn('status', ['disetujui', 'menunggu'])
            ->with(['pegawai.bidang', 'jenisCuti'])
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama' => $item->pegawai->nama_lengkap ?? '-',
                    'nip' => $item->pegawai->nip ?? '-',
                    'bidang' => $item->pegawai->bidang->nama_bidang ?? '-',
                    'jenis_cuti' => $item->jenisCuti->nama_cuti ?? '-',
                    'tanggal_mulai' => $item->tanggal_mulai->format('Y-m-d'),
                    'tanggal_selesai' => $item->tanggal_selesai->format('Y-m-d'),
                    'status' => $item->status,
                ];
            });

        return view('admin.dashboard.index', compact(
            'statistik',
            'perBulan',
            'perBidang',
            'pengajuanBaru',
            'totalPegawai',
            'tahun',
            'allCuti'
        ));
    }
}