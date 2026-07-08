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

        // Hitung hari cuti terpakai tahun ini per jenis cuti
        $tahunIni = now()->year;
        $approvedLeaves = $pegawai->pengajuanCuti()
            ->where('status', 'disetujui')
            ->whereYear('tanggal_mulai', $tahunIni)
            ->with('jenisCuti')
            ->get();

        $usedDays = [];
        foreach ($approvedLeaves as $cuti) {
            $kode = $cuti->jenisCuti->kode_cuti ?? '';
            if (!isset($usedDays[$kode])) {
                $usedDays[$kode] = 0;
            }
            $usedDays[$kode] += $cuti->jumlah_hari;
        }

        // Ambil semua jenis cuti untuk list dropdown kuota di dashboard
        $jenisCutiList = \App\Models\JenisCuti::all();
        $quotas = [];
        foreach ($jenisCutiList as $jc) {
            $kode = $jc->kode_cuti;
            $used = $usedDays[$kode] ?? 0;
            
            if ($kode === 'CT') {
                $sisa = $pegawai->sisa_cuti_tahunan;
            } else {
                $maks = $jc->maks_hari ?? 0;
                $sisa = max($maks - $used, 0);
            }
            
            $quotas[] = [
                'id' => $jc->id,
                'kode' => $kode,
                'nama' => $jc->nama_cuti,
                'sisa' => $sisa,
            ];
        }

        return view('pegawai.dashboard.index', compact('pegawai', 'pengajuanList', 'statistik', 'quotas'));
    }

    public function calendar(): View
    {
        return view('pegawai.calendar');
    }
}