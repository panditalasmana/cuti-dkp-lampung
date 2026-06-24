<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bidang;
use App\Services\BidangService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BidangController extends Controller
{
    public function __construct(private BidangService $service) {}

    public function index(Request $request): View
    {
        $bidang = $this->service->paginate(10, $request->search ?? '');
        return view('admin.bidang.index', compact('bidang'));
    }

    public function create(): View
    {
        return view('admin.bidang.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kode_bidang'       => ['required', 'string', 'max:10', 'alpha_num'],
            'nama_bidang'       => ['required', 'string', 'max:200'],
            'kepala_bidang'     => ['nullable', 'string', 'max:200'],
            'nip_kepala_bidang' => ['nullable', 'string', 'max:18', 'digits_between:15,18'],
            'keterangan'        => ['nullable', 'string', 'max:500'],
            'is_active'         => ['boolean'],
        ]);

        $data['kode_bidang'] = strtoupper($data['kode_bidang']);

        $this->service->create($data);

        return redirect()->route('admin.bidang.index')
                         ->with('success', 'Bidang berhasil ditambahkan.');
    }

    public function show(Bidang $bidang): View
    {
        $bidang = $this->service->findById($bidang->id);
        return view('admin.bidang.show', compact('bidang'));
    }

    public function edit(Bidang $bidang): View
    {
        return view('admin.bidang.edit', compact('bidang'));
    }

    public function update(Request $request, Bidang $bidang): RedirectResponse
    {
        $data = $request->validate([
            'kode_bidang'       => ['required', 'string', 'max:10', 'alpha_num'],
            'nama_bidang'       => ['required', 'string', 'max:200'],
            'kepala_bidang'     => ['nullable', 'string', 'max:200'],
            'nip_kepala_bidang' => ['nullable', 'string', 'max:18', 'digits_between:15,18'],
            'keterangan'        => ['nullable', 'string', 'max:500'],
            'is_active'         => ['boolean'],
        ]);

        $data['kode_bidang'] = strtoupper($data['kode_bidang']);
        $data['is_active']   = $request->boolean('is_active');

        $this->service->update($bidang, $data);

        return redirect()->route('admin.bidang.index')
                         ->with('success', 'Data bidang berhasil diperbarui.');
    }

    public function destroy(Bidang $bidang): RedirectResponse
    {
        $this->service->delete($bidang);

        return redirect()->route('admin.bidang.index')
                         ->with('success', 'Bidang berhasil dihapus.');
    }
}