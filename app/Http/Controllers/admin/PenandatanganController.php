<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penandatangan;
use App\Services\PenandatanganService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenandatanganController extends Controller
{
    public function __construct(private PenandatanganService $service) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'kategori']);
        $penandatangan = $this->service->paginate(15, $filters);
        return view('admin.penandatangan.index', compact('penandatangan', 'filters'));
    }

    public function create(): View
    {
        return view('admin.penandatangan.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kategori'         => ['required', 'in:pejabat_wenang,atasan_langsung,eselon_4'],
            'nama'             => ['required', 'string', 'max:255'],
            'nip'              => ['required', 'string', 'max:20'],
            'jabatan'          => ['required', 'string', 'max:255'],
            'pangkat_golongan' => ['nullable', 'string', 'max:255'],
            'is_default'       => ['nullable', 'boolean'],
            'is_active'        => ['nullable', 'boolean'],
        ]);

        $this->service->store($data);

        return redirect()->route('admin.penandatangan.index')
                         ->with('success', 'Pejabat penandatangan berhasil ditambahkan.');
    }

    public function edit(Penandatangan $penandatangan): View
    {
        return view('admin.penandatangan.edit', compact('penandatangan'));
    }

    public function update(Request $request, Penandatangan $penandatangan): RedirectResponse
    {
        $data = $request->validate([
            'kategori'         => ['required', 'in:pejabat_wenang,atasan_langsung,eselon_4'],
            'nama'             => ['required', 'string', 'max:255'],
            'nip'              => ['required', 'string', 'max:20'],
            'jabatan'          => ['required', 'string', 'max:255'],
            'pangkat_golongan' => ['nullable', 'string', 'max:255'],
            'is_default'       => ['nullable', 'boolean'],
            'is_active'        => ['nullable', 'boolean'],
        ]);

        $this->service->update($penandatangan, $data);

        return redirect()->route('admin.penandatangan.index')
                         ->with('success', 'Data pejabat penandatangan berhasil diperbarui.');
    }

    public function destroy(Penandatangan $penandatangan): RedirectResponse
    {
        $nama = $penandatangan->nama;
        $this->service->delete($penandatangan);

        return redirect()->route('admin.penandatangan.index')
                         ->with('success', "Pejabat penandatangan ({$nama}) berhasil dihapus.");
    }

    public function setDefault(Penandatangan $penandatangan): RedirectResponse
    {
        $this->service->setDefault($penandatangan);

        return redirect()->route('admin.penandatangan.index')
                         ->with('success', "{$penandatangan->nama} telah dijadikan penandatangan utama (default) untuk kategori {$penandatangan->kategori_label}.");
    }
}
