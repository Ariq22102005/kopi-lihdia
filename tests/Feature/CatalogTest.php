<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesShopData;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase, CreatesShopData;

    public function test_inactive_product_is_hidden_from_catalog(): void
    {
        $this->makeProduct(['name' => 'Menu Aktif']);
        $this->makeProduct(['name' => 'Menu Tersembunyi', 'is_active' => false]);

        $this->get('/produk')
            ->assertOk()
            ->assertSee('Menu Aktif')
            ->assertDontSee('Menu Tersembunyi');
    }

    public function test_inactive_product_detail_returns_404(): void
    {
        $product = $this->makeProduct(['is_active' => false]);

        $this->get(route('products.show', $product))->assertNotFound();
    }

    public function test_search_filters_products(): void
    {
        $this->makeProduct(['name' => 'Matcha Spesial']);
        $this->makeProduct(['name' => 'Americano Biasa']);

        $this->get('/produk?q=matcha')
            ->assertSee('Matcha Spesial')
            ->assertDontSee('Americano Biasa');
    }
}
