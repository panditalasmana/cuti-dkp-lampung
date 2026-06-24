<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisCuti;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JenisCutiController extends Controller
{
    public function __construct(private ActivityLogService $logService) {}

    public function index(Request $request): View
    {
        $jenisCuti = JenisCuti::when($request->search, fn($q) => $q->where('nama_cuti', 'like', "%{$request->search}%"))
                              ->withCount('pengajuanCuti')
                              ->orderBy('kode_cuti')
                              ->paginate(10)
                              ->withQueryString();
        return view('admin.jenis_cuti.index', compact('jenisCuti'));
    }

    public function create(): View
    {
        return view('admin.jenis_cuti.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kode_cuti'      => ['required', 'string', 'max:10', 'unique:jenis_cuti,kode_cuti'],
            'nama_cuti'      => ['required', 'string', 'max:200'],
            'maks_hari'      => ['nullable', 'integer', 'min:1'],
            'potong_kuota'   => ['boolean'],
            'perlu_lampiran' => ['boolean'],
            'keterangan'     => ['nullable', 'string', 'max:500'],
            'dasar_hukum'    => ['nullable', 'string', 'max:500'],
            'is_active'      => ['boolean'],
        ]);

        $data['potong_kuota']   = $request->boolean('potong_kuota');
        $data['perlu_lampiran'] = $request->boolean('perlu_lampiran');
        $data['kode_cuti']      = strtoupper($data['kode_cuti']);

        $jenis = JenisCuti::create($data);
        $this->logService->logCreate('jenis_cuti', "Menambah jenis cuti: {$jenis->nama_cuti}", $jenis);

        return redirect()->route('admin.jenis-cuti.index')
                         ->with('success', 'Jenis cuti berhasil ditambahkan.');
    }

    public function edit(JenisCuti $jenisCuti): View
    {
        return view('admin.jenis_cuti.edit', compact('jenisCuti'));
    }

    public function update(Request $request, JenisCuti $jenisCuti): RedirectResponse
    {
        $data = $request->validate([
            'kode_cuti'      => ['required', 'string', 'max:10', "unique:jenis_cuti,kode_cuti,{$jenisCuti->id}"],
            'nama_cuti'      => ['required', 'string', 'max:200'],
            'maks_hari'      => ['nullable', 'integer', 'min:1'],
            'potong_kuota'   => ['boolean'],
            'perlu_lampiran' => ['boolean'],
            'keterangan'     => ['nullable', 'string', 'max:500'],
            'dasar_hukum'    => ['nullable', 'string', 'max:500'],
            'is_active'      => ['boolean'],
        ]);

        $data['potong_kuota']   = $request->boolean('potong_kuota');
        $data['perlu_lampiran'] = $request->boolean('perlu_lampiran');
        $data['is_active']      = $request->boolean('is_active');

        $jenisCuti->update($data);
        $this->logService->logUpdate('jenis_cuti', "Mengubah jenis cuti: {$jenisCuti->nama_cuti}", $jenisCuti, [], $data);

        return redirect()->route('admin.jenis-cuti.index')
                         ->with('success', 'Jenis cuti berhasil diperbarui.');
    }

    public function destroy(JenisCuti $jenisCuti): RedirectResponse
    {
        if ($jenisCuti->pengajuanCuti()->exists()) {
            return back()->withErrors(['error' => 'Jenis cuti tidak dapat dihapus karena sudah digunakan.']);
        }

        $nama = $jenisCuti->nama_cuti;
        $jenisCuti->delete();
        $this->logService->logDelete('jenis_cuti', "Menghapus jenis cuti: {$nama}");

        return redirect()->route('admin.jenis-cuti.index')
                         ->with('success', 'Jenis cuti berhasil dihapus.');
    }
}