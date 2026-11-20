<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReservationController as AdminReservationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MidtransNotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// --- Publik (home dan katalog) ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/produk', [CatalogController::class, 'index'])->name('products.index');
Route::get('/produk/{product:slug}', [CatalogController::class, 'show'])->name('products.show');

// Reservasi meja (boleh tanpa login, dibatasi 6 kiriman per menit per IP)
Route::get('/reservasi/ketersediaan', [ReservationController::class, 'availability'])->name('reservations.availability');
Route::post('/reservasi', [ReservationController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('reservations.store');

// --- Webhook Midtrans (dipanggil server Midtrans, tanpa login dan tanpa CSRF) ---
Route::post('/midtrans/notification', [MidtransNotificationController::class, 'handle'])
    ->name('midtrans.notification');

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

    // Keranjang
    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::get('/keranjang/mini', [CartController::class, 'mini'])->name('cart.mini');
    Route::post('/keranjang/{product}', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/keranjang/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/keranjang/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');

    // Checkout dan pesanan
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/pesanan', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{order:order_number}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/pesanan/{order:order_number}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');

    // Pembayaran Midtrans
    Route::post('/pesanan/{order:order_number}/token', [PaymentController::class, 'token'])->name('payments.token');
    Route::post('/pesanan/{order:order_number}/cek-status', [PaymentController::class, 'check'])->name('payments.check');
});

// --- Khusus admin ---
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('products', ProductController::class)->except('show');

    // Reservasi
    Route::get('reservations', [AdminReservationController::class, 'index'])->name('reservations.index');
    Route::patch('reservations/{reservation}/status', [AdminReservationController::class, 'updateStatus'])->name('reservations.status');

    // Manajemen pesanan (admin hanya mengubah status pemrosesan)
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order:order_number}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order:order_number}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
});
