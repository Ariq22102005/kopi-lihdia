<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// --- Publik ---
Route::get('/', fn () => redirect()->route('products.index'));

// Sementara (diganti ProductController di Tahap 5)
Route::get('/produk', fn () => view('products.index'))->name('products.index');

// --- Tamu (belum login) ---
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

// --- Sudah login (customer dan admin) ---
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
});

// --- Khusus admin ---
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Sementara (diganti DashboardController di Tahap 4)
    Route::get('/', fn () => view('admin.dashboard'))->name('dashboard');
});