@extends('layouts.layout')

@section('title', 'Daftar Akun E-Izin')

@section('content')
<div class="auth-bg py-12">
    <div class="auth-card">
        <div class="flex justify-center mb-6">
            <div
                class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white text-xl font-bold shadow-lg shadow-blue-200">
                i
            </div>
        </div>

        <h2 class="text-center text-gray-900 text-2xl font-bold tracking-tight mb-2">Pendaftaran Akun</h2>
        <p class="text-center text-gray-500 text-sm mb-8">Pilih peranmu dan lengkapi data diri.</p>

        @if($errors->any())
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="space-y-5">
                <div>
                    <label
                        class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider ml-3 mb-1">Daftar
                        Sebagai</label>
                    <div class="relative">
                        <select name="role" id="roleSelect" class="auth-input appearance-none cursor-pointer pr-10"
                            required>
                            <option value="siswa" {{ old('role', 'siswa') === 'siswa' ? 'selected' : '' }}>Siswa</option>
                            <option value="guru" {{ old('role') === 'guru' ? 'selected' : '' }}>Guru / Staff</option>
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider ml-3 mb-1">Nama
                        Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="auth-input" placeholder="Nama sesuai absen/SK" required
                        autofocus>
                </div>

                <div id="siswaSpecificFields" class="space-y-5">
                    <div>
                        <label
                            class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider ml-3 mb-1">NISN
                            (10 Digit)</label>
                        <input type="number" name="nisn" value="{{ old('nisn') }}" class="auth-input" placeholder="Masukkan 10 digit NISN"
                            oninput="if (this.value.length > 10) this.value = this.value.slice(0, 10);">
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider ml-3 mb-1">Kelas</label>
                        <select name="kelas" class="auth-input appearance-none cursor-pointer">
                            <option value="">Pilih Kelas</option>
                            <option value="X">Kelas X</option>
                            <option value="XI">Kelas XI</option>
                            <option value="XII">Kelas XII</option>
                        </select>
                    </div>
                </div>

                <div id="guruSpecificFields" class="hidden space-y-5">
                    <div>
                        <label
                            class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider ml-3 mb-1">NIP
                            (Nomor Induk Pegawai)</label>
                        <input type="number" name="nip" value="{{ old('nip') }}" class="auth-input" placeholder="Masukkan NIP resmi">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider ml-3 mb-1">Email
                            Aktif</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="auth-input" placeholder="user@sekolah.sch.id" required>
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider ml-3 mb-1">WhatsApp</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" class="auth-input" placeholder="0812..." required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider ml-3 mb-1">Password</label>
                        <input type="password" name="password" class="auth-input" placeholder="Min. 8 karakter"
                            required>
                    </div>
                    <div>
                        <label
                            class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider ml-3 mb-1">Konfirmasi</label>
                        <input type="password" name="password_confirmation" class="auth-input"
                            placeholder="Ulangi password" required>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <button type="submit" class="auth-btn">
                    Daftar Sekarang
                </button>
            </div>
        </form>

        <p class="mt-8 text-center text-sm text-gray-500">
            Sudah punya akun? <a href="{{ route('login') }}" class="auth-link">Masuk kembali</a>
        </p>
    </div>
</div>

<script>
    const roleSelect = document.getElementById('roleSelect');
    const siswaFields = document.getElementById('siswaSpecificFields');
    const guruFields = document.getElementById('guruSpecificFields');

    function syncRoleFields() {
        if (this.value === 'guru') {
            siswaFields.classList.add('hidden');
            guruFields.classList.remove('hidden');
        } else {
            siswaFields.classList.remove('hidden');
            guruFields.classList.add('hidden');
        }
    }

    roleSelect.addEventListener('change', syncRoleFields);
    syncRoleFields.call(roleSelect);
</script>
@endsection
