# Update UI/UX KopiLihdia

Cara pakai: extract zip ini ke root project (timpa file yang sama). Tidak ada perubahan di controller, route, model, atau database.
Setelah itu jalankan: php artisan view:clear

File baru:
- public/css/kopilihdia.css  (design system, menggantikan <style> inline di layout)
- public/img/pattern-kopi-light.svg, pattern-kopi-green.svg  (pola daun + biji kopi)
- resources/views/partials/icons.blade.php  (sprite ikon garis)
- resources/views/components/icon.blade.php  (pakai: <x-icon name="cart" :size="20" />)

File diubah: layout, katalog (index, _card, show), keranjang (index, _mini), checkout, pesanan (index, show, _status), login, register, profil, admin (dashboard, kategori, produk, pesanan), public/js/shop.js

Bootstrap 5 tetap dipakai. Tidak perlu npm build.
