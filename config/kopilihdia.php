<?php

/*
 | Konten halaman Home. Ubah di sini tanpa menyentuh view.
 | PENTING: semua isi di bawah masih CONTOH. Ganti dengan data coffee shop yang asli.
 */
return [
    'tagline' => 'Secangkir kopi, satu tempat untuk berhenti sejenak.',

    'address' => 'Jl. Contoh No. 1, Bengkulu',
    'hours'   => 'Setiap hari, 09.00 - 22.00 WIB',
    'phone'   => '081234567890',

    'instagram' => [
        'handle' => '@kopilihdia',
        'url'    => 'https://www.instagram.com/',
    ],

    // image = foto di public/img/ (opsional, kalau file belum ada fasilitas tampil tanpa foto)
    // icon dipakai kalau suatu saat butuh ikon (nama di partials/icons.blade.php)
    'facilities' => [
        ['icon' => 'wifi',    'title' => 'WiFi Kencang', 'image' => 'fasilitas/wifi.jpg',      'text' => 'Cocok untuk kerja, kuliah, atau sekadar scroll santai.'],
        ['icon' => 'plug',    'title' => 'Colokan Banyak', 'image' => 'fasilitas/colokan.jpg',    'text' => 'Laptop dan HP tetap hidup selama kamu betah duduk.'],
        ['icon' => 'wind',    'title' => 'Ruang Ber-AC', 'image' => 'fasilitas/ruang-ac.jpg',      'text' => 'Area dalam yang sejuk dan tenang untuk ngobrol lama.'],
        ['icon' => 'sun',     'title' => 'Area Outdoor', 'image' => 'fasilitas/outdoor.jpg',      'text' => 'Udara terbuka untuk kamu yang suka nongkrong di luar.'],
        ['icon' => 'parking', 'title' => 'Parkir Luas', 'image' => 'fasilitas/parkir.jpg',       'text' => 'Motor dan mobil, tanpa pusing cari tempat.'],
        ['icon' => 'moon',    'title' => 'Mushola', 'image' => 'fasilitas/mushola.jpg',           'text' => 'Ibadah tetap nyaman tanpa harus pergi jauh.'],
    ],

    /*
     | Galeri suasana. Taruh foto di public/img/galeri/ lalu sesuaikan nama file di bawah.
     | size: normal | tall | wide. Kalau file belum ada, tampil kotak placeholder.
     */
    'gallery' => [
        ['file' => 'galeri/suasana-1.jpg', 'alt' => 'Area dalam coffee shop',    'size' => 'tall'],
        ['file' => 'galeri/suasana-2.jpg', 'alt' => 'Barista meracik kopi',      'size' => 'normal'],
        ['file' => 'galeri/suasana-3.jpg', 'alt' => 'Meja dan kursi kayu',       'size' => 'normal'],
        ['file' => 'galeri/suasana-4.jpg', 'alt' => 'Area outdoor',              'size' => 'wide'],
        ['file' => 'galeri/suasana-5.jpg', 'alt' => 'Secangkir kopi di meja',    'size' => 'normal'],
        ['file' => 'galeri/suasana-6.jpg', 'alt' => 'Pelanggan sedang bersantai','size' => 'tall'],
    ],

    /*
     | Testimoni. 'sample' => true menampilkan label "Contoh" di kartu.
     | Ganti dengan testimoni asli, lalu ubah sample jadi false (atau hapus barisnya).
     */
    'testimonials' => [
        ['name' => 'Nama Pelanggan', 'role' => 'Pelanggan setia', 'rating' => 5, 'sample' => true,
         'text' => 'Tempatnya nyaman, kopinya pas, dan pesan online-nya gampang banget. Jadi langganan kalau lagi butuh tempat kerja.'],
        ['name' => 'Nama Pelanggan', 'role' => 'Mahasiswa', 'rating' => 5, 'sample' => true,
         'text' => 'WiFi kencang dan colokan banyak. Cocok buat ngerjain tugas berjam-jam tanpa merasa diburu-buru.'],
        ['name' => 'Nama Pelanggan', 'role' => 'Pekerja kantoran', 'rating' => 4, 'sample' => true,
         'text' => 'Pesan dari kantor, sampai rumah tinggal ambil. Pembayarannya juga praktis lewat e-wallet.'],
        ['name' => 'Nama Pelanggan', 'role' => 'Pelanggan baru', 'rating' => 5, 'sample' => true,
         'text' => 'Suasananya hangat, pelayanannya ramah. Kopi susunya jadi favorit baru aku.'],
        ['name' => 'Nama Pelanggan', 'role' => 'Komunitas lokal', 'rating' => 5, 'sample' => true,
         'text' => 'Sering kumpul bareng teman-teman di sini. Area outdoor-nya enak buat ngobrol sampai malam.'],
    ],

    /*
     | Aturan reservasi meja (nilai awal, silakan diubah).
     | Jam yang bisa dipesan dibuat otomatis dari open sampai sebelum close, tiap 'interval' menit.
     */
    'reservation' => [
        'max_guests' => 12,
        // Total kursi yang bisa dipesan per jam (reservasi yang dibatalkan tidak dihitung)
        'capacity_per_slot' => 20,
        'open'       => '09:00',
        'close'      => '21:00',
        'interval'   => 60,
        'days_ahead' => 30,
        'timezone'   => 'Asia/Jakarta',
    ],
];
