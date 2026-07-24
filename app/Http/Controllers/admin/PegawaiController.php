<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Repositories\BidangRepository;
use App\Repositories\JabatanRepository;
use App\Services\PegawaiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PegawaiController extends Controller
{
    public function __construct(
        private PegawaiService $service,
        private BidangRepository $bidangRepo,
        private JabatanRepository $jabatanRepo,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'bidang_id', 'jabatan_id', 'jenis_pegawai', 'status']);
        $pegawai = $this->service->paginate(15, $filters);
        $bidang  = $this->bidangRepo->all();
        $jabatan = $this->jabatanRepo->all();
        
        $autocompleteList = Pegawai::where('is_active', true)
            ->get(['id', 'nip', 'nama_lengkap'])
            ->map(function ($p) {
                return [
                    'nip'  => $p->nip,
                    'nama' => $p->nama_lengkap,
                    'url'  => route('admin.pegawai.show', $p),
                ];
            });

        return view('admin.pegawai.index', compact('pegawai', 'bidang', 'jabatan', 'autocompleteList'));
    }

    public function create(): View
    {
        $bidang  = $this->bidangRepo->all();
        $jabatan = $this->jabatanRepo->all();
        return view('admin.pegawai.create', compact('bidang', 'jabatan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nip'               => ['required', 'string', 'digits_between:15,18'],
            'nama_lengkap'      => ['required', 'string', 'max:200'],
            'email'             => ['nullable', 'email', 'max:100'],
            'password'          => ['nullable', 'string', 'min:8'],
            'bidang_id'         => ['required', 'exists:bidang,id'],
            'sub_bagian'        => ['nullable', 'string', 'max:150'],
            'jabatan_id'        => ['required', 'exists:jabatan,id'],
            'jenis_kelamin'     => ['required', 'in:L,P'],
            'tempat_lahir'      => ['required', 'string', 'max:100'],
            'tanggal_lahir'     => ['required', 'date', 'before:today'],
            'no_telepon'        => ['nullable', 'string', 'max:15'],
            'tanggal_masuk'     => ['required', 'date'],
            'jenis_pegawai'     => ['required', 'in:PNS,PPPK,Honorer'],
            'pangkat'           => ['nullable', 'string', 'max:100'],
            'sisa_cuti_tahunan' => ['required', 'integer', 'min:0', 'max:72'],
            'foto'              => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'is_active'         => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;

        $this->service->create($data);

        return redirect()->route('admin.pegawai.index')
                         ->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function show(Pegawai $pegawai): View
    {
        $pegawai = $this->service->findById($pegawai->id);
        
        // Ambil semua jenis cuti aktif
        $jenisCutiList = \App\Models\JenisCuti::active()->get();
        
        // Hitung akumulasi hari cuti yang sudah disetujui/menunggu di tahun ini
        $tahunIni = now()->year;
        $usedDays = $pegawai->pengajuanCuti()
            ->whereNotIn('status', [\App\Models\PengajuanCuti::STATUS_DITOLAK, \App\Models\PengajuanCuti::STATUS_DIBATALKAN])
            ->whereYear('tanggal_mulai', $tahunIni)
            ->get()
            ->groupBy('jenisCuti.kode_cuti')
            ->map(function ($group) {
                return $group->sum('lama_cuti');
            });

        $quotas = [];
        foreach ($jenisCutiList as $jc) {
            $kode = $jc->kode_cuti;
            $used = $usedDays[$kode] ?? 0;
            
            if ($kode === 'CT') {
                $sisa = $pegawai->sisa_cuti_tahunan;
            } elseif ($kode === 'CB_HAJI') {
                $usedHajiCount = $pegawai->pengajuanCuti()
                    ->whereNotIn('status', [\App\Models\PengajuanCuti::STATUS_DITOLAK, \App\Models\PengajuanCuti::STATUS_DIBATALKAN])
                    ->where('jenis_cuti_id', $jc->id)
                    ->count();
                $sisa = max(3 - ($usedHajiCount * 3), 0);
            } elseif ($kode === 'CB_UMROH') {
                $usedUmrohDays = $pegawai->pengajuanCuti()
                    ->whereNotIn('status', [\App\Models\PengajuanCuti::STATUS_DITOLAK, \App\Models\PengajuanCuti::STATUS_DIBATALKAN])
                    ->where('jenis_cuti_id', $jc->id)
                    ->sum('lama_cuti');
                $sisa = max(30 - $usedUmrohDays, 0);
            } else {
                $maks = $jc->maks_hari ?? 0;
                $sisa = max($maks - $used, 0);
            }
            
            $quotas[] = [
                'nama' => $jc->nama_cuti,
                'sisa' => $sisa,
                'satuan' => $jc->satuan,
                'maks' => $jc->maks_hari,
            ];
        }

        // Ambil semua riwayat pengajuan cuti pegawai tersebut
        $riwayatCuti = $pegawai->pengajuanCuti()
            ->with('jenisCuti')
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();

        return view('admin.pegawai.show', compact('pegawai', 'quotas', 'riwayatCuti'));
    }

    public function edit(Pegawai $pegawai): View
    {
        $bidang  = $this->bidangRepo->all();
        $jabatan = $this->jabatanRepo->all();
        return view('admin.pegawai.edit', compact('pegawai', 'bidang', 'jabatan'));
    }

    public function update(Request $request, Pegawai $pegawai): RedirectResponse
    {
        $data = $request->validate([
            'nama_lengkap'      => ['required', 'string', 'max:200'],
            'email'             => ['nullable', 'email', 'max:100'],
            'password'          => ['nullable', 'string', 'min:8'],
            'bidang_id'         => ['required', 'exists:bidang,id'],
            'sub_bagian'        => ['nullable', 'string', 'max:150'],
            'jabatan_id'        => ['required', 'exists:jabatan,id'],
            'jenis_kelamin'     => ['required', 'in:L,P'],
            'tempat_lahir'      => ['required', 'string', 'max:100'],
            'tanggal_lahir'     => ['required', 'date', 'before:today'],
            'no_telepon'        => ['nullable', 'string', 'max:15'],
            'tanggal_masuk'     => ['required', 'date'],
            'jenis_pegawai'     => ['required', 'in:PNS,PPPK,Honorer'],
            'pangkat'           => ['nullable', 'string', 'max:100'],
            'sisa_cuti_tahunan' => ['required', 'integer', 'min:0', 'max:72'],
            'foto'              => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'is_active'         => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $this->service->update($pegawai, $data);

        return redirect()->route('admin.pegawai.index')
                         ->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy(Pegawai $pegawai): RedirectResponse
    {
        $this->service->delete($pegawai);
        return redirect()->route('admin.pegawai.index')
                         ->with('success', 'Pegawai berhasil dihapus.');
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_pegawai.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'nip',
                'nama_lengkap',
                'email',
                'bidang',
                'sub_bagian',
                'jabatan',
                'jenis_pegawai',
                'pangkat',
                'tempat_lahir',
                'tanggal_lahir',
                'no_telepon',
                'tanggal_masuk',
                'sisa_cuti_tahunan',
                'jenis_kelamin'
            ], ';');

            fputcsv($file, [
                '199203102022031002',
                'Budiman Santoso',
                'budiman@mail.com',
                'Sekretariat Dinas',
                'Sub Bagian Umum dan Kepegawaian',
                'Kepala Dinas',
                'PNS',
                'Penata Muda / IIIa',
                'Bandar Lampung',
                '10/03/1992',
                '081234567890',
                '01/03/2022',
                '12',
                'L'
            ], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file_csv' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $file = $request->file('file_csv');
        $filePath = $file->getRealPath();

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return redirect()->back()->with('error', 'Gagal membuka file CSV.');
        }

        $header = fgetcsv($handle, 0, ';');
        if (!$header || count($header) < 5) {
            rewind($handle);
            $header = fgetcsv($handle, 0, ',');
            $delimiter = ',';
        } else {
            $delimiter = ';';
        }

        if (!$header) {
            fclose($handle);
            return redirect()->back()->with('error', 'Format CSV tidak valid atau kosong.');
        }

        $header = array_map(function($h) {
            return trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h));
        }, $header);

        $fieldMap = array_flip($header);

        $requiredFields = ['nip', 'nama_lengkap', 'bidang', 'jabatan', 'jenis_pegawai', 'tempat_lahir', 'tanggal_lahir', 'tanggal_masuk', 'sisa_cuti_tahunan'];
        foreach ($requiredFields as $field) {
            if (!isset($fieldMap[$field])) {
                fclose($handle);
                return redirect()->back()->with('error', "Kolom wajib '{$field}' tidak ditemukan dalam CSV.");
            }
        }

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                if (empty($row) || count($row) < count($header)) {
                    continue;
                }

                $nip = trim($row[$fieldMap['nip']]);
                if (empty($nip)) continue;

                $nama = trim($row[$fieldMap['nama_lengkap']]);
                $email = isset($fieldMap['email']) ? trim($row[$fieldMap['email']]) : null;
                $bidangNama = trim($row[$fieldMap['bidang']]);
                $jabatanNama = trim($row[$fieldMap['jabatan']]);
                $subBagian = isset($fieldMap['sub_bagian']) ? trim($row[$fieldMap['sub_bagian']]) : null;
                $jenisPegawai = strtoupper(trim($row[$fieldMap['jenis_pegawai']]));
                $pangkat = isset($fieldMap['pangkat']) ? trim($row[$fieldMap['pangkat']]) : null;
                $tempatLahir = trim($row[$fieldMap['tempat_lahir']]);
                $tanggalLahir = trim($row[$fieldMap['tanggal_lahir']]);
                $noTelepon = isset($fieldMap['no_telepon']) ? trim($row[$fieldMap['no_telepon']]) : null;
                $tanggalMasuk = trim($row[$fieldMap['tanggal_masuk']]);
                $sisaCuti = intval(trim($row[$fieldMap['sisa_cuti_tahunan']]));
                $jenisKelamin = isset($fieldMap['jenis_kelamin']) ? strtoupper(trim($row[$fieldMap['jenis_kelamin']])) : 'L';

                $bidang = \App\Models\Bidang::where('nama_bidang', 'LIKE', "%{$bidangNama}%")->first();
                if (!$bidang) {
                    $errors[] = "Baris NIP {$nip}: Bidang '{$bidangNama}' tidak ditemukan.";
                    $errorCount++;
                    continue;
                }

                $jabatan = \App\Models\Jabatan::where('nama_jabatan', 'LIKE', "%{$jabatanNama}%")->first();
                if (!$jabatan) {
                    $errors[] = "Baris NIP {$nip}: Jabatan '{$jabatanNama}' tidak ditemukan.";
                    $errorCount++;
                    continue;
                }

                try {
                    $tglLahirParsed = $this->parseDate($tanggalLahir);
                    $tglMasukParsed = $this->parseDate($tanggalMasuk);
                } catch (\Exception $e) {
                    $errors[] = "Baris NIP {$nip}: Format tanggal lahir/masuk salah. Gunakan YYYY-MM-DD atau DD/MM/YYYY.";
                    $errorCount++;
                    continue;
                }

                try {
                    $existingPegawai = Pegawai::where('nip', $nip)->first();
                    if ($existingPegawai) {
                        // Update pegawai
                        $user = $existingPegawai->user;
                        if ($user) {
                            $user->update([
                                'name' => $nama,
                                'email' => $email,
                            ]);
                        }
                        $existingPegawai->update([
                            'bidang_id' => $bidang->id,
                            'sub_bagian' => $subBagian,
                            'jabatan_id' => $jabatan->id,
                            'jenis_pegawai' => $jenisPegawai,
                            'pangkat' => $pangkat,
                            'tempat_lahir' => $tempatLahir,
                            'tanggal_lahir' => $tglLahirParsed,
                            'no_telepon' => $noTelepon,
                            'tanggal_masuk' => $tglMasukParsed,
                            'sisa_cuti_tahunan' => $sisaCuti,
                            'jenis_kelamin' => in_array($jenisKelamin, ['L', 'P']) ? $jenisKelamin : 'L',
                        ]);
                    } else {
                        // Create pegawai baru
                        $this->service->create([
                            'nip' => $nip,
                            'nama_lengkap' => $nama,
                            'email' => $email,
                            'bidang_id' => $bidang->id,
                            'sub_bagian' => $subBagian,
                            'jabatan_id' => $jabatan->id,
                            'jenis_pegawai' => $jenisPegawai,
                            'pangkat' => $pangkat,
                            'tempat_lahir' => $tempatLahir,
                            'tanggal_lahir' => $tglLahirParsed,
                            'no_telepon' => $noTelepon,
                            'tanggal_masuk' => $tglMasukParsed,
                            'sisa_cuti_tahunan' => $sisaCuti,
                            'jenis_kelamin' => in_array($jenisKelamin, ['L', 'P']) ? $jenisKelamin : 'L',
                            'is_active' => true,
                        ]);
                    }
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Baris NIP {$nip}: Gagal menyimpan data. " . $e->getMessage();
                    $errorCount++;
                }
            }

            \Illuminate\Support\Facades\DB::commit();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            fclose($handle);
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat melakukan import: ' . $e->getMessage());
        }

        fclose($handle);

        $msg = "Import selesai. {$successCount} pegawai berhasil di-import.";
        if ($errorCount > 0) {
            $msg .= " {$errorCount} baris gagal. Detail kesalahan: " . implode(', ', array_slice($errors, 0, 5));
            return redirect()->back()->with('warning', $msg);
        }

        return redirect()->back()->with('success', $msg);
    }

    private function parseDate(string $dateStr): string
    {
        $dateStr = trim($dateStr);
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
            return $dateStr;
        }

        try {
            $date = \Carbon\Carbon::createFromFormat('d/m/Y', $dateStr);
            if ($date) {
                return $date->format('Y-m-d');
            }
        } catch (\Exception $e) {}

        try {
            $date = \Carbon\Carbon::createFromFormat('d-m-Y', $dateStr);
            if ($date) {
                return $date->format('Y-m-d');
            }
        } catch (\Exception $e) {}

        throw new \Exception("Invalid date format");
    }

    public function export()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="data_pegawai_dkp.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            // Tulis UTF-8 BOM untuk kompatibilitas dengan Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, [
                'nip',
                'nama_lengkap',
                'email',
                'bidang',
                'sub_bagian',
                'jabatan',
                'jenis_pegawai',
                'pangkat',
                'tempat_lahir',
                'tanggal_lahir',
                'no_telepon',
                'tanggal_masuk',
                'sisa_cuti_tahunan',
                'jenis_kelamin'
            ], ';');

            $pegawais = Pegawai::with(['user', 'bidang', 'jabatan'])->get();

            foreach ($pegawais as $p) {
                fputcsv($file, [
                    $p->nip,
                    $p->nama_lengkap,
                    $p->user->email ?? '',
                    $p->bidang->nama_bidang ?? '',
                    $p->sub_bagian ?? '',
                    $p->jabatan->nama_jabatan ?? '',
                    $p->jenis_pegawai,
                    $p->pangkat ?? '',
                    $p->tempat_lahir ?? '',
                    $p->tanggal_lahir ? $p->tanggal_lahir->format('d/m/Y') : '',
                    $p->no_telepon ?? '',
                    $p->tanggal_masuk ? $p->tanggal_masuk->format('d/m/Y') : '',
                    $p->sisa_cuti_tahunan,
                    $p->jenis_kelamin
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export NIP, Nama, dan Password Default Pegawai ke Excel/CSV
     */
    public function exportAkun()
    {
        $filename = "data_akun_dan_password_pegawai_dkp_" . date('Ymd_His') . ".csv";
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            // Tulis UTF-8 BOM untuk kompatibilitas penuh dengan Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, [
                'NO',
                'NIP',
                'NAMA PEGAWAI',
                'BIDANG / UPTD',
                'SUB BAGIAN',
                'JABATAN',
                'JENIS PEGAWAI',
                'PASSWORD DEFAULT'
            ], ';');

            $pegawais = Pegawai::with(['bidang', 'jabatan'])
                ->orderBy('nama_lengkap', 'asc')
                ->get();
            
            $no = 1;
            foreach ($pegawais as $p) {
                $cleanNip = preg_replace('/\s+/', '', trim($p->nip));
                $passwordDefault = strlen($cleanNip) >= 4 ? substr($cleanNip, 0, 4) : '-';

                fputcsv($file, [
                    $no++,
                    $cleanNip,
                    $p->nama_lengkap,
                    $p->bidang->nama_bidang ?? '',
                    $p->sub_bagian ?? '',
                    $p->jabatan->nama_jabatan ?? '',
                    $p->jenis_pegawai,
                    $passwordDefault
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}