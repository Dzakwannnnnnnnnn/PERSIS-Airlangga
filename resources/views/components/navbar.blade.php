<nav class="glass sticky top-0 z-50">
  <div class="max-w-6xl mx-auto px-4 md:px-6 py-4 flex justify-between items-center">
    <a href="/" class="text-xl font-bold tracking-tighter flex items-center gap-2">
      <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white text-[10px]">i</div>
      <div class="leading-tight">
        <div class="text-[17px] md:text-[18px]">E-Izin.</div>
        <div class="text-[9px] md:text-[10px] font-semibold tracking-wide text-gray-500">SMK TI Airlangga Samarinda</div>
      </div>
    </a>

    <div class="hidden md:flex items-center space-x-8 text-sm font-medium">
      <a href="/" class="hover:text-blue-600 transition-colors">Beranda</a>
      <a href="{{ route('prosedur') }}" class="hover:text-blue-600 transition-colors">Prosedur</a>

      @guest
      {{-- TAMPILAN JIKA BELUM LOGIN --}}
      <a href="{{ route('login') }}"
        class="bg-black text-white px-6 py-2 rounded-full hover:opacity-80 transition-all shadow-lg shadow-gray-200">
        Masuk
      </a>
      @else
      {{-- TAMPILAN JIKA SUDAH LOGIN --}}
      @php $role = strtolower(auth()->user()->role ?? ''); @endphp
      <div class="flex items-center gap-6">
        @if($role === 'admin')
          <a href="{{ route('admin.dashboard') }}" class="text-blue-600 font-bold italic underline-offset-4 hover:underline">
            Dashboard Admin
          </a>
        @elseif($role === 'guru')
          <a href="{{ route('guru.izin.index') }}" class="text-blue-600 font-bold underline-offset-4 hover:underline">
            Validasi Pengajuan
          </a>
        @elseif($role === 'siswa')
          <a href="/status-pengajuan" class="text-blue-600 font-bold underline-offset-4 hover:underline">
            Lihat Status
          </a>
          <a href="{{ route('izin.create') }}" class="text-blue-600 font-bold underline-offset-4 hover:underline">
            Ajukan Izin
          </a>
        @endif

        <form method="POST" action="{{ route('logout') }}" class="inline">
          @csrf
          <button type="submit"
            class="bg-red-50 text-red-600 px-5 py-2 rounded-full border border-red-100 hover:bg-red-100 transition-all font-medium active:scale-95">
            Keluar
          </button>
        </form>
      </div>
      @endguest
    </div>

    <details class="md:hidden relative">
      <summary class="list-none cursor-pointer rounded-xl border border-gray-200 bg-white/80 px-3 py-2 text-sm font-semibold text-gray-700">
        Menu
      </summary>
      <div class="absolute right-0 mt-2 w-60 rounded-2xl border border-gray-200 bg-white p-3 shadow-xl">
        <div class="flex flex-col gap-2 text-sm">
          <a href="/" class="rounded-lg px-3 py-2 hover:bg-gray-50">Beranda</a>
          <a href="{{ route('prosedur') }}" class="rounded-lg px-3 py-2 hover:bg-gray-50">Prosedur</a>

          @guest
            <a href="{{ route('login') }}" class="rounded-lg bg-black px-3 py-2 text-white text-center">Masuk</a>
          @else
            @php $role = strtolower(auth()->user()->role ?? ''); @endphp
            @if($role === 'admin')
              <a href="{{ route('admin.dashboard') }}" class="rounded-lg px-3 py-2 hover:bg-gray-50">Dashboard Admin</a>
            @elseif($role === 'guru')
              <a href="{{ route('guru.izin.index') }}" class="rounded-lg px-3 py-2 hover:bg-gray-50">Validasi Pengajuan</a>
            @elseif($role === 'siswa')
              <a href="/status-pengajuan" class="rounded-lg px-3 py-2 hover:bg-gray-50">Lihat Status</a>
              <a href="{{ route('izin.create') }}" class="rounded-lg px-3 py-2 hover:bg-gray-50">Ajukan Izin</a>
            @endif

            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="mt-2 w-full rounded-lg bg-red-50 px-3 py-2 text-red-600 border border-red-100">Keluar</button>
            </form>
          @endguest
        </div>
      </div>
    </details>
  </div>
</nav>
