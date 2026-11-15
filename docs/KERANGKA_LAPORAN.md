# Kerangka Laporan Tugas Besar: KopiLihdia

## Halaman awal
- Cover (judul, nama kelompok dan anggota beserta NIM, program studi, dosen pengampu)
- Kata pengantar, daftar isi, daftar gambar, daftar tabel

## BAB I. Pendahuluan
1. Latar belakang (kenapa toko kopi online, kebutuhan pembayaran daring)
2. Rumusan masalah
3. Tujuan
4. Batasan masalah (tanpa varian produk, ongkir flat, Midtrans Sandbox)
5. Manfaat

## BAB II. Landasan Teori
- E-commerce
- Framework Laravel dan konsep MVC
- Eloquent ORM dan migration
- Autentikasi, role, dan middleware
- Payment gateway Midtrans (Snap, notifikasi HTTP, signature key)
- Git dan GitHub

## BAB III. Analisis dan Perancangan
1. Analisis kebutuhan (fungsional dan non-fungsional)
2. Use case diagram (Admin, Pelanggan, Midtrans)
3. Alur bisnis: katalog, keranjang, checkout, pembayaran, pemrosesan pesanan
4. ERD dan relasi antartabel (lihat README)
5. Struktur tabel (kolom, tipe, keterangan)
6. Rancangan antarmuka (referensi desain, palet warna krem dan hijau)
7. Keputusan desain penting:
   - Dua status terpisah (`payment_status` dan `order_status`)
   - Snapshot nama dan harga di `order_items`
   - Stok dikurangi saat order dibuat dan dikembalikan jika pembayaran gagal/kedaluwarsa
   - Total dihitung ulang di server
   - Webhook diverifikasi signature dan nominal

## BAB IV. Implementasi
Tiap subbab: penjelasan, potongan kode inti, dan screenshot.
1. Setup project dan database
2. Migration, model, relasi, seeder
3. Autentikasi dan middleware admin
4. Modul admin (dashboard, kategori, produk, pesanan)
5. Modul pelanggan (katalog, keranjang, checkout, riwayat, invoice)
6. Integrasi Midtrans (Snap token, notifikasi, cek status)
7. Pengelolaan source code dengan Git (cuplikan riwayat commit)

## BAB V. Pengujian
Tabel: No, Skenario, Langkah, Hasil diharapkan, Hasil aktual, Status.

| Skenario | Hasil diharapkan |
|---|---|
| Customer membuka `/admin` | 403 Forbidden |
| Guest membuka `/admin` | Diarahkan ke login |
| Daftar dengan role admin dari form | Tetap tercipta sebagai customer |
| Hapus kategori yang punya produk | Ditolak dengan pesan |
| Upload file PDF atau lebih dari 2 MB sebagai gambar produk | Ditolak validasi |
| Tambah ke keranjang melebihi stok | Ditolak dengan pesan stok |
| Checkout dengan total dimanipulasi | Total tetap dihitung server |
| Checkout saat stok berubah | Ditolak, tidak ada order |
| Notifikasi Midtrans dengan signature palsu | 403, status tidak berubah |
| Notifikasi dengan nominal tidak cocok | 400, status tidak berubah |
| Pembayaran berhasil | Status "Sudah Dibayar" |
| Pembayaran kedaluwarsa | Status "Kedaluwarsa", stok kembali |
| Admin ubah status pesanan belum dibayar | Ditolak |
| Admin mencoba melompati status | Ditolak |
| Customer melihat pesanan orang lain | 403 |

Sertakan juga screenshot hasil `php artisan test` (semua hijau).

## BAB VI. Penutup
- Kesimpulan
- Saran pengembangan (varian produk, ongkir via API kurir, notifikasi email, laporan penjualan)

## Daftar Pustaka
Dokumentasi Laravel, dokumentasi Midtrans, dokumentasi Bootstrap.

## Lampiran
- Pembagian tugas anggota kelompok
- Tautan repository GitHub
- Akun demo

---

## Daftar screenshot yang perlu diambil
1. Halaman katalog (semua menu, filter kategori aktif, hasil pencarian)
2. Detail produk, produk stok habis, dan badge "Sisa N"
3. Keranjang berisi beberapa item, pesan error melebihi stok
4. Form checkout dan pesan validasi
5. Popup Midtrans Snap
6. Halaman pesanan: menunggu pembayaran, sudah dibayar, kedaluwarsa
7. Riwayat pesanan dan invoice
8. Dashboard admin (6 statistik)
9. CRUD kategori (termasuk penolakan hapus) dan CRUD produk (upload gambar)
10. Daftar pesanan admin dengan filter, detail pesanan, form ubah status
11. Tabel database di HeidiSQL (`users`, `products`, `orders`, `order_items`)
12. Hasil uji signature palsu (403) dan hasil `php artisan test`
13. Halaman GitHub: daftar file dan riwayat commit
14. Tampilan di layar HP (responsif)
