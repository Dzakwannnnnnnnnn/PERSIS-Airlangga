@extends('layouts.layout')

@section('title', 'Selamat Datang')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[80vh] text-center px-6 hero-gradient reveal">

    <div class="max-w-4xl">
        <div
            class="animate-ios inline-block px-4 py-1.5 mb-6 text-xs font-semibold tracking-widest text-blue-600 uppercase bg-blue-50/50 border border-blue-100 rounded-full">
            E-Izin Digital Ecosystem - SMK TI Airlangga Samarinda
        </div>

        <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight text-gray-900 leading-[1.1] mb-6">
            Izin sekolah <br>
            <span class="text-blue-600">dalam satu ketukan.</span>
        </h1>

        <p class="text-lg md:text-xl text-gray-500 mb-12 max-w-2xl mx-auto leading-relaxed">
            Cara paling modern untuk mengelola perizinan siswa. <br class="hidden md:block">
            Cepat, transparan, dan sepenuhnya digital.
        </p>

        <div class="reveal reveal-delay-3">

            {{-- BELUM LOGIN --}}
            @guest
            <div class="flex justify-center">
                <a href="{{ route('login') }}"
                    class="w-full sm:w-auto px-10 py-4 bg-black text-white rounded-2xl font-semibold">
                    Mulai Mengajukan Izin
                </a>
            </div>
            @endguest


            {{-- JIKA SUDAH LOGIN --}}
            @auth
            @php $role = strtolower(auth()->user()->role ?? ''); @endphp
            <div class="flex flex-col sm:flex-row justify-center items-center gap-5">

                {{-- ADMIN --}}
                @if($role === 'admin')
                <a href="{{ route('admin.dashboard') }}"
                    class="w-full sm:w-auto px-10 py-4 bg-blue-600 text-white rounded-2xl font-semibold hover:bg-blue-700 transition-all shadow-xl shadow-blue-100 transform hover:scale-105 active:scale-95">
                    Buka Dashboard Admin
                </a>
                @endif

                {{-- SISWA --}}
                @if($role === 'siswa')
                <a href="/status-pengajuan"
                    class="w-full sm:w-auto px-10 py-4 bg-white/80 text-gray-900 rounded-2xl font-semibold hover:bg-white transition-all border border-gray-200 shadow-sm transform hover:scale-105 active:scale-95">
                    Lihat Status
                </a>

                <a href="{{ route('izin.create') }}"
                    class="w-full sm:w-auto px-10 py-4 bg-black text-white rounded-2xl font-semibold hover:opacity-80 transition-all shadow-xl shadow-gray-200 transform hover:scale-105 active:scale-95">
                    Ajukan Perizinan
                </a>
                @endif

                {{-- GURU --}}
                @if($role === 'guru')
                <a href="{{ route('guru.izin.index') }}"
                    class="w-full sm:w-auto px-10 py-4 bg-blue-600 text-white rounded-2xl font-semibold hover:bg-blue-700 transition-all shadow-xl shadow-blue-100 transform hover:scale-105 active:scale-95">
                    Validasi Pengajuan
                </a>
                @endif

            </div>
            @endauth

        </div>
    </div>
</div>

<div class="max-w-6xl mx-auto px-6 py-20 grid grid-cols-1 md:grid-cols-3 gap-8">
    <div class="ios-card p-10">
        <div class="text-3xl mb-4">⚡️</div>
        <h3 class="text-xl font-bold mb-2">Instan</h3>
        <p class="text-gray-500">Tidak perlu lagi mencari guru piket keliling sekolah.</p>
    </div>

    <div class="ios-card p-10">
        <div class="text-3xl mb-4">🛡️</div>
        <h3 class="text-xl font-bold mb-2">Aman</h3>
        <p class="text-gray-500">Data tersimpan rapi dan tidak akan hilang seperti kertas.</p>
    </div>

    <div class="ios-card p-10">
        <div class="text-3xl mb-4">📱</div>
        <h3 class="text-xl font-bold mb-2">Mobile First</h3>
        <p class="text-gray-500">Akses dari smartphone mana saja, kapan saja.</p>
    </div>
</div>

@if(session('card_login_success'))
<div id="card-login-modal" class="fixed inset-0 z-[10000] bg-black/40 flex items-center justify-center px-6">
    <div class="max-w-md w-full rounded-3xl bg-white p-7 shadow-2xl text-center">
        <h3 class="text-xl font-bold text-gray-900 mb-2">Login Berhasil</h3>
        <p class="text-gray-600 mb-6">{{ session('card_login_success') }}</p>
        <button onclick="document.getElementById('card-login-modal').remove()"
            class="px-6 py-2 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700">
            Tutup
        </button>
    </div>
</div>
@endif
@endsection
