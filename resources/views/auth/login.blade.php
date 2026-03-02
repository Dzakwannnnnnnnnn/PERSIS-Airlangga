@extends('layouts.layout')

@section('title', 'Login')

@section('content')
<div class="auth-bg py-6 md:py-10">
    <div class="auth-card">
        <div class="flex justify-center mb-6">
            <div
                class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white text-xl font-bold shadow-lg shadow-blue-200">
                i
            </div>
        </div>

        <h2 class="text-center text-gray-900 text-2xl font-bold tracking-tight mb-2">Masuk ke E-Izin</h2>
        <p class="text-center text-gray-500 text-sm mb-8">
            Kelola perizinan sekolah secara digital.
        </p>

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

        @if(session('status'))
            <div class="mb-5 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="space-y-4">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider ml-3 mb-1">Email Akun</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="auth-input" placeholder="Email" required autofocus>
                    <p class="mt-1 ml-3 text-xs text-gray-500">Isi dengan email yang dipakai saat registrasi.</p>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider ml-3 mb-1">Password</label>
                    <input type="password" name="password" class="auth-input" placeholder="Password" required>
                    <p class="mt-1 ml-3 text-xs text-gray-500">Masukkan password akun kamu.</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between my-6 px-1 gap-3">
                <label class="flex items-center text-sm text-gray-600">
                    <input type="checkbox" name="remember"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                    <span class="ml-2">Ingat saya</span>
                </label>
                <a href="#" class="text-sm auth-link">Lupa sandi?</a>
            </div>

            <button type="submit" class="auth-btn">
                Masuk
            </button>
        </form>

        <div class="my-6 flex items-center gap-3">
            <div class="h-px flex-1 bg-gray-200"></div>
            <span class="text-xs uppercase tracking-wider text-gray-400 font-semibold">atau</span>
            <div class="h-px flex-1 bg-gray-200"></div>
        </div>

        <form method="POST" action="{{ route('login.tap') }}" id="tap-login-form">
            @csrf
            <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider ml-1 mb-2">
                Tap Kartu Pelajar
            </label>
            <input
                type="text"
                name="card_code"
                id="card_code"
                class="auth-input"
                placeholder="Tempel kartu pada reader..."
                autocomplete="off"
                required>
            <p class="mt-2 text-xs text-gray-500">Tap kartu pelajar terdaftar. Kode kartu akan terbaca otomatis oleh reader.</p>
            <button type="submit" class="auth-btn mt-4">
                Login dengan Kartu
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-gray-500">
            Belum punya akun?
            <a href="{{ route('register') }}" class="auth-link">Daftar sekarang</a>
        </p>
    </div>
</div>

<script>
    const cardInput = document.getElementById('card_code');
    const tapLoginForm = document.getElementById('tap-login-form');

    let scanBuffer = '';
    let lastScanKeyAt = 0;
    let scanResetTimer = null;

    function resetScanBuffer() {
        scanBuffer = '';
        if (scanResetTimer) {
            clearTimeout(scanResetTimer);
            scanResetTimer = null;
        }
    }

    function queueScanBufferReset() {
        if (scanResetTimer) {
            clearTimeout(scanResetTimer);
        }

        scanResetTimer = setTimeout(resetScanBuffer, 150);
    }

    function submitCardLoginFromScan(code) {
        if (!cardInput || !tapLoginForm) return;
        const finalCode = (code || '').trim();
        if (!finalCode) return;

        cardInput.value = finalCode;
        tapLoginForm.submit();
    }

    if (cardInput) {
        cardInput.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                submitCardLoginFromScan(cardInput.value);
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.ctrlKey || event.altKey || event.metaKey) return;

            const now = Date.now();
            if (now - lastScanKeyAt > 120) {
                resetScanBuffer();
            }
            lastScanKeyAt = now;

            if (event.key === 'Enter') {
                if (scanBuffer.length >= 4) {
                    event.preventDefault();
                    submitCardLoginFromScan(scanBuffer);
                }
                resetScanBuffer();
                return;
            }

            if (event.key.length === 1) {
                scanBuffer += event.key;
                queueScanBufferReset();
            }
        });
    }
</script>
@endsection
