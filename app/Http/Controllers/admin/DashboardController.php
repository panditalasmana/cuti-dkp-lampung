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

        return view('admin.dashboard.index', compact(
            'statistik',
            'perBulan',
            'perBidang',
            'pengajuanBaru',
            'totalPegawai',
            'tahun'
        ));
    }
}