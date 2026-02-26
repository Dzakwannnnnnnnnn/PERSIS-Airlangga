<?php

namespace App\Http\Controllers\Admin; // Pastikan namespace sesuai folder

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
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

        $users = User::query()
            ->with([
                'latestIzin' => function ($query) {
                    $query->select('izins.id', 'izins.user_id', 'izins.kelas');
                },
            ])
            ->where('id', '!=', auth()->id())
            ->whereRaw('LOWER(role) != ?', ['admin'])
            ->when(request()->filled('q'), function ($query) {
                $term = trim((string) request('q'));
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('nisn', 'like', "%{$term}%")
                        ->orWhere('nip', 'like', "%{$term}%")
                        ->orWhere('kelas', 'like', "%{$term}%");
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

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless(strtolower((string) auth()->user()->role) === 'admin', 403);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'kelas' => ['nullable', 'string', 'max:20'],
        ]);

        $role = strtolower((string) $user->role);
        $kelas = $role === 'siswa' ? $request->kelas : null;

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'kelas' => $kelas,
        ]);

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
