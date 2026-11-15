<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesShopData;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase, CreatesShopData;

    public function test_guest_is_redirected_to_login_from_admin(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_customer_cannot_open_admin_pages(): void
    {
        $customer = $this->makeCustomer();

        foreach (['/admin', '/admin/categories', '/admin/products', '/admin/orders'] as $url) {
            $this->actingAs($customer)->get($url)->assertForbidden();
        }
    }

    public function test_admin_can_open_admin_pages(): void
    {
        $admin = $this->makeAdmin();

        foreach (['/admin', '/admin/categories', '/admin/products', '/admin/orders'] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_registration_always_creates_customer(): void
    {
        $this->post('/register', [
            'name'                  => 'Calon Admin',
            'email'                 => 'calon@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'admin', // percobaan manipulasi
        ]);

        $this->assertSame('customer', User::where('email', 'calon@example.com')->value('role'));
    }

    public function test_admin_login_redirects_to_dashboard(): void
    {
        $admin = $this->makeAdmin();

        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_login_with_wrong_password_fails(): void
    {
        $customer = $this->makeCustomer();

        $this->post('/login', ['email' => $customer->email, 'password' => 'salah'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
