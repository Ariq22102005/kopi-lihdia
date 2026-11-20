# Tahap 4: Reservasi meja

Extract ke root project (timpa), lalu jalankan:
  php artisan migrate
  php artisan config:clear && php artisan view:clear && php artisan route:clear

Yang baru: tabel reservations, form reservasi di Home (#reservasi), halaman admin /admin/reservations
(konfirmasi / batalkan), kartu "Reservasi Menunggu" di dashboard admin, tes tests/Feature/ReservationTest.php.

Aturan awal ada di config/kopilihdia.php bagian 'reservation':
max 12 orang, jam 09:00 sampai sebelum 21:00 tiap 60 menit, maksimal 30 hari ke depan, zona Asia/Jakarta.

File yang ikut ditimpa dan mungkin sudah lo edit: routes/web.php dan config/kopilihdia.php.
Kalau sudah diubah, gabungkan manual (tambah route /reservasi dan route admin reservations, serta blok 'reservation' di config).
