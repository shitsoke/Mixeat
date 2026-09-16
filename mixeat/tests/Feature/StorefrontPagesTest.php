<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class StorefrontPagesTest extends TestCase
{
    public function test_customer_storefront_pages_load(): void
    {
        $pages = [
            '/',
            '/menu',
            '/featured',
            '/products/1',
            '/branches',
            '/cart',
            '/orders',
            '/orders/1',
            '/login',
            '/register',
            '/about',
            '/contact',
        ];

        foreach ($pages as $page) {
            $response = $this->get($page);

            $response->assertStatus(200);
        }

        $this->get('/checkout')->assertRedirect('/login');
        $this->get('/order-confirmation')->assertRedirect('/login');

        $this->actingAs(User::factory()->create())
            ->get('/profile')
            ->assertStatus(200);
    }
}
