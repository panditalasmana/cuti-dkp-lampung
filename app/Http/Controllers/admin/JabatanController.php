<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use App\Services\JabatanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JabatanController extends Controller
{
    public function __construct(private JabatanService $service) {}

    public function index(Request $request): View
    {
        $jabatan = $this->service->paginate(10, $request->search ?? '');
        return view('admin.jabatan.index', compact('jabatan'));
    }

    public function create(): View
    {
        return view('admin.jabatan.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kode_jabatan' => ['required', 'string', 'max:10'],
            'nama_jabatan' => ['required', 'string', 'max:200'],
            'golongan'     => ['nullable', 'in:I,II,III,IV'],
            'eselon'       => ['nullable', 'string', 'max:10'],
            'keterangan'   => ['nullable', 'string', 'max:500'],
            'is_active'    => ['boolean'],
        ]);

        $data['kode_jabatan'] = strtoupper($data['kode_jabatan']);
        $this->service->create($data);

        return redirect()->route('admin.jabatan.index')
                         ->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function edit(Jabatan $jabatan): View
    {
        return view('admin.jabatan.edit', compact('jabatan'));
    }

    public function update(Request $request, Jabatan $jabatan): RedirectResponse
    {
        $data = $request->validate([
            'kode_jabatan' => ['required', 'string', 'max:10'],
            'nama_jabatan' => ['required', 'string', 'max:200'],
            'golongan'     => ['nullable', 'in:I,II,III,IV'],
            'eselon'       => ['nullable', 'string', 'max:10'],
            'keterangan'   => ['nullable', 'string', 'max:500'],
            'is_active'    => ['boolean'],
        ]);

        $data['kode_jabatan'] = strtoupper($data['kode_jabatan']);
        $data['is_active']    = $request->boolean('is_active');

        $this->service->update($jabatan, $data);

        return redirect()->route('admin.jabatan.index')
                         ->with('success', 'Data jabatan berhasil diperbarui.');
    }

    public function destroy(Jabatan $jabatan): RedirectResponse
    {
        $this->service->delete($jabatan);
        return redirect()->route('admin.jabatan.index')
                         ->with('success', 'Jabatan berhasil dihapus.');
    }
}