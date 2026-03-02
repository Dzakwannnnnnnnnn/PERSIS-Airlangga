@extends('layouts.layout')

@section('title', 'Ajukan Izin')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-10">
  <div class="ios-card p-8 md:p-10">
    <h2 class="text-3xl font-bold text-gray-900 mb-2">Form Pengajuan Izin</h2>
    <p class="text-gray-500 mb-8">Lengkapi data berikut untuk mengirim izin kepada pihak sekolah.</p>

    @if($errors->any())
    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
      <ul class="list-disc pl-5 space-y-1">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    @if($frontOfficeMode)
      <div class="mb-6 rounded-2xl border border-blue-100 bg-blue-50 px-4 py-4">
        <p class="text-sm font-semibold text-blue-700 mb-2">Tap Kartu Pelajar (Mode Front Office: ON)</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
          <input
            type="text"
            id="card_code"
            placeholder="Tempel kartu pada reader..."
            class="md:col-span-2 w-full rounded-xl border border-blue-200 bg-white px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-100">
          <button type="button" id="btn-tap"
            class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
            Isi Otomatis dari Kartu
          </button>
        </div>
        <p id="tap-hint" class="mt-2 text-xs text-gray-600">NISN, nama, dan kelas akan terisi otomatis dari kartu pelajar terdaftar dan dikunci.</p>
      </div>
    @else
      <div class="mb-6 rounded-2xl border border-gray-200 bg-gray-50 px-4 py-4">
        <p class="text-sm font-semibold text-gray-700">Mode Front Office: OFF</p>
        <p class="mt-1 text-xs text-gray-600">Fitur tap kartu dinonaktifkan. Pengajuan hanya untuk akun siswa yang sedang login.</p>
      </div>
    @endif

    <form action="{{ route('izin.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
      @csrf

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block mb-2 text-sm font-semibold text-gray-700">NISN</label>
          <input
            type="text"
            name="nisn"
            id="nisn"
            value="{{ old('nisn') }}"
            placeholder="Terisi otomatis saat tap kartu"
            class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 outline-none"
            readonly>
        </div>
        <div>
          <label class="block mb-2 text-sm font-semibold text-gray-700">Nama Siswa</label>
          <input
            type="text"
            name="nama"
            id="nama"
            value="{{ old('nama') }}"
            placeholder="Masukkan nama siswa"
            class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 outline-none focus:ring-2 focus:ring-blue-100"
            required>
        </div>
      </div>

      <div>
        <label class="block mb-2 text-sm font-semibold text-gray-700">Kelas</label>
        <input
          type="text"
          name="kelas"
          id="kelas"
          value="{{ old('kelas') }}"
          placeholder="Contoh: XI PPLG"
          class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 outline-none focus:ring-2 focus:ring-blue-100"
          required>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block mb-2 text-sm font-semibold text-gray-700">Waktu Izin</label>
          <input type="datetime-local" name="waktu_izin" value="{{ old('waktu_izin') }}"
            class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 outline-none focus:ring-2 focus:ring-blue-100"
            required>
        </div>
        <div>
          <label class="block mb-2 text-sm font-semibold text-gray-700">Jenis Izin</label>
          <select name="jenis_izin"
            class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 outline-none focus:ring-2 focus:ring-blue-100"
            required>
            <option value="">Pilih Jenis Izin</option>
            <option value="sakit" {{ old('jenis_izin')==='sakit' ? 'selected' : '' }}>Sakit</option>
            <option value="izin keluarga" {{ old('jenis_izin')==='izin keluarga' ? 'selected' : '' }}>Izin Keluarga
            </option>
            <option value="keperluan mendesak" {{ old('jenis_izin')==='keperluan mendesak' ? 'selected' : '' }}>
              Keperluan Mendesak</option>
            <option value="lainnya" {{ old('jenis_izin')==='lainnya' ? 'selected' : '' }}>Lainnya</option>
          </select>
        </div>
      </div>

      <div>
        <label class="block mb-2 text-sm font-semibold text-gray-700">Alasan Izin</label>
        <textarea name="alasan_izin" rows="4"
          class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 outline-none focus:ring-2 focus:ring-blue-100"
          placeholder="Jelaskan alasan izin secara jelas..." required>{{ old('alasan_izin') }}</textarea>
      </div>

      <div>
        <label class="block mb-2 text-sm font-semibold text-gray-700">Foto Bukti (Opsional)</label>
        <input type="file" name="bukti_foto" accept=".jpg,.jpeg,.png,image/jpeg,image/png"
          class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3">
      </div>

      <label class="flex items-start gap-3 p-4 rounded-2xl bg-blue-50 border border-blue-100 cursor-pointer">
        <input type="checkbox" name="paraf_siswa" value="1" class="mt-1 rounded border-gray-300 text-blue-600" {{
          old('paraf_siswa') ? 'checked' : '' }} required>
        <span class="text-sm text-gray-700">
          Saya menyatakan data yang saya isi benar dan ini menjadi paraf/persetujuan siswa.
        </span>
      </label>

      <button type="submit"
        class="w-full md:w-auto px-8 py-3 rounded-2xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition-all">
        Kirim Pengajuan Izin
      </button>
    </form>
  </div>
