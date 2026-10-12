<?php

namespace Tests\Feature;

use App\Models\Promotion;
use App\Models\User;
use Tests\TestCase;

class StorefrontPagesTest extends TestCase
{
    public function test_customer_storefront_pages_load(): void
    {
        $pages = [
            '/',
            '/catering',
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
            '/feedback',
        ];

        foreach ($pages as $page) {
            $response = $this->get($page);

            $response->assertStatus(200);
        }

        $this->get('/about')
            ->assertOk()
            ->assertSee(asset('images/About-us.jpg'), false);

        $this->get('/checkout')->assertRedirect('/login');
        $this->get('/order-confirmation')->assertRedirect('/login');

        $this->actingAs(User::factory()->create())
            ->get('/profile')
            ->assertStatus(200);
    }

    public function test_food_tray_products_are_shown_in_catering_not_the_menu(): void
    {
        $this->get('/menu')
            ->assertOk()
            ->assertDontSee('Chicken Meal')
            ->assertDontSee('Cheesy Burger');

        $this->get('/catering')
            ->assertOk()
            ->assertSee('Party Trays & Buffet Setups')
            ->assertSee('Chicken Meal')
            ->assertSee('Request this tray')
            ->assertSee('data-party-trays-toggle', false)
            ->assertSee('aria-controls="party-trays-menu"', false)
            ->assertSee('id="party-trays-menu"', false)
            ->assertSee('class="mb-12 hidden"', false)
            ->assertSee('service=Party%20Trays', false);
    }

    public function test_header_menu_link_is_labeled_food_bowls_and_filters_the_menu(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Food Bowls')
            ->assertSee(route('menu', ['category' => 'Food Bowls']), false);
    }

    public function test_homepage_slideshow_includes_published_promotions_only(): void
    {
        Promotion::create([
            'title' => 'First homepage offer',
            'description' => 'First offer details.',
            'details' => 'Full first offer details.',
            'image' => 'promotions/first.jpg',
            'is_active' => true,
        ]);
        Promotion::create([
            'title' => 'Second homepage offer',
            'description' => 'Second offer details.',
            'details' => 'Full second offer details.',
            'image' => 'promotions/second.jpg',
            'is_active' => true,
        ]);
        Promotion::create([
            'title' => 'Hidden homepage offer',
            'description' => 'This offer should not appear.',
            'details' => 'Hidden details.',
            'image' => 'promotions/hidden.jpg',
            'is_active' => false,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('data-home-promotion-carousel', false)
            ->assertSee('data-promotion-next', false)
            ->assertSee('aria-label="View details for First homepage offer"', false)
            ->assertSee(route('featured.show', Promotion::where('title', 'First homepage offer')->first()), false)
            ->assertDontSee('First offer details.')
            ->assertDontSee('Second offer details.')
            ->assertDontSee('Hidden homepage offer');
    }
}
