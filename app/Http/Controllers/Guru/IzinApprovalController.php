<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Izin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IzinApprovalController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:pending,diterima,ditolak'],
            'tanggal_dari' => ['nullable', 'date'],
            'tanggal_sampai' => ['nullable', 'date'],
            'sort' => ['nullable', 'in:latest,oldest'],
        ]);

        $sort = $request->get('sort', 'latest');

        $izins = Izin::with('user')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim((string) $request->q);
                $query->where(function ($q) use ($term) {
                    $q->where('nama', 'like', "%{$term}%")
                        ->orWhere('kelas', 'like', "%{$term}%")
                        ->orWhere('jenis_izin', 'like', "%{$term}%")
                        ->orWhereHas('user', function ($uq) use ($term) {
                            $uq->where('name', 'like', "%{$term}%");
                        });
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('tanggal_dari'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->tanggal_dari);
            })
            ->when($request->filled('tanggal_sampai'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->tanggal_sampai);
            })
            ->orderBy('created_at', $sort === 'oldest' ? 'asc' : 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('guru.izin-index', compact('izins', 'sort'));
    }

    public function show(Izin $izin): View
    {
        $izin->load('user');

        return view('guru.izin-show', compact('izin'));
    }

    public function update(Request $request, Izin $izin): RedirectResponse
    {
        $request->validate([
            'aksi' => ['required', 'in:terima,tolak'],
            'paraf_guru' => ['nullable', 'accepted'],
        ]);

        if ($request->aksi === 'terima') {
            if (!$request->boolean('paraf_guru')) {
                return back()->withErrors([
                    'paraf_guru' => 'Paraf guru wajib dicentang jika izin diterima.',
                ]);
            }

            $izin->update([
                'status' => 'diterima',
                'paraf_guru' => true,
            ]);

            return back()->with('success', 'Pengajuan izin diterima.');
        }

        $izin->update([
            'status' => 'ditolak',
            'paraf_guru' => false,
        ]);

        return back()->with('success', 'Pengajuan izin ditolak.');
    }
}
