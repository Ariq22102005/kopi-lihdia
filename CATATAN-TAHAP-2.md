# Tahap 2: Menu pilihan + Galeri suasana + tombol Instagram

Extract ke root project (timpa), lalu: php artisan config:clear && php artisan view:clear
Tidak ada migrasi.

- config/kopilihdia.php ikut ditimpa. Kalau sudah lo edit (alamat, jam, IG), salin ulang isinya, atau cukup tambahkan blok 'gallery' dari file ini.
- Foto galeri: taruh di public/img/galeri/ dengan nama suasana-1.jpg sampai suasana-6.jpg (atau ubah nama di config). Yang belum ada tampil sebagai kotak placeholder.
- Link IG: ubah 'instagram' => ['url' => ...] di config.
