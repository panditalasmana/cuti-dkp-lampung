<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HariLibur;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HariLiburController extends Controller
{
    public function index(Request $request): View
    {
        $tahun = $request->get('tahun', date('Y'));
        
        $query = HariLibur::query();
        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }

        $hariLibur = $query->orderBy('tanggal', 'asc')->paginate(20)->withQueryString();
        
        $startYear = 2026;
        $currentYear = max($startYear, now()->year);
        $tahunList = range($currentYear + 1, $startYear);

        return view('admin.hari_libur.index', compact('hariLibur', 'tahun', 'tahunList'));
    }

    public function create(): View
    {
        return view('admin.hari_libur.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal'         => 'required|date|unique:hari_libur,tanggal',
            'keterangan'      => 'required|string|max:255',
            'is_cuti_bersama' => 'nullable|boolean',
        ]);

        $validated['is_cuti_bersama'] = $request->has('is_cuti_bersama');

        HariLibur::create($validated);

        return redirect()->route('admin.hari-libur.index')
            ->with('success', 'Hari Libur / Cuti Bersama berhasil ditambahkan.');
    }

    public function edit(HariLibur $hariLibur): View
    {
        return view('admin.hari_libur.edit', compact('hariLibur'));
    }

    public function update(Request $request, HariLibur $hariLibur): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal'         => 'required|date|unique:hari_libur,tanggal,' . $hariLibur->id,
            'keterangan'      => 'required|string|max:255',
            'is_cuti_bersama' => 'nullable|boolean',
        ]);

        $validated['is_cuti_bersama'] = $request->has('is_cuti_bersama');

        $hariLibur->update($validated);

        return redirect()->route('admin.hari-libur.index')
            ->with('success', 'Data Hari Libur / Cuti Bersama berhasil diperbarui.');
    }

    public function destroy(HariLibur $hariLibur): RedirectResponse
    {
        $hariLibur->delete();

        return redirect()->route('admin.hari-libur.index')
            ->with('success', 'Hari Libur berhasil dihapus.');
    }
}
