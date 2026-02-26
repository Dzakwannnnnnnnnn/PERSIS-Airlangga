@extends('layouts.layout')

@section('title', 'Detail Validasi Pengajuan')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-10">
  <div class="mb-5 flex items-center justify-between gap-3">
    <div>
      <h2 class="text-3xl font-bold text-gray-900">Detail Pengajuan Izin</h2>
      <p class="text-gray-500 mt-1">Cek detail pengajuan sebelum validasi.</p>
    </div>
    <a href="{{ route('guru.izin.index') }}" class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Kembali</a>
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

  <div class="ios-card p-6">
    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
      <div>
        <dt class="text-gray-500">Nama Siswa</dt>
        <dd class="font-semibold text-gray-900">{{ $izin->nama ?? $izin->user?->name ?? '-' }}</dd>
      </div>
      <div>
        <dt class="text-gray-500">Kelas</dt>
        <dd class="font-semibold text-gray-900">{{ $izin->kelas ?? '-' }}</dd>
      </div>
      <div>
        <dt class="text-gray-500">Jenis Izin</dt>
        <dd class="font-semibold text-gray-900">{{ ucfirst($izin->jenis_izin) }}</dd>
      </div>
      <div>
        <dt class="text-gray-500">Waktu Pengajuan</dt>
        <dd class="font-semibold text-gray-900">{{ $izin->created_at?->format('d M Y H:i') }}</dd>
      </div>
      <div class="md:col-span-2">
        <dt class="text-gray-500">Alasan Izin</dt>
        <dd class="font-semibold text-gray-900">{{ $izin->alasan_izin ?? $izin->keterangan ?? '-' }}</dd>
      </div>
      <div class="md:col-span-2">
        <dt class="text-gray-500">Foto Bukti</dt>
        <dd class="font-semibold text-gray-900">
          @if($izin->bukti_foto)
            <a href="{{ '/storage/' . $izin->bukti_foto }}" target="_blank" class="text-blue-600 hover:underline">Lihat Foto Bukti</a>
          @else
            <span class="text-gray-500">Tidak ada foto bukti.</span>
          @endif
        </dd>
      </div>
      <div class="md:col-span-2">
        <dt class="text-gray-500">Status</dt>
        <dd class="mt-1">
          @if($izin->status === 'pending')
            <span class="inline-flex rounded-full bg-yellow-100 px-2.5 py-1 text-xs font-semibold text-yellow-700">Pending</span>
          @elseif($izin->status === 'diterima')
            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">Diterima</span>
          @else
            <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">Ditolak</span>
          @endif
        </dd>
      </div>
    </dl>

    @if($izin->status === 'pending')
      <form action="{{ route('guru.izin.update', $izin->id) }}" method="POST" class="mt-6 border-t border-gray-100 pt-5">
        @csrf
        @method('PATCH')
        <label class="flex items-center gap-2 text-sm text-gray-700">
          <input id="parafGuru" type="checkbox" name="paraf_guru" value="1" class="rounded border-gray-300 text-blue-600">
          <span>Saya menyetujui dan paraf sebagai guru validator.</span>
        </label>
        <div class="mt-4 flex flex-wrap gap-2">
          <button id="btnTerima" type="submit" name="aksi" value="terima" disabled class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-40">Terima</button>
          <button type="submit" name="aksi" value="tolak" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Tolak</button>
        </div>
        <p class="mt-2 text-xs text-gray-500">Tombol Terima aktif setelah paraf dicentang. Tombol Tolak tetap aktif tanpa paraf.</p>
      </form>
    @endif
  </div>
</div>

@if($izin->status === 'pending')
<script>
  const parafGuru = document.getElementById('parafGuru');
  const btnTerima = document.getElementById('btnTerima');

  if (parafGuru && btnTerima) {
    parafGuru.addEventListener('change', function () {
      btnTerima.disabled = !this.checked;
    });
  }
</script>
@endif
@endsection
