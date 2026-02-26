<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\CardLoginController;

Route::middleware('guest')->group(function () {

  Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->name('login');

  Route::post('/login', [AuthenticatedSessionController::class, 'store']);
  Route::post('/login/tap', [CardLoginController::class, 'store'])->name('login.tap');

  Route::get('/register', [RegisterController::class, 'showRegistrationForm'])
    ->name('register');

  Route::post('/register', [RegisterController::class, 'register']);
});

Route::middleware('auth')->group(function () {
  Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');
});
