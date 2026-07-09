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
                        'jumlah_hari' => $item->lama_cuti,
                    ]
                ];
            });

        return response()->json($events);
    }

    public function gdriveAuth()
    {
        $client = new \Google\Client();
        $client->setClientId(config('filesystems.disks.google.clientId'));
        $client->setClientSecret(config('filesystems.disks.google.clientSecret'));
        
        $redirectUri = config('filesystems.disks.google.redirectUri') ?: route('admin.gdrive.callback');
        $client->setRedirectUri($redirectUri);
        
        $client->addScope(\Google\Service\Drive::DRIVE);
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        return redirect()->away($client->createAuthUrl());
    }

    public function gdriveCallback(\Illuminate\Http\Request $request)
    {
        $code = $request->get('code');
        if (!$code) {
            return "Error: Authorization code not found.";
        }

        try {
            $client = new \Google\Client();
            $client->setClientId(config('filesystems.disks.google.clientId'));
            $client->setClientSecret(config('filesystems.disks.google.clientSecret'));
            
            $redirectUri = config('filesystems.disks.google.redirectUri') ?: route('admin.gdrive.callback');
            $client->setRedirectUri($redirectUri);

            $token = $client->fetchAccessTokenWithAuthCode($code);

            if (isset($token['refresh_token'])) {
                $refreshToken = $token['refresh_token'];
                
                // Update .env file
                $envPath = base_path('.env');
                if (file_exists($envPath)) {
                    $envContent = file_get_contents($envPath);
                    
                    // Replace GOOGLE_DRIVE_REFRESH_TOKEN
                    $pattern = '/^GOOGLE_DRIVE_REFRESH_TOKEN=.*$/m';
                    if (preg_match($pattern, $envContent)) {
                        $envContent = preg_replace($pattern, 'GOOGLE_DRIVE_REFRESH_TOKEN="' . $refreshToken . '"', $envContent);
                    } else {
                        $envContent .= "\nGOOGLE_DRIVE_REFRESH_TOKEN=\"" . $refreshToken . "\"";
                    }
                    
                    // Remove GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON if exists
                    $patternSA = '/^GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON=.*$/m';
                    if (preg_match($patternSA, $envContent)) {
                        $envContent = preg_replace($patternSA, 'GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON=""', $envContent);
                    }
                    
                    file_put_contents($envPath, $envContent);
                    
                    // Clear config cache
                    \Illuminate\Support\Facades\Artisan::call('config:clear');
                    
                    return "<h1>🎉 Refresh Token Berhasil Diperbarui!</h1>
                            <p>Refresh token baru Anda telah disimpan di file <code>.env</code>.</p>
                            <p>Silakan uji kembali fitur upload berkas/bukti scan cuti Anda.</p>
                            <p><a href='" . route('admin.dashboard') . "'>Kembali ke Dashboard Admin</a></p>";
                }
                
                return "File .env tidak ditemukan, silakan salin token ini ke .env Anda secara manual:<br><br><code>" . $refreshToken . "</code>";
            } else {
                return "Gagal mendapatkan Refresh Token. Silakan coba kembali atau pastikan Anda memberikan semua izin (centang izin Google Drive).<br><br>Response:<pre>" . print_r($token, true) . "</pre>";
            }
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
}