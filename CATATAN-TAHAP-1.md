# Tahap 1: Loader + Home + Fasilitas

Extract ke root project (timpa file yang sama), lalu: php artisan config:clear && php artisan view:clear && php artisan route:clear
Tidak ada migrasi di tahap ini.

Edit isi halaman Home di config/kopilihdia.php (alamat, jam buka, IG, fasilitas). Semua isinya masih CONTOH.

Catatan: routes/web.php ikut ditimpa. Kalau lo pernah mengubahnya, cukup ganti baris '/' menjadi:
Route::get('/', [HomeController::class, 'index'])->name('home');
dan tambah: use App\Http\Controllers\HomeController;
