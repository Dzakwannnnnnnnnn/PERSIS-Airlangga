<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class RegisterController extends Controller
{
    // 1. Menampilkan halaman register
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // 2. Menangani proses pendaftaran
    public function register(Request $request)
    {
        // A. Validasi Dinamis
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:15'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:siswa,guru'],
        ];

        // Logika Kondisional: Jika siswa wajib NISN, jika guru wajib NIP
        if ($request->role === 'siswa') {
            if (!Schema::hasColumn('users', 'card_uid')) {
                return back()
                    ->withInput()
                    ->withErrors(['card_uid' => 'Kolom nomor kartu pelajar belum tersedia di database. Jalankan migrasi terlebih dahulu.']);
            }

            $rules['nisn'] = ['required', 'digits:10', 'unique:users,nisn'];
            $rules['kelas'] = ['required', 'string', 'max:20'];
            $rules['card_uid'] = ['required', 'string', 'max:100', 'unique:users,card_uid'];
        } else {
            if (!Schema::hasColumn('users', 'nip')) {
                return back()
                    ->withInput()
                    ->withErrors(['nip' => 'Kolom NIP belum tersedia di database. Jalankan migrasi terlebih dahulu.']);
            }

            $rules['nip'] = ['required', 'numeric', 'unique:users,nip'];
        }

        $request->validate($rules);

        // B. Simpan ke Database
        $role = strtolower((string) $request->role);

        $payload = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $role,
            'nisn' => $role === 'siswa' ? $request->nisn : null,
            'kelas' => $role === 'siswa' ? $request->kelas : null,
            'is_verified' => false, // Akun baru statusnya belum terverifikasi
        ];

        if (Schema::hasColumn('users', 'card_uid')) {
            $payload['card_uid'] = $role === 'siswa' ? trim((string) $request->card_uid) : null;
        }

        if (Schema::hasColumn('users', 'nip')) {
            $payload['nip'] = $role === 'guru' ? $request->nip : null;
        }

        User::create($payload);

        return redirect()->route('login')->with(
            'status',
            'Registrasi berhasil. Akun kamu harus dikonfirmasi admin sebelum bisa login.'
        );
    }
}
