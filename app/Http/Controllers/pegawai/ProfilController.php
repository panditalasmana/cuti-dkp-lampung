<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Services\PegawaiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function __construct(private PegawaiService $service) {}

    public function index(): View
    {
        $pegawai = $this->service->findByUserId(Auth::id());
        return view('pegawai.profile.index', compact('pegawai'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'alamat'     => ['nullable', 'string', 'max:500'],
            'no_telepon' => ['nullable', 'string', 'max:15'],
            'email'      => ['nullable', 'email', 'max:100'],
            'foto'       => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'hapus_foto' => ['nullable', 'boolean'],
        ]);

        $pegawai = $this->service->findByUserId(Auth::id());
        $this->service->updateProfil($pegawai, $request->all());

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function hapusFoto(): RedirectResponse
    {
        $pegawai = $this->service->findByUserId(Auth::id());
        if ($pegawai->foto) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($pegawai->foto);
            $pegawai->update(['foto' => null]);
        }
        return back()->with('success', 'Foto profil berhasil dihapus.');
    }

    public function gantiPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'password_lama'          => ['required', 'string'],
            'password_baru'          => ['required', 'string', 'min:8', 'confirmed'],
            'password_baru_confirmation' => ['required'],
        ]);

        if (!Hash::check($request->password_lama, Auth::user()->password)) {
            return back()->withErrors(['password_lama' => 'Password lama tidak sesuai.']);
        }

        $this->service->gantiPassword(Auth::user(), $request->password_baru);

        return back()->with('success', 'Password berhasil diubah.');
    }
}