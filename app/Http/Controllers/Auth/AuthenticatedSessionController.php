<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class AuthenticatedSessionController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Proses masuk (Login).
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi Input
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Coba Login
        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ]);
        }

        $user = Auth::user();
        $role = strtolower((string) $user->role);
        if ($role !== 'admin' && !$user->is_verified) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Akun kamu belum dikonfirmasi admin.']);
        }

        // 3. Jika berhasil, buat ulang session (keamanan dari session fixation)
        $request->session()->regenerate();

        // 4. Logika Pengalihan (Redirect) berdasarkan Role
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Jika siswa, arahkan ke beranda
        if ($role === 'siswa') {
            return redirect('/');
        }

        return redirect('/');
    }

    /**
     * Proses keluar (Logout).
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Keluar dari guard auth
        Auth::guard('web')->logout();

        // Menghancurkan session user
        $request->session()->invalidate();

        // Membuat ulang token CSRF baru agar token lama tidak bisa dipakai lagi
        $request->session()->regenerateToken();

        // Redirect ke homepage dengan pesan sukses
        return redirect('/')->with('success', 'Kamu telah berhasil keluar.');
    }
}