</div>

@if(session('success'))
<div id="success-modal" class="fixed inset-0 z-[10000] bg-black/40 flex items-center justify-center px-6">
  <div class="max-w-md w-full rounded-3xl bg-white p-7 shadow-2xl text-center">
    <h3 class="text-xl font-bold text-gray-900 mb-2">Pengajuan Selesai</h3>
    <p class="text-gray-600 mb-6">{{ session('success') }}</p>
    <button onclick="document.getElementById('success-modal').remove()"
      class="px-6 py-2 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700">
      Tutup
    </button>
  </div>
</div>
@endif

@if($frontOfficeMode)
  <script>
    const cardInput = document.getElementById('card_code');
    const tapBtn = document.getElementById('btn-tap');
    const tapHint = document.getElementById('tap-hint');
    const nisnInput = document.getElementById('nisn');
    const namaInput = document.getElementById('nama');
    const kelasInput = document.getElementById('kelas');
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

    async function lookupCardAndFill(rawCode = null) {
      const code = ((rawCode ?? cardInput.value) || '').trim();
      if (!code) return;
      cardInput.value = code;

      tapHint.textContent = 'Membaca data kartu...';
      tapHint.className = 'mt-2 text-xs text-blue-700';

      try {
        const response = await fetch('{{ route('izin.lookup-card') }}', {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
          },
          body: JSON.stringify({ card_code: code }),
        });

        const data = await response.json();
        if (!response.ok) {
          if (response.status === 419) {
            throw new Error('Sesi halaman sudah kedaluwarsa. Silakan refresh halaman lalu coba lagi.');
          }
          throw new Error(data.message || 'Kartu tidak dikenali.');
        }

        nisnInput.value = data.nisn || '';
        namaInput.value = data.name || '';
        kelasInput.value = data.kelas || '';
        namaInput.readOnly = true;
        kelasInput.readOnly = true;
        namaInput.classList.remove('bg-white');
        kelasInput.classList.remove('bg-white');
        namaInput.classList.add('bg-gray-50');
        kelasInput.classList.add('bg-gray-50');

        tapHint.textContent = 'Data siswa berhasil terisi otomatis.';
        tapHint.className = 'mt-2 text-xs text-green-700';
      } catch (error) {
        tapHint.textContent = error.message || 'Gagal membaca kartu.';
        tapHint.className = 'mt-2 text-xs text-red-700';
      }
    }

    tapBtn.addEventListener('click', lookupCardAndFill);
    cardInput.addEventListener('keydown', function(event) {
      if (event.key === 'Enter') {
        event.preventDefault();
        lookupCardAndFill();
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
          lookupCardAndFill(scanBuffer);
        }
        resetScanBuffer();
        return;
      }

      if (event.key.length === 1) {
        scanBuffer += event.key;
        queueScanBufferReset();
      }
    });
  </script>
@endif
@endsection
