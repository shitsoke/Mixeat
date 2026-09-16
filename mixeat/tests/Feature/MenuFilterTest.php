<?php

namespace Tests\Feature;

use Tests\TestCase;

class MenuFilterTest extends TestCase
{
    public function test_home_category_links_open_the_filtered_menu(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(route('menu', ['category' => 'Food Trays']), false);
    }

    public function test_menu_filters_products_by_category_and_availability(): void
    {
        $this->get('/menu?category=Food Bowls&availability=available')
            ->assertOk()
            ->assertSee('Iced Tea')
            ->assertSee('Soft Drink')
            ->assertDontSee('Chicken Meal')
            ->assertDontSee('Cheesy Burger');
    }

    public function test_menu_filters_products_by_price_and_sorts_low_to_high(): void
    {
        $response = $this->get('/menu?price=under-100&sort=price-low');

        $response->assertOk()
            ->assertSee('French Fries')
            ->assertSee('Iced Tea')
            ->assertSee('Soft Drink')
            ->assertDontSee('Chicken Meal')
            ->assertDontSee('Chocolate Cake');

    }
}
