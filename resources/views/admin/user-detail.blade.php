@extends('layouts.layout')

@section('title', 'Detail Akun User')

@section('content')
<div class="min-h-screen bg-[#F5F5F7] py-12 px-6">
  <div class="max-w-3xl mx-auto">
    <div class="mb-6">
      <a href="{{ route('admin.dashboard') }}" class="text-sm text-blue-600 hover:underline">Kembali ke Dashboard Admin</a>
    </div>

    <div class="ios-card p-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-2">Detail Akun</h1>
      <p class="text-gray-500 mb-8">Informasi lengkap akun pengguna.</p>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">
        <div>
          <p class="text-gray-400 uppercase text-[11px] font-semibold tracking-wider mb-1">Nama</p>
          <p class="font-semibold text-gray-900">{{ $user->name }}</p>
        </div>
        <div>
          <p class="text-gray-400 uppercase text-[11px] font-semibold tracking-wider mb-1">Email</p>
          <p class="font-semibold text-gray-900">{{ $user->email }}</p>
        </div>
        <div>
          <p class="text-gray-400 uppercase text-[11px] font-semibold tracking-wider mb-1">Role</p>
          <p class="font-semibold text-gray-900">{{ strtoupper($user->role ?? '-') }}</p>
        </div>
        <div>
          <p class="text-gray-400 uppercase text-[11px] font-semibold tracking-wider mb-1">Status Akun</p>
          @if($user->is_verified)
            <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700">AKTIF</span>
          @else
            <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">PENDING</span>
          @endif
        </div>
        <div>
          <p class="text-gray-400 uppercase text-[11px] font-semibold tracking-wider mb-1">NISN</p>
          <p class="font-semibold text-gray-900">{{ $user->nisn ?? '-' }}</p>
        </div>
        <div>
          <p class="text-gray-400 uppercase text-[11px] font-semibold tracking-wider mb-1">No. Kartu Pelajar</p>
          <p class="font-semibold text-gray-900">{{ $user->card_uid ?? '-' }}</p>
        </div>
        <div>
          <p class="text-gray-400 uppercase text-[11px] font-semibold tracking-wider mb-1">NIP</p>
          <p class="font-semibold text-gray-900">{{ $user->nip ?? '-' }}</p>
        </div>
        <div>
          <p class="text-gray-400 uppercase text-[11px] font-semibold tracking-wider mb-1">Kelas</p>
          <p class="font-semibold text-gray-900">{{ $user->display_kelas }}</p>
        </div>
        <div>
          <p class="text-gray-400 uppercase text-[11px] font-semibold tracking-wider mb-1">Nomor WA</p>
          <p class="font-semibold text-gray-900">{{ $user->phone ?? '-' }}</p>
        </div>
        <div>
          <p class="text-gray-400 uppercase text-[11px] font-semibold tracking-wider mb-1">Tanggal Daftar</p>
          <p class="font-semibold text-gray-900">{{ $user->created_at?->format('d M Y H:i') }}</p>
        </div>
        <div>
          <p class="text-gray-400 uppercase text-[11px] font-semibold tracking-wider mb-1">Terakhir Update</p>
          <p class="font-semibold text-gray-900">{{ $user->updated_at?->format('d M Y H:i') }}</p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
