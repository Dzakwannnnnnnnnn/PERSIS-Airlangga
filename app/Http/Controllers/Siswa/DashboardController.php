<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Izin;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        $role = strtolower((string) auth()->user()->role);

        if ($role !== 'siswa') {
            return redirect('/');
        }

        $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:pending,diterima,ditolak'],
            'tanggal_dari' => ['nullable', 'date'],
            'tanggal_sampai' => ['nullable', 'date'],
            'sort' => ['nullable', 'in:latest,oldest'],
        ]);

        $sort = $request->get('sort', 'latest');

        $izins = Izin::where('user_id', auth()->id())
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim((string) $request->q);
                $query->where(function ($q) use ($term) {
                    $q->where('jenis_izin', 'like', "%{$term}%")
                        ->orWhere('alasan_izin', 'like', "%{$term}%")
                        ->orWhere('keterangan', 'like', "%{$term}%");
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

        return view('dashboard', compact('izins'));
    }
}
