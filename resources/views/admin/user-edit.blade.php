@extends('layouts.layout')

@section('title', 'Edit Akun User')

@section('content')
<div class="min-h-screen bg-[#F5F5F7] py-12 px-6">
  <div class="max-w-2xl mx-auto">
    <div class="mb-6">
      <a href="{{ route('admin.dashboard') }}" class="text-sm text-blue-600 hover:underline">Kembali ke Dashboard Admin</a>
    </div>

    <div class="ios-card p-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Akun User</h1>
      <p class="text-gray-500 mb-6">Perbarui informasi dasar akun yang sudah diverifikasi.</p>

      @if($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
          <ul class="list-disc pl-5 space-y-1">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="space-y-5">
        @csrf
        @method('PATCH')

        <div>
          <label class="block mb-2 text-sm font-semibold text-gray-700">Nama</label>
          <input type="text" name="name" value="{{ old('name', $user->name) }}"
            class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 outline-none focus:ring-2 focus:ring-blue-100" required>
        </div>

        <div>
          <label class="block mb-2 text-sm font-semibold text-gray-700">Email</label>
          <input type="email" name="email" value="{{ old('email', $user->email) }}"
            class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 outline-none focus:ring-2 focus:ring-blue-100" required>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block mb-2 text-sm font-semibold text-gray-700">Nomor WA</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
              class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 outline-none focus:ring-2 focus:ring-blue-100">
          </div>
          @if(strtolower($user->role ?? '') === 'siswa')
            <div>
              <label class="block mb-2 text-sm font-semibold text-gray-700">Kelas</label>
              @php
                $selectedKelas = old('kelas', $user->kelas);
                $kelasOptions = [
                  'X PPLG', 'XI PPLG', 'XII PPLG',
                  'X MPLB', 'XI MPLB', 'XII MPLB',
                  'X DKV', 'XI DKV', 'XII DKV',
                  'X TJKT', 'XI TJKT', 'XII TJKT',
                ];
              @endphp
              <select name="kelas"
                class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 outline-none focus:ring-2 focus:ring-blue-100">
                <option value="">Pilih Kelas</option>
                @foreach($kelasOptions as $kelas)
                  <option value="{{ $kelas }}" {{ $selectedKelas === $kelas ? 'selected' : '' }}>{{ $kelas }}</option>
                @endforeach
                @if($selectedKelas && !in_array($selectedKelas, $kelasOptions))
                  <option value="{{ $selectedKelas }}" selected>{{ $selectedKelas }}</option>
                @endif
              </select>
            </div>
            <div>
              <label class="block mb-2 text-sm font-semibold text-gray-700">Nomor Kartu Pelajar</label>
              <input type="text" name="card_uid" value="{{ old('card_uid', $user->card_uid) }}"
                class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 outline-none focus:ring-2 focus:ring-blue-100"
                placeholder="Contoh: 04AABB1122">
            </div>
          @endif
        </div>

        <button class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">
          Simpan Perubahan
        </button>
      </form>
    </div>
  </div>
</div>
@endsection
