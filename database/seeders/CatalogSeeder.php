<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Biji Kopi' => [
                ['Arabika Gayo 250g', 85000, 25, 'Biji kopi arabika dari dataran tinggi Gayo, Aceh. Rasa fruity dengan aftertaste manis.'],
                ['Arabika Toraja 250g', 90000, 18, 'Karakter earthy dan body tebal khas Toraja.'],
                ['Robusta Lampung 250g', 55000, 40, 'Robusta pahit tegas, cocok untuk kopi susu.'],
                ['Arabika Bali Kintamani 250g', 88000, 0, 'Citrus dan floral, stok sedang habis.'],
            ],
            'Kopi Bubuk' => [
                ['Kopi Bubuk Tubruk 200g', 35000, 60, 'Giling kasar untuk seduh tubruk tradisional.'],
                ['Kopi Bubuk Espresso 200g', 48000, 30, 'Giling halus untuk mesin espresso dan moka pot.'],
                ['Kopi Bubuk Robusta Murni 200g', 32000, 2, 'Tanpa campuran, stok tinggal sedikit.'],
            ],
            'Alat Seduh' => [
                ['V60 Dripper Keramik', 120000, 15, 'Dripper keramik ukuran 02 untuk manual brew.'],
                ['Kertas Filter V60 (100 lembar)', 45000, 50, 'Filter kertas putih tanpa rasa.'],
                ['French Press 600ml', 135000, 12, 'Gelas borosilikat dengan rangka stainless.'],
                ['Moka Pot 3 Cup', 150000, 10, 'Moka pot aluminium untuk kopi ala espresso.'],
                ['Gilingan Kopi Manual', 210000, 8, 'Burr keramik, ukuran gilingan bisa diatur.'],
            ],
        ];

        foreach ($data as $categoryName => $products) {
            $category = Category::create([
                'name' => $categoryName,
                'slug' => Str::slug($categoryName),
                'description' => "Koleksi $categoryName pilihan KopiLihdia.",
            ]);

            foreach ($products as [$name, $price, $stock, $desc]) {
                Product::create([
                    'category_id' => $category->id,
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'description' => $desc,
                    'price' => $price,
                    'stock' => $stock,
                    'image' => null, // gambar diisi lewat upload admin nanti
                ]);
            }
        }
    }
}