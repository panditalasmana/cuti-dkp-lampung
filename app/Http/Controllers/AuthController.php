<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(private ActivityLogService $logService) {}

    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'nip'      => ['required', 'string', 'max:18'],
            'password' => ['required', 'string'],
        ], [
            'nip.required'      => 'NIP wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $credentials = [
            'nip'       => $request->nip,
            'password'  => $request->password,
            'is_active' => true,
        ];

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput(['nip' => $request->nip])
                ->withErrors(['nip' => 'NIP atau password salah, atau akun tidak aktif.']);
        }

        $request->session()->regenerate();


        // Update last login
        Auth::user()->update(['last_login_at' => now()]);

        $this->logService->logLogin($request->nip);

        return $this->redirectByRole();
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->logService->logLogout();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }

    private function redirectByRole(): RedirectResponse
    {
        return match (Auth::user()->role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'pegawai' => redirect()->route('pegawai.dashboard'),
            default   => redirect()->route('login'),
        };
    }
}