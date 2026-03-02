@extends('layouts.layout')

@section('title', 'Tambah Akun User')

@section('content')
<div class="min-h-screen bg-[#F5F5F7] py-12 px-6">
  <div class="mx-auto max-w-2xl">
    <div class="mb-6">
      <a href="{{ route('admin.dashboard') }}" class="text-sm text-blue-600 hover:underline">Kembali ke Dashboard Admin</a>
    </div>

    <div class="ios-card p-8">
      <h1 class="mb-2 text-3xl font-bold text-gray-900">Tambah Akun User</h1>
      <p class="mb-6 text-gray-500">Admin bisa membuat akun siswa atau guru secara manual.</p>

      @if($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
          <ul class="list-disc space-y-1 pl-5">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
        @csrf

        <div>
          <label class="mb-2 block text-sm font-semibold text-gray-700">Role</label>
          <select name="role" id="roleSelect"
            class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 outline-none focus:ring-2 focus:ring-blue-100"
            required>
            <option value="siswa" {{ old('role', 'siswa') === 'siswa' ? 'selected' : '' }}>Siswa</option>
            <option value="guru" {{ old('role') === 'guru' ? 'selected' : '' }}>Guru</option>
          </select>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">Nama</label>
            <input type="text" name="name" value="{{ old('name') }}"
              class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 outline-none focus:ring-2 focus:ring-blue-100"
              required>
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">Email</label>
            <input type="email" name="email" value="{{ old('email') }}"
              class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 outline-none focus:ring-2 focus:ring-blue-100"
              required>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">Nomor WA</label>
            <input type="text" name="phone" value="{{ old('phone') }}"
              class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 outline-none focus:ring-2 focus:ring-blue-100"
              required>
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">Status Verifikasi</label>
            <select name="is_verified"
              class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 outline-none focus:ring-2 focus:ring-blue-100">
              <option value="1" {{ old('is_verified', '1') === '1' ? 'selected' : '' }}>Terverifikasi</option>
              <option value="0" {{ old('is_verified') === '0' ? 'selected' : '' }}>Pending</option>
            </select>
          </div>
        </div>

        <div id="siswaFields" class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">NISN (10 digit)</label>
            <input type="text" name="nisn" value="{{ old('nisn') }}"
              class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 outline-none focus:ring-2 focus:ring-blue-100"
              maxlength="10">
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">Kelas</label>
            <select name="kelas" id="kelasInput"
              class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 outline-none focus:ring-2 focus:ring-blue-100">
              <option value="">Pilih Kelas</option>
              @foreach($kelasOptions as $kelas)
                <option value="{{ $kelas }}" {{ old('kelas') === $kelas ? 'selected' : '' }}>{{ $kelas }}</option>
              @endforeach
            </select>
          </div>

          @if($hasCardUid)
            <div class="md:col-span-2">
              <label class="mb-2 block text-sm font-semibold text-gray-700">Nomor Kartu Pelajar</label>
              <input type="text" name="card_uid" value="{{ old('card_uid') }}"
                class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 outline-none focus:ring-2 focus:ring-blue-100"
                placeholder="Contoh: 04AABB1122">
            </div>
          @endif
        </div>

        <div id="guruFields" class="hidden">
          @if($hasNip)
            <label class="mb-2 block text-sm font-semibold text-gray-700">NIP</label>
            <input type="text" name="nip" value="{{ old('nip') }}"
              class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 outline-none focus:ring-2 focus:ring-blue-100">
          @endif
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">Password</label>
            <input type="password" name="password"
              class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 outline-none focus:ring-2 focus:ring-blue-100"
              required>
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">Konfirmasi Password</label>
            <input type="password" name="password_confirmation"
              class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 outline-none focus:ring-2 focus:ring-blue-100"
              required>
          </div>
        </div>

        <button type="submit" class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">
          Simpan Akun
        </button>
      </form>
    </div>
  </div>
</div>

<script>
  const roleSelect = document.getElementById('roleSelect');
  const siswaFields = document.getElementById('siswaFields');
  const guruFields = document.getElementById('guruFields');
  const nisnInput = document.querySelector('input[name="nisn"]');
  const kelasInput = document.getElementById('kelasInput');
  const cardUidInput = document.querySelector('input[name="card_uid"]');
  const nipInput = document.querySelector('input[name="nip"]');

  function syncRoleFields() {
    if (!roleSelect) return;

    const isGuru = roleSelect.value === 'guru';
    if (siswaFields) siswaFields.classList.toggle('hidden', isGuru);
    if (guruFields) guruFields.classList.toggle('hidden', !isGuru);

    if (nisnInput) nisnInput.required = !isGuru;
    if (kelasInput) kelasInput.required = !isGuru;
    if (cardUidInput) cardUidInput.required = !isGuru;
    if (nipInput) nipInput.required = isGuru;
  }

  if (roleSelect) {
    roleSelect.addEventListener('change', syncRoleFields);
    syncRoleFields();
  }
</script>
@endsection
