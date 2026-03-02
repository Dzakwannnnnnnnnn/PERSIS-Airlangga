@extends('layouts.layout')

@section('title', 'Validasi Pengajuan Izin')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-10">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-3">
    <div>
      <h2 class="text-3xl font-bold text-gray-900">Validasi Pengajuan Izin</h2>
      <p class="text-gray-500 mt-1">Cari, filter, dan validasi pengajuan siswa dengan cepat.</p>
    </div>
  </div>

  @if(session('success'))
    <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
      {{ session('success') }}
    </div>
  @endif

  @if($errors->any())
    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
      <ul class="list-disc pl-5 space-y-1">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="ios-card p-6 mb-5">
    <form method="GET" action="{{ route('guru.izin.index') }}" class="grid grid-cols-1 md:grid-cols-8 gap-3">
      <div class="md:col-span-2">
        <input
          type="text"
          name="q"
          value="{{ request('q') }}"
          placeholder="Cari nama / jenis izin"
          class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-100">
      </div>
      <div>
        <select name="kelas" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-100">
          <option value="">Semua Kelas</option>
          @foreach($kelasOptions as $kelas)
            <option value="{{ $kelas }}" {{ request('kelas') === $kelas ? 'selected' : '' }}>{{ $kelas }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <select name="periode" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-100">
          <option value="">Semua Periode</option>
          <option value="mingguan" {{ request('periode') === 'mingguan' ? 'selected' : '' }}>Per Minggu</option>
          <option value="bulanan" {{ request('periode') === 'bulanan' ? 'selected' : '' }}>Per Bulan</option>
          <option value="tahunan" {{ request('periode') === 'tahunan' ? 'selected' : '' }}>Per Tahun</option>
        </select>
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
      <div class="md:col-span-8 flex gap-2">
        <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Terapkan</button>
        <a href="{{ route('guru.izin.index') }}" class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Reset</a>
      </div>
      <div class="md:col-span-8 flex flex-wrap gap-2 border-t border-gray-100 pt-3">
        <a href="{{ route('guru.izin.export', array_merge(request()->query(), ['format' => 'excel'])) }}"
          class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
          Export Excel
        </a>
        <a href="{{ route('guru.izin.export', array_merge(request()->query(), ['format' => 'pdf'])) }}"
          class="rounded-xl bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">
          Export PDF
        </a>
        <p class="self-center text-xs text-gray-500">Export mengikuti filter aktif (kelas, nama/kata kunci, periode, status, tanggal).</p>
      </div>
    </form>
  </div>

  <div class="ios-card p-0 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr class="text-left text-gray-600">
            <th class="px-4 py-3 font-semibold">Siswa</th>
            <th class="px-4 py-3 font-semibold">Kelas</th>
            <th class="px-4 py-3 font-semibold">Jenis</th>
            <th class="px-4 py-3 font-semibold">Tanggal</th>
            <th class="px-4 py-3 font-semibold">Status</th>
            <th class="px-4 py-3 font-semibold">Bukti</th>
            <th class="px-4 py-3 font-semibold">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
          @forelse($izins as $izin)
            <tr>
              <td class="px-4 py-3">
                <div class="font-semibold text-gray-900">{{ $izin->nama ?? $izin->user?->name ?? '-' }}</div>
                <div class="text-xs text-gray-500 truncate max-w-[220px]">{{ $izin->alasan_izin ?? $izin->keterangan }}</div>
              </td>
              <td class="px-4 py-3">{{ $izin->kelas ?? '-' }}</td>
              <td class="px-4 py-3">{{ ucfirst($izin->jenis_izin) }}</td>
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
                  <a href="{{ '/storage/' . $izin->bukti_foto }}" target="_blank" class="text-blue-600 hover:underline font-medium">Lihat</a>
                @else
                  <span class="text-gray-400">-</span>
                @endif
              </td>
              <td class="px-4 py-3">
                @if($izin->status === 'pending')
                  <a href="{{ route('guru.izin.show', $izin->id) }}" class="inline-flex rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700">Detail & Validasi</a>
                @elseif($izin->status === 'diterima')
                  <div class="flex flex-wrap gap-2">
                    <a href="{{ route('guru.izin.show', $izin->id) }}" class="inline-flex rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">Detail</a>
                    <a href="{{ route('guru.izin.download-pdf', $izin->id) }}" class="inline-flex rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700">Download PDF</a>
                  </div>
                @else
                  <a href="{{ route('guru.izin.show', $izin->id) }}" class="inline-flex rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">Detail</a>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="px-4 py-8 text-center text-gray-500">Data pengajuan tidak ditemukan.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  @if($izins->hasPages())
    <div class="mt-5">
      {{ $izins->links() }}
    </div>
  @endif
</div>
@endsection
