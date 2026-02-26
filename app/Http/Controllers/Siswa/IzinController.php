<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Izin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class IzinController extends Controller
{
    public function create()
    {
        $authUser = auth()->user();
        $frontOfficeMode = (bool) config('app.front_office_mode');

        if (strtolower((string) $authUser->role) !== 'siswa' && !$frontOfficeMode) {
            abort(403, 'Mode front office sedang nonaktif.');
        }

        $defaultUser = strtolower((string) $authUser->role) === 'siswa' ? $authUser : null;
        $defaultKelas = $defaultUser?->kelas ?: ($defaultUser ? Izin::where('user_id', $defaultUser->id)->latest()->value('kelas') : null);

        return view('pengajuan', [
            'user' => $defaultUser,
            'defaultKelas' => $defaultKelas,
            'frontOfficeMode' => $frontOfficeMode,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nisn' => ['nullable', 'string', 'exists:users,nisn'],
            'nama' => ['required', 'string', 'max:255'],
            'kelas' => ['required', 'string', 'max:20'],
            'waktu_izin' => ['required', 'date'],
            'jenis_izin' => ['required', 'in:sakit,izin keluarga,keperluan mendesak,lainnya'],
            'alasan_izin' => ['required', 'string', 'max:2000'],
            'bukti_foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'paraf_siswa' => ['accepted'],
        ]);

        $authUser = auth()->user();
        $isAuthSiswa = strtolower((string) $authUser->role) === 'siswa';
        $frontOfficeMode = (bool) config('app.front_office_mode');

        if (!$isAuthSiswa && !$frontOfficeMode) {
            abort(403, 'Mode front office sedang nonaktif.');
        }

        if ($request->filled('nisn')) {
            $siswa = User::whereRaw('LOWER(role) = ?', ['siswa'])
                ->where('nisn', $request->nisn)
                ->firstOrFail();

            if ($isAuthSiswa && (int) $authUser->id !== (int) $siswa->id) {
                abort(403);
            }

            // Mode tap kartu: data identitas wajib mengikuti akun terdaftar.
            $resolvedNama = $siswa->name;
            $resolvedKelas = (string) ($siswa->kelas ?: Izin::where('user_id', $siswa->id)->latest()->value('kelas'));
        } else {
            if (!$isAuthSiswa) {
                return back()
                    ->withInput()
                    ->withErrors(['nisn' => 'NISN hanya bisa terisi dari tap kartu untuk mode front office.']);
            }

            $siswa = $authUser;
            $resolvedNama = $request->nama;
            $resolvedKelas = $request->kelas;
        }

        $buktiPath = null;
        if ($request->hasFile('bukti_foto')) {
            $buktiPath = $request->file('bukti_foto')->store('bukti-izin', 'public');
        }

        if ((string) $siswa->kelas !== (string) $resolvedKelas) {
            $siswa->update(['kelas' => $resolvedKelas]);
        }

        Izin::create([
            'user_id' => $siswa->id,
            'nama' => $resolvedNama,
            'kelas' => $resolvedKelas,
            'waktu_izin' => $request->waktu_izin,
            'jenis_izin' => $request->jenis_izin,
            'alasan_izin' => $request->alasan_izin,
            'keterangan' => $request->alasan_izin,
            'bukti_foto' => $buktiPath,
            'paraf_siswa' => true,
            'paraf_guru' => false,
            'status' => 'pending',
        ]);

        return redirect()->route('izin.create')->with('success', 'Pengajuan telah selesai. Selanjutnya menunggu konfirmasi dari waka kesiswaan dan guru wali kelas.');
    }

    public function lookupCard(Request $request): JsonResponse
    {
        if (!config('app.front_office_mode')) {
            return response()->json([
                'message' => 'Mode front office sedang nonaktif.',
            ], 403);
        }

        $request->validate([
            'card_code' => ['required', 'string', 'max:100'],
        ]);

        $code = trim((string) $request->card_code);
        $query = User::query()->whereRaw('LOWER(role) = ?', ['siswa']);

        $query->where(function ($q) use ($code) {
            $q->where('nisn', $code);

            if (Schema::hasColumn('users', 'card_uid')) {
                $q->orWhere('card_uid', $code);
            }
        });

        $siswa = $query->first();
        if (!$siswa) {
            return response()->json([
                'message' => 'Kartu tidak dikenali.',
            ], 404);
        }

        $kelas = $siswa->kelas ?: Izin::where('user_id', $siswa->id)->latest()->value('kelas');

        return response()->json([
            'name' => $siswa->name,
            'nisn' => $siswa->nisn,
            'kelas' => $kelas,
        ]);
    }

    public function print(Izin $izin): View|RedirectResponse
    {
        abort_unless(strtolower((string) auth()->user()->role) === 'siswa', 403);

        if ((int) $izin->user_id !== (int) auth()->id()) {
            abort(403);
        }

        if ($izin->status !== 'diterima') {
            return redirect()->route('izin.status')->with('error', 'Bukti izin hanya bisa dicetak jika status sudah diterima guru.');
        }

        return view('izin-print', compact('izin'));
    }
}
