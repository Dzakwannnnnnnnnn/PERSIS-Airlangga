<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class CardLoginController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'card_code' => ['required', 'string', 'max:100'],
        ]);

        $code = trim((string) $request->card_code);

        $userQuery = User::query()->whereRaw('LOWER(role) = ?', ['siswa']);

        $userQuery->where(function ($query) use ($code) {
            $query->where('nisn', $code);

            if (Schema::hasColumn('users', 'card_uid')) {
                $query->orWhere('card_uid', $code);
            }
        });

        $user = $userQuery->first();

        if (!$user) {
            return back()->withErrors([
                'card_code' => 'Kartu pelajar tidak dikenali. Hubungi admin sekolah.',
            ]);
        }

        if (!$user->is_verified) {
            return back()->withErrors([
                'card_code' => 'Akun siswa ini belum dikonfirmasi admin.',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/')->with('card_login_success', 'Login kartu berhasil. Selamat datang, ' . $user->name . '.');
    }
}
