@extends('layouts.layout')

@section('title', 'Status Pengajuan')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-10">
    <div class="ios-card p-6 md:p-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Antrean Status Pengajuan Izin</h2>
        <p class="text-gray-500 mb-6">Pantau seluruh pengajuan kamu: diterima, ditolak, atau masih pending.</p>

        @if(session('success'))
            <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="ios-card p-5 mb-5">
            <form method="GET" action="{{ route('izin.status') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
                <div class="md:col-span-2">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari jenis / alasan izin"
                        class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <select name="status" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="diterima" {{ request('status') === 'diterima' ? 'selected' : '' }}>Diterima</option>
                        <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div>
                    <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <select name="sort" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Tanggal Terbaru</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Tanggal Terlama</option>
                    </select>
                </div>
                <div class="md:col-span-6 flex gap-2">
                    <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Terapkan</button>
                    <a href="{{ route('izin.status') }}" class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Reset</a>
                </div>
            </form>
        </div>

        <div class="ios-card p-0 overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr class="text-left text-gray-600">
                            <th class="px-4 py-3 font-semibold">Jenis</th>
                            <th class="px-4 py-3 font-semibold">Alasan</th>
                            <th class="px-4 py-3 font-semibold">Tanggal</th>
                            <th class="px-4 py-3 font-semibold">Status</th>
                            <th class="px-4 py-3 font-semibold">Bukti</th>
                            <th class="px-4 py-3 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($izins as $izin)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-gray-900">{{ ucfirst($izin->jenis_izin) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600 max-w-[280px] truncate">{{ $izin->alasan_izin ?? $izin->keterangan }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $izin->created_at?->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    @if($izin->status === 'pending')
                                        <span class="inline-flex rounded-full bg-yellow-100 px-2.5 py-1 text-xs font-semibold text-yellow-700">Pending</span>
                                    @elseif($izin->status === 'diterima')
                                        <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">Diterima</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">Ditolak</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($izin->bukti_foto)
                                        <a class="text-blue-600 hover:underline font-medium" target="_blank" href="{{ '/storage/' . $izin->bukti_foto }}">Lihat</a>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($izin->status === 'diterima')
                                        <a href="{{ route('izin.print', $izin->id) }}" target="_blank" class="inline-block rounded-lg bg-black px-3 py-1.5 text-xs font-semibold text-white hover:opacity-90">
                                            Cetak
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">Data pengajuan tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($izins->hasPages())
            <div class="mb-6">
                {{ $izins->links() }}
            </div>
        @endif

        <a href="/pengajuan" class="inline-block rounded-xl bg-blue-600 px-6 py-2 font-semibold text-white hover:bg-blue-700">
            Ajukan Izin Baru
        </a>
    </div>
</div>
@endsection
