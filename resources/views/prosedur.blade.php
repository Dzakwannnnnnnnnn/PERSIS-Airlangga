@extends('layouts.layout')

@section('title', 'Prosedur Aplikasi')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-10">
    <div class="ios-card p-8 md:p-10 mb-6">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">Bagaimana Aplikasi E-Izin Bekerja</h1>
        <p class="text-gray-500">Alur pengajuan izin siswa dari pengisian form hingga validasi oleh guru dan pemantauan status.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
        <div class="ios-card p-6">
            <div class="text-xs font-semibold tracking-wider text-blue-600 mb-2">LANGKAH 1</div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Siswa Mengajukan Izin</h2>
            <p class="text-sm text-gray-600">Siswa login, buka menu <strong>Ajukan Perizinan</strong>, isi data lengkap, upload bukti (opsional), lalu paraf siswa (checklist).</p>
        </div>
        <div class="ios-card p-6">
            <div class="text-xs font-semibold tracking-wider text-blue-600 mb-2">LANGKAH 2</div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Guru Memvalidasi</h2>
            <p class="text-sm text-gray-600">Guru membuka menu <strong>Validasi Pengajuan</strong>, meninjau data dan bukti, lalu memilih <strong>Terima</strong> atau <strong>Tolak</strong>.</p>
        </div>
        <div class="ios-card p-6">
            <div class="text-xs font-semibold tracking-wider text-blue-600 mb-2">LANGKAH 3</div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Status Dipantau Siswa</h2>
            <p class="text-sm text-gray-600">Siswa membuka menu <strong>Lihat Status</strong> untuk memantau antrean pengajuan: <strong>Pending</strong>, <strong>Diterima</strong>, atau <strong>Ditolak</strong>.</p>
        </div>
    </div>

    <div class="ios-card p-8">
        <h3 class="text-2xl font-bold text-gray-900 mb-4">Aturan Validasi</h3>
        <div class="space-y-3 text-sm text-gray-700">
            <p>1. Jika guru menerima pengajuan, guru wajib memberikan paraf (checklist) sebelum klik <strong>Terima</strong>.</p>
            <p>2. Jika pengajuan tidak disetujui, guru dapat langsung klik <strong>Tolak</strong>.</p>
            <p>3. Semua pengajuan tercatat otomatis berdasarkan waktu pengajuan untuk memudahkan pelacakan.</p>
        </div>
    </div>
</div>
@endsection

