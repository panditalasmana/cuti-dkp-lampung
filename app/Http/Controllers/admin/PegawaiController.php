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
        $filters = $request->only(['search', 'bidang_id', 'jabatan_id', 'jenis_pegawai']);
        $pegawai = $this->service->paginate(15, $filters);
        $bidang  = $this->bidangRepo->all();
        $jabatan = $this->jabatanRepo->all();
        return view('admin.pegawai.index', compact('pegawai', 'bidang', 'jabatan'));
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
            'agama'             => ['required', 'in:Islam,Kristen,Katholik,Hindu,Budha,Konghucu'],
            'alamat'            => ['required', 'string', 'max:500'],
            'no_telepon'        => ['nullable', 'string', 'max:15'],
            'status_pernikahan' => ['required', 'in:Belum Menikah,Menikah,Janda,Duda'],
            'tanggal_masuk'     => ['required', 'date'],
            'jenis_pegawai'     => ['required', 'in:PNS,PPPK,Honorer'],
            'pangkat'           => ['nullable', 'string', 'max:100'],
            'sisa_cuti_tahunan' => ['required', 'integer', 'min:0', 'max:72'],
            'foto'              => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $this->service->create($data);

        return redirect()->route('admin.pegawai.index')
                         ->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function show(Pegawai $pegawai): View
    {
        $pegawai = $this->service->findById($pegawai->id);
        return view('admin.pegawai.show', compact('pegawai'));
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
            'agama'             => ['required', 'in:Islam,Kristen,Katholik,Hindu,Budha,Konghucu'],
            'alamat'            => ['required', 'string', 'max:500'],
            'no_telepon'        => ['nullable', 'string', 'max:15'],
            'status_pernikahan' => ['required', 'in:Belum Menikah,Menikah,Janda,Duda'],
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
}