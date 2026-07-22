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
        $perJenisCuti = $this->pengajuanService->statistikPerJenisCuti($tahun);
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
            'perJenisCuti',
            'pengajuanBaru',
            'totalPegawai',
            'tahun',
            'allCuti'
        ));
    }

    public function calendar(): View
    {
        return view('admin.calendar');
    }

    public function calendarEvents(): \Illuminate\Http\JsonResponse
    {
        $events = \App\Models\PengajuanCuti::where('status', 'disetujui')
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
                    'end' => $item->tanggal_selesai->copy()->addDay()->format('Y-m-d'), // End date is exclusive in FullCalendar
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

        $liburEvents = \App\Models\HariLibur::all()->map(function ($libur) {
            return [
                'id' => 'libur_' . $libur->id,
                'title' => ($libur->is_cuti_bersama ? '🟡 ' : '🔴 ') . $libur->keterangan,
                'start' => $libur->tanggal->format('Y-m-d'),
                'end' => $libur->tanggal->format('Y-m-d'),
                'backgroundColor' => $libur->is_cuti_bersama ? '#f59e0b' : '#ef4444',
                'borderColor' => $libur->is_cuti_bersama ? '#d97706' : '#dc2626',
                'display' => 'block',
                'extendedProps' => [
                    'nama' => $libur->keterangan,
                    'nip' => 'HARI LIBUR',
                    'bidang' => $libur->is_cuti_bersama ? 'Cuti Bersama' : 'Libur Nasional',
                    'jenis_cuti' => $libur->keterangan,
                    'tanggal_mulai' => $libur->tanggal->isoFormat('D MMMM Y'),
                    'tanggal_selesai' => $libur->tanggal->isoFormat('D MMMM Y'),
                    'jumlah_hari' => '1 hari',
                ]
            ];
        });

        return response()->json($events->concat($liburEvents)->values());
    }

    public function checkNotifications(): \Illuminate\Http\JsonResponse
    {
        $latest = \App\Models\PengajuanCuti::with(['pegawai', 'jenisCuti'])
            ->where('status', \App\Models\PengajuanCuti::STATUS_MENUNGGU)
            ->orderBy('id', 'desc')
            ->first();

        $unreadCount = \App\Models\PengajuanCuti::where('status', \App\Models\PengajuanCuti::STATUS_MENUNGGU)->count();

        $latestData = null;
        if ($latest) {
            $latestData = [
                'id' => $latest->id,
                'nama_pegawai' => $latest->pegawai->nama_lengkap ?? 'Pegawai',
                'jenis_cuti' => $latest->jenisCuti->nama_cuti ?? 'Cuti',
                'lama_cuti' => $latest->lama_cuti_display,
                'waktu' => $latest->created_at ? $latest->created_at->diffForHumans() : 'baru saja',
            ];
        }

        return response()->json([
            'unread_count' => $unreadCount,
            'latest_pengajuan' => $latestData,
        ]);
    }
}