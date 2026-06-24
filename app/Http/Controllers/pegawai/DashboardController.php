<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Services\PegawaiService;
use App\Services\PengajuanCutiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private PegawaiService $pegawaiService,
        private PengajuanCutiService $pengajuanService,
    ) {}

    public function index(): View
    {
        $pegawai       = $this->pegawaiService->findByUserId(Auth::id());
        $pengajuanList = $this->pengajuanService->paginateForPegawai($pegawai->id, 5);
        $statistik     = [
            'total'     => $pegawai->pengajuanCuti()->count(),
            'menunggu'  => $pegawai->pengajuanCuti()->where('status', 'menunggu')->count(),
            'disetujui' => $pegawai->pengajuanCuti()->where('status', 'disetujui')->count(),
            'ditolak'   => $pegawai->pengajuanCuti()->where('status', 'ditolak')->count(),
        ];


        return view('pegawai.dashboard.index', compact('pegawai', 'pengajuanList', 'statistik'));
    }
}