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
            $usedDays[$kode] += $cuti->lama_cuti;
        }

        // Ambil semua jenis cuti untuk list dropdown kuota di dashboard
        $jenisCutiList = \App\Models\JenisCuti::all();
        $quotas = [];
        foreach ($jenisCutiList as $jc) {
            $kode = $jc->kode_cuti;
            
            // Skip Cuti Melahirkan jika bukan perempuan
            if ($kode === 'CM' && $pegawai->jenis_kelamin !== 'P') {
                continue;
            }
            
            $used = $usedDays[$kode] ?? 0;
            
            if ($kode === 'CT') {
                $sisa = $pegawai->sisa_cuti_tahunan;
            } elseif ($kode === 'CB_HAJI') {
                $usedHajiCount = $pegawai->pengajuanCuti()
                    ->whereNotIn('status', ['ditolak', 'dibatalkan'])
                    ->where('jenis_cuti_id', $jc->id)
                    ->count();
                $sisa = max(3 - ($usedHajiCount * 3), 0);
            } elseif ($kode === 'CB_UMROH') {
                $usedUmrohDays = $pegawai->pengajuanCuti()
                    ->whereNotIn('status', ['ditolak', 'dibatalkan'])
                    ->where('jenis_cuti_id', $jc->id)
                    ->sum('lama_cuti');
                $sisa = max(30 - $usedUmrohDays, 0);
            } else {
                $maks = $jc->maks_hari ?? 0;
                $sisa = max($maks - $used, 0);
            }
            
            $quotas[] = [
                'id' => $jc->id,
                'kode' => $kode,
                'nama' => $jc->nama_cuti,
                'sisa' => $sisa,
                'satuan' => $jc->satuan,
            ];
        }

        return view('pegawai.dashboard.index', compact('pegawai', 'pengajuanList', 'statistik', 'quotas'));
    }

    public function calendar(): View
    {
        return view('pegawai.calendar');
    }

    public function calendarEvents(): \Illuminate\Http\JsonResponse
    {
        $pegawai = $this->pegawaiService->findByUserId(Auth::id());
        $events = \App\Models\PengajuanCuti::where('status', 'disetujui')
            ->where('pegawai_id', $pegawai->id)
            ->with(['pegawai.bidang', 'jenisCuti'])
            ->get()
            ->map(function ($item) {
                $namaCuti = strtolower($item->jenisCuti->nama_cuti ?? '');
                $color = '#14b8a6'; // default: teal
                
                if (str_contains($namaCuti, 'tahunan')) {
                    $color = '#0d6efd'; // blue
                } elseif (str_contains($namaCuti, 'sakit')) {
                    $color = '#dc3545'; // red
                } elseif (str_contains($namaCuti, 'melahirkan')) {
                    $color = '#ec4899'; // pink
                } elseif (str_contains($namaCuti, 'besar')) {
                    $color = '#6f42c1'; // purple
                } elseif (str_contains($namaCuti, 'penting')) {
                    $color = '#fd7e14'; // orange
                }

                return [
                    'id' => $item->id,
                    'title' => ($item->pegawai->nama_lengkap ?? 'Pegawai') . ' - ' . ($item->jenisCuti->nama_cuti ?? 'Cuti'),
                    'start' => $item->tanggal_mulai->format('Y-m-d'),
                    'end' => $item->tanggal_selesai->copy()->addDay()->format('Y-m-d'),
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'extendedProps' => [
                        'nama' => $item->pegawai->nama_lengkap ?? '-',
                        'nip' => $item->pegawai->nip ?? '-',
                        'bidang' => $item->pegawai->bidang->nama_bidang ?? '-',
                        'jenis_cuti' => $item->jenisCuti->nama_cuti ?? '-',
                        'tanggal_mulai' => $item->tanggal_mulai->isoFormat('D MMMM Y'),
                        'tanggal_selesai' => $item->tanggal_selesai->isoFormat('D MMMM Y'),
                        'jumlah_hari' => $item->lama_cuti_display,
                    ]
                ];
            });

        return response()->json($events);
    }
}