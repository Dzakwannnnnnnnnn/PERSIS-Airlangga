<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboard;
use App\Http\Controllers\Siswa\IzinController;
use App\Http\Controllers\Guru\IzinApprovalController;

// 1. Halaman Utama
Route::get('/', function () {
    return view('welcome');
});
Route::get('/prosedur', function () {
    return view('prosedur');
})->name('prosedur');

// 2. Auth Routes (Login, Logout, dll)
require __DIR__ . '/auth.php';

// 3. GROUP ADMIN
// Menambahkan ->name('admin.') supaya pemanggilan route jadi route('admin.dashboard')
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::post('/verify/{id}', [AdminDashboard::class, 'verify'])->name('verify');
    Route::get('/users/create', [AdminDashboard::class, 'create'])->name('users.create');
    Route::post('/users', [AdminDashboard::class, 'store'])->name('users.store');
    Route::get('/users/bulk-verify', function () {
        return redirect()->route('admin.dashboard')->with('error', 'Gunakan aksi "Verifikasi Instan (Selected)" dari dashboard admin.');
    });
    Route::post('/users/bulk-verify', [AdminDashboard::class, 'bulkVerify'])->name('users.bulk-verify');
    Route::get('/users/bulk-delete', function () {
        return redirect()->route('admin.dashboard')->with('error', 'Gunakan aksi "Hapus Selected" dari dashboard admin.');
    });
    Route::post('/users/bulk-delete', [AdminDashboard::class, 'bulkDelete'])->name('users.bulk-delete');
    Route::get('/users/{user}', [AdminDashboard::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [AdminDashboard::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}', [AdminDashboard::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/status', [AdminDashboard::class, 'updateStatus'])->name('users.status');
    Route::delete('/users/{user}', [AdminDashboard::class, 'destroy'])->name('users.destroy');
});

// 4. GROUP SISWA
Route::middleware(['auth', 'siswa'])->group(function () {
    Route::get('/dashboard', [SiswaDashboard::class, 'index'])->name('dashboard');
    Route::get('/status-pengajuan', [SiswaDashboard::class, 'index'])->name('izin.status');
});

// 5. GROUP PENGAJUAN (front office + siswa)
Route::middleware(['auth'])->group(function () {
    Route::get('/pengajuan', [IzinController::class, 'create'])->name('izin.create');
    Route::post('/pengajuan', [IzinController::class, 'store'])->name('izin.store');
    Route::get('/pengajuan/{izin}/print', [IzinController::class, 'print'])->name('izin.print');
    Route::post('/pengajuan/lookup-card', [IzinController::class, 'lookupCard'])->name('izin.lookup-card');
});

// 6. GROUP GURU
Route::middleware(['auth', 'guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/pengajuan', [IzinApprovalController::class, 'index'])->name('izin.index');
    Route::get('/pengajuan/export', [IzinApprovalController::class, 'export'])->name('izin.export');
    Route::get('/pengajuan/{izin}', [IzinApprovalController::class, 'show'])->name('izin.show');
    Route::patch('/pengajuan/{izin}', [IzinApprovalController::class, 'update'])->name('izin.update');
    Route::get('/pengajuan/{izin}/download-pdf', [IzinApprovalController::class, 'downloadPdf'])->name('izin.download-pdf');
});

// 7. PROFILE ROUTES
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
