<?php

namespace App\Http\Controllers\Admin; // Pastikan namespace sesuai folder

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const KELAS_OPTIONS = [
        'X PPLG',
        'XI PPLG',
        'XII PPLG',
        'X MPLB',
        'XI MPLB',
        'XII MPLB',
        'X DKV',
        'XI DKV',
        'XII DKV',
        'X TJKT',
        'XI TJKT',
        'XII TJKT',
    ];

    /**
     * Menampilkan halaman utama admin (Daftar Siswa & Guru)
     */
    public function index(): View
    {
        abort_unless(strtolower((string) auth()->user()->role) === 'admin', 403);

        request()->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', 'in:siswa,guru'],
            'status' => ['nullable', 'in:pending,verified'],
            'sort' => ['nullable', 'in:latest,oldest,name_asc,name_desc'],
        ]);

        $sort = request('sort', 'latest');
        $hasCardUid = Schema::hasColumn('users', 'card_uid');

        $users = User::query()
            ->with([
                'latestIzin' => function ($query) {
                    $query->select('izins.id', 'izins.user_id', 'izins.kelas');
                },
            ])
            ->where('id', '!=', auth()->id())
            ->whereRaw('LOWER(role) != ?', ['admin'])
            ->when(request()->filled('q'), function ($query) use ($hasCardUid) {
                $term = trim((string) request('q'));
                $query->where(function ($q) use ($term, $hasCardUid) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('nisn', 'like', "%{$term}%")
                        ->orWhere('nip', 'like', "%{$term}%")
                        ->orWhere('kelas', 'like', "%{$term}%");

                    if ($hasCardUid) {
                        $q->orWhere('card_uid', 'like', "%{$term}%");
                    }
                });
            })
            ->when(request()->filled('role'), function ($query) {
                $query->whereRaw('LOWER(role) = ?', [request('role')]);
            })
            ->when(request()->filled('status'), function ($query) {
                $isVerified = request('status') === 'verified';
                $query->where('is_verified', $isVerified);
            });

        if ($sort === 'oldest') {
            $users->oldest();
        } elseif ($sort === 'name_asc') {
            $users->orderBy('name');
        } elseif ($sort === 'name_desc') {
            $users->orderByDesc('name');
        } else {
            $users->latest();
        }

        $users = $users->paginate(15)->withQueryString();

        $counts = [
            'pending' => User::whereRaw('LOWER(role) != ?', ['admin'])->where('is_verified', false)->count(),
            'total' => User::whereRaw('LOWER(role) != ?', ['admin'])->count(),
        ];

        return view('admin.dashboard', compact('users', 'counts'));
    }

    /**
     * Mengubah status is_verified menjadi true
     */
    public function verify($id): RedirectResponse
    {
        abort_unless(strtolower((string) auth()->user()->role) === 'admin', 403);

        $user = User::findOrFail($id);
        $user->update([
            'is_verified' => true
        ]);

        return back()->with('success', 'Akun ' . $user->name . ' berhasil diverifikasi!');
    }

    public function bulkVerify(Request $request): RedirectResponse
    {
        abort_unless(strtolower((string) auth()->user()->role) === 'admin', 403);

        $request->validate([
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $affected = User::whereIn('id', $request->user_ids)
            ->whereRaw('LOWER(role) != ?', ['admin'])
            ->update(['is_verified' => true]);

        return back()->with('success', $affected . ' akun berhasil diverifikasi.');
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        abort_unless(strtolower((string) auth()->user()->role) === 'admin', 403);

        $request->validate([
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $affected = User::whereIn('id', $request->user_ids)
            ->where('id', '!=', auth()->id())
            ->whereRaw('LOWER(role) != ?', ['admin'])
            ->delete();

        return back()->with('success', $affected . ' akun berhasil dihapus.');
    }

    public function show(User $user): View
    {
        abort_unless(strtolower((string) auth()->user()->role) === 'admin', 403);
        $user->loadMissing([
            'latestIzin' => function ($query) {
                $query->select('izins.id', 'izins.user_id', 'izins.kelas');
            },
        ]);

        return view('admin.user-detail', compact('user'));
    }

    public function edit(User $user): View
    {
        abort_unless(strtolower((string) auth()->user()->role) === 'admin', 403);

        return view('admin.user-edit', compact('user'));
    }

    public function create(): View
    {
        abort_unless(strtolower((string) auth()->user()->role) === 'admin', 403);

        return view('admin.user-create', [
            'kelasOptions' => self::KELAS_OPTIONS,
            'hasCardUid' => Schema::hasColumn('users', 'card_uid'),
            'hasNip' => Schema::hasColumn('users', 'nip'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(strtolower((string) auth()->user()->role) === 'admin', 403);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:siswa,guru'],
            'is_verified' => ['nullable', 'in:0,1'],
        ];

        $role = strtolower((string) $request->input('role'));
        $hasCardUid = Schema::hasColumn('users', 'card_uid');
        $hasNip = Schema::hasColumn('users', 'nip');

        if ($role === 'siswa') {
            $rules['nisn'] = ['required', 'digits:10', 'unique:users,nisn'];
            $rules['kelas'] = ['required', 'in:' . implode(',', self::KELAS_OPTIONS)];
            if ($hasCardUid) {
                $rules['card_uid'] = ['required', 'string', 'max:100', 'unique:users,card_uid'];
            }
        }

        if ($role === 'guru' && $hasNip) {
            $rules['nip'] = ['required', 'numeric', 'unique:users,nip'];
        }

        $validated = $request->validate($rules);
        $isVerified = $request->input('is_verified', '1') === '1';

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => $role,
            'is_verified' => $isVerified,
            'nisn' => $role === 'siswa' ? $validated['nisn'] : null,
            'kelas' => $role === 'siswa' ? $validated['kelas'] : null,
        ];

        if ($hasNip) {
            $payload['nip'] = $role === 'guru' ? ($validated['nip'] ?? null) : null;
        }

        if ($hasCardUid) {
            $payload['card_uid'] = $role === 'siswa'
                ? trim((string) ($validated['card_uid'] ?? ''))
                : null;
        }

        User::create($payload);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Akun user baru berhasil ditambahkan.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless(strtolower((string) auth()->user()->role) === 'admin', 403);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'kelas' => ['nullable', 'string', 'max:20'],
        ];

        $hasCardUid = Schema::hasColumn('users', 'card_uid');
        if ($hasCardUid) {
            $rules['card_uid'] = ['nullable', 'string', 'max:100', 'unique:users,card_uid,' . $user->id];
        }

        $request->validate($rules);

        $role = strtolower((string) $user->role);
        $kelas = $role === 'siswa' ? $request->kelas : null;

        $payload = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'kelas' => $kelas,
        ];

        if ($hasCardUid) {
            $cardUid = $role === 'siswa' ? trim((string) $request->card_uid) : null;
            $payload['card_uid'] = $cardUid === '' ? null : $cardUid;
        }

        $user->update($payload);

        return redirect()->route('admin.dashboard')->with('success', 'Data akun ' . $user->name . ' berhasil diperbarui.');
    }

    public function updateStatus(Request $request, User $user): RedirectResponse
    {
        abort_unless(strtolower((string) auth()->user()->role) === 'admin', 403);

        $request->validate([
            'is_verified' => ['required', 'in:0,1'],
        ]);

        $user->update([
            'is_verified' => (bool) $request->is_verified,
        ]);

        return back()->with('success', 'Status akun ' . $user->name . ' berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_unless(strtolower((string) auth()->user()->role) === 'admin', 403);

        if ((int) $user->id === (int) auth()->id()) {
            return back()->with('error', 'Akun admin yang sedang login tidak bisa dihapus.');
        }

        if (strtolower((string) $user->role) === 'admin') {
            return back()->with('error', 'Akun admin lain tidak boleh dihapus dari halaman ini.');
        }

        $name = $user->name;
        $user->delete();

        return back()->with('success', 'Akun ' . $name . ' berhasil dihapus.');
    }
}
