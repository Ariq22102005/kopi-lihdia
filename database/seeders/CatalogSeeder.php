<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Kopi Susu' => [
                ['Es Kopi Susu Lihdia', 18000, 50, 'Signature kami: espresso, susu segar, dan gula aren.'],
                ['Es Kopi Susu Gula Aren', 20000, 40, 'Manis alami gula aren dengan aroma kopi yang lembut.'],
                ['Es Kopi Susu Pandan', 22000, 25, 'Perpaduan kopi susu dengan sirup pandan yang wangi.'],
                ['Es Kopi Susu Karamel', 23000, 0, 'Karamel gurih manis, stok sedang habis.'],
            ],
            'Kopi Klasik' => [
                ['Americano', 16000, 60, 'Espresso dan air, ringan dan segar.'],
                ['Cappuccino', 22000, 30, 'Espresso dengan susu dan busa tebal.'],
                ['Latte', 22000, 2, 'Susu lembut dengan shot espresso, stok tinggal sedikit.'],
            ],
            'Non-Kopi' => [
                ['Matcha Latte', 24000, 20, 'Matcha premium dengan susu creamy.'],
                ['Cokelat Dingin', 21000, 25, 'Cokelat pekat dengan susu dingin.'],
                ['Teh Tarik Lihdia', 17000, 35, 'Teh pekat dengan susu, gaya tarik.'],
            ],
            'Makanan' => [
                ['Roti Bakar Cokelat Keju', 20000, 15, 'Roti tebal dengan cokelat dan keju parut.'],
                ['Croissant Butter', 18000, 12, 'Croissant renyah dengan butter asli.'],
                ['Kentang Goreng', 15000, 20, 'Kentang goreng renyah dengan saus sambal.'],
            ],
        ];

        foreach ($data as $categoryName => $products) {
            $category = Category::create([
                'name' => $categoryName,
                'slug' => Str::slug($categoryName),
                'description' => "Menu $categoryName pilihan KopiLihdia.",
            ]);

            foreach ($products as [$name, $price, $stock, $desc]) {
                Product::create([
                    'category_id' => $category->id,
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'description' => $desc,
                    'price' => $price,
                    'stock' => $stock,
                    'image' => null,
                ]);
            }
        }
    }
}
