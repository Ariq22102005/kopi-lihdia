# Tahap 5: Halaman /produk lebih santai + kapasitas reservasi

Extract ke root project (timpa), lalu: php artisan config:clear && php artisan view:clear && php artisan route:clear
Tidak ada migrasi baru.

/produk: sapaan sesuai jam, judul yang berganti kata, placeholder pencarian mengetik sendiri, kartu miring mengikuti kursor,
foto zoom + kilau saat memuat, tombol hati (favorit, tersimpan di browser) + chip Favorit, percikan biji kopi,
foto terbang ke keranjang, tombol jadi centang, angka keranjang berdenyut, total berhitung naik.

Reservasi: kapasitas per jam (config 'capacity_per_slot' = 20 kursi). Jam yang penuh otomatis tidak bisa dipilih.
Reservasi yang dibatalkan admin otomatis membebaskan kursi.

File yang ikut ditimpa dan mungkin sudah lo edit: routes/web.php dan config/kopilihdia.php (gabungkan manual kalau perlu).
public/js/shop.js juga berubah sedikit (memicu event untuk animasi), jadi jangan lupa ikut ditimpa.
