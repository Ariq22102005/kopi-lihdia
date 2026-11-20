# Update: slot foto fasilitas

Extract ke root project (timpa), lalu: php artisan config:clear && php artisan view:clear
Taruh foto di public/img/fasilitas/ dengan nama:
wifi.jpg, colokan.jpg, ruang-ac.jpg, outdoor.jpg, parkir.jpg, mushola.jpg
(atau ubah nama file di config/kopilihdia.php bagian 'facilities' > 'image').
Fasilitas yang fotonya belum ada tampil tanpa foto. Disarankan isi semua 6 supaya rata.
Foto rasio 4:3 (landscape) paling pas. Ukuran file disarankan di bawah 500 KB per foto.
config/kopilihdia.php ikut ditimpa: kalau sudah lo edit, tambahkan baris 'image' => '...' ke tiap fasilitas saja.
