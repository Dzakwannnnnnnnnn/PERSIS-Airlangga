@extends('layouts.layout')

@section('title', 'Dashboard Admin')

@section('content')
<div class="min-h-screen bg-[#F5F5F7] py-12 px-6">
  <div class="max-w-6xl mx-auto">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
      <div>
        <h1 class="text-4xl font-bold text-gray-900 tracking-tight">Manajemen Pengguna</h1>
        <p class="text-gray-500 mt-2 text-lg">Verifikasi, cari, sortir, edit, dan hapus akun user.</p>
      </div>
      <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('admin.users.create') }}" class="rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700">
          + Tambah User
        </a>
        <div class="bg-white px-6 py-3 rounded-2xl shadow-sm border border-gray-100 text-center">
          <span class="block text-2xl font-bold text-blue-600">{{ $counts['pending'] }}</span>
          <span class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Menunggu</span>
        </div>
        <div class="bg-white px-6 py-3 rounded-2xl shadow-sm border border-gray-100 text-center">
          <span class="block text-2xl font-bold text-gray-900">{{ $counts['total'] }}</span>
          <span class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Total User</span>
        </div>
      </div>
    </div>

    @if(session('success'))
      <div class="mb-5 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 font-semibold">
        {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 font-semibold">
        {{ session('error') }}
      </div>
    @endif

    <div class="ios-card p-6 mb-5">
      <form method="GET" action="{{ route('admin.dashboard') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
        <div class="md:col-span-2">
          <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama/email/nisn/kelas"
            class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
          <select name="role" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-100">
            <option value="">Semua Role</option>
            <option value="siswa" {{ request('role') === 'siswa' ? 'selected' : '' }}>Siswa</option>
            <option value="guru" {{ request('role') === 'guru' ? 'selected' : '' }}>Guru</option>
          </select>
        </div>
        <div>
          <select name="status" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-100">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Terverifikasi</option>
          </select>
        </div>
        <div>
          <select name="sort" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-100">
            <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Terbaru</option>
            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Terlama</option>
            <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Nama A-Z</option>
            <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Nama Z-A</option>
          </select>
        </div>
        <div class="md:col-span-5 flex gap-2">
          <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Terapkan</button>
          <a href="{{ route('admin.dashboard') }}" class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Reset</a>
        </div>
      </form>
    </div>

    <div class="ios-card p-0 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr class="text-left text-gray-600">
              <th class="px-4 py-3">
                <input type="checkbox" id="check-all" class="rounded border-gray-300 text-blue-600">
              </th>
              <th class="px-4 py-3 font-semibold">Nama</th>
              <th class="px-4 py-3 font-semibold">Role</th>
              <th class="px-4 py-3 font-semibold">Kontak</th>
              <th class="px-4 py-3 font-semibold">Status</th>
              <th class="px-4 py-3 font-semibold">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 bg-white">
            @forelse($users as $user)
              <tr>
                <td class="px-4 py-3">
                  <input type="checkbox" value="{{ $user->id }}" class="row-check rounded border-gray-300 text-blue-600">
                </td>
                <td class="px-4 py-3">
                  <div class="font-semibold text-gray-900">{{ $user->name }}</div>
                  <div class="text-xs text-gray-500">{{ $user->email }}</div>
                  <div class="text-xs text-gray-400">NISN: {{ $user->nisn ?? '-' }} | NIP: {{ $user->nip ?? '-' }}</div>
                </td>
                <td class="px-4 py-3 uppercase text-xs font-bold text-gray-600">{{ $user->role }}</td>
                <td class="px-4 py-3 text-xs text-gray-600">
                  <div>{{ $user->phone ?? '-' }}</div>
                  <div>Kelas: {{ $user->display_kelas }}</div>
                </td>
                <td class="px-4 py-3">
                  @if($user->is_verified)
                    <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-bold text-green-700">Terverifikasi</span>
                  @else
                    <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700">Pending</span>
                  @endif
                </td>
                <td class="px-4 py-3">
                  @if(!$user->is_verified)
                    <form action="{{ route('admin.verify', $user->id) }}" method="POST">
                      @csrf
                      <button class="rounded-xl bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">
                        Verifikasi
                      </button>
                    </form>
                  @else
                    <div class="flex items-center gap-2">
                      <a href="{{ route('admin.users.show', $user->id) }}" class="rounded-xl border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">Detail</a>
                      <a href="{{ route('admin.users.edit', $user->id) }}" class="rounded-xl bg-amber-500 px-3 py-2 text-xs font-semibold text-white hover:bg-amber-600">Edit</a>
                      <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="js-delete-form" data-user-name="{{ $user->name }}">
                        @csrf
                        @method('DELETE')
                        <button class="rounded-xl bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700">Hapus</button>
                      </form>
                    </div>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-500">Data user tidak ditemukan.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-4 flex items-center justify-between gap-3">
      <div class="flex items-center gap-2">
        <button type="button" id="bulk-verify-btn" disabled class="rounded-xl bg-black px-4 py-2 text-sm font-semibold text-white hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-40">
          Verifikasi Akun Terpilih
        </button>
        <button type="button" id="bulk-delete-btn" disabled class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-40">
          Hapus Akun Terpilih
        </button>
      </div>
      <div>
        {{ $users->links() }}
      </div>
    </div>

    <form method="POST" action="{{ route('admin.users.bulk-verify') }}" id="bulk-verify-form" class="hidden">
      @csrf
      <div id="bulk-ids-wrapper"></div>
    </form>

    <form method="POST" action="{{ route('admin.users.bulk-delete') }}" id="bulk-delete-form" class="hidden">
      @csrf
      <div id="bulk-delete-ids-wrapper"></div>
    </form>
  </div>
</div>

<div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4">
  <div class="w-full max-w-md rounded-2xl bg-white p-5 shadow-xl">
    <h3 class="text-lg font-bold text-gray-900">Konfirmasi Hapus</h3>
    <p id="delete-modal-message" class="mt-2 text-sm text-gray-600"></p>
    <div class="mt-5 flex justify-end gap-2">
      <button type="button" id="delete-cancel-btn" class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</button>
      <button type="button" id="delete-confirm-btn" class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Ya, Hapus</button>
    </div>
  </div>
</div>

<script>
  const checkAll = document.getElementById('check-all');
  const rowChecks = document.querySelectorAll('.row-check');
  const bulkBtn = document.getElementById('bulk-verify-btn');
  const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
  const bulkForm = document.getElementById('bulk-verify-form');
  const bulkIdsWrapper = document.getElementById('bulk-ids-wrapper');
  const bulkDeleteForm = document.getElementById('bulk-delete-form');
  const bulkDeleteIdsWrapper = document.getElementById('bulk-delete-ids-wrapper');
  const deleteModal = document.getElementById('delete-modal');
  const deleteModalMessage = document.getElementById('delete-modal-message');
  const deleteCancelBtn = document.getElementById('delete-cancel-btn');
  const deleteConfirmBtn = document.getElementById('delete-confirm-btn');
  const singleDeleteForms = document.querySelectorAll('.js-delete-form');
  let pendingDeleteAction = null;

  function openDeleteModal(message, onConfirm) {
    pendingDeleteAction = onConfirm;
    deleteModalMessage.textContent = message;
    deleteModal.classList.remove('hidden');
    deleteModal.classList.add('flex');
  }

  function closeDeleteModal() {
    pendingDeleteAction = null;
    deleteModal.classList.add('hidden');
    deleteModal.classList.remove('flex');
  }

  if (deleteCancelBtn) {
    deleteCancelBtn.addEventListener('click', closeDeleteModal);
  }

  if (deleteConfirmBtn) {
    deleteConfirmBtn.addEventListener('click', function () {
      if (typeof pendingDeleteAction === 'function') {
        pendingDeleteAction();
      }
      closeDeleteModal();
    });
  }
  
  if (deleteModal) {
    deleteModal.addEventListener('click', function (event) {
      if (event.target === deleteModal) {
        closeDeleteModal();
      }
    });
  }
  
  function syncBulkActionState() {
    const selectedCount = Array.from(document.querySelectorAll('.row-check:checked')).length;
    const hasSelection = selectedCount > 0;

    if (bulkBtn) {
      bulkBtn.disabled = !hasSelection;
    }
    if (bulkDeleteBtn) {
      bulkDeleteBtn.disabled = !hasSelection;
    }
  }

  if (checkAll) {
    checkAll.addEventListener('change', function() {
      rowChecks.forEach((box) => {
        box.checked = checkAll.checked;
      });
      syncBulkActionState();
    });
  }

  rowChecks.forEach((box) => {
    box.addEventListener('change', function () {
      const checkedCount = Array.from(document.querySelectorAll('.row-check:checked')).length;
      checkAll.checked = checkedCount === rowChecks.length && rowChecks.length > 0;
      syncBulkActionState();
    });
  });

  singleDeleteForms.forEach((form) => {
    form.addEventListener('submit', function (event) {
      event.preventDefault();
      const userName = form.dataset.userName || 'akun ini';
      openDeleteModal(`Yakin ingin menghapus akun ${userName}? Aksi ini tidak bisa dibatalkan.`, function () {
        form.submit();
      });
    });
  });

  if (bulkBtn) {
    bulkBtn.addEventListener('click', function() {
      const selected = Array.from(document.querySelectorAll('.row-check:checked'));
      if (!selected.length) {
        alert('Pilih minimal satu akun.');
        return;
      }

      bulkIdsWrapper.innerHTML = '';
      selected.forEach((box) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'user_ids[]';
        input.value = box.value;
        bulkIdsWrapper.appendChild(input);
      });

      bulkForm.submit();
    });
  }

  if (bulkDeleteBtn) {
    bulkDeleteBtn.addEventListener('click', function() {
      const selected = Array.from(document.querySelectorAll('.row-check:checked'));
      if (!selected.length) {
        alert('Pilih minimal satu akun.');
        return;
      }

      bulkDeleteIdsWrapper.innerHTML = '';
      selected.forEach((box) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'user_ids[]';
        input.value = box.value;
        bulkDeleteIdsWrapper.appendChild(input);
      });
      openDeleteModal(`Yakin ingin menghapus ${selected.length} akun terpilih? Aksi ini tidak bisa dibatalkan.`, function () {
        bulkDeleteForm.submit();
      });
    });
  }

  syncBulkActionState();
</script>
@endsection
