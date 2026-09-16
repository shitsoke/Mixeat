<?php

namespace Tests\Feature;

use Tests\TestCase;

class CartFlowTest extends TestCase
{
    public function test_new_visitor_can_build_a_cart_but_must_log_in_to_order(): void
    {
        $this->get('/cart')
            ->assertOk()
            ->assertSee('Your cart is empty');

        $addResponse = $this->from('/menu')
            ->post('/cart/1')
            ->assertRedirect('/menu');

        $addResponse->assertSessionHas('cart.1.quantity', 1);

        $this->get('/cart')
            ->assertOk()
            ->assertSee('Chicken Meal')
            ->assertSee('149.00')
            ->assertSee('149.00')
            ->assertDontSee('199.00');

        $this->get('/checkout')
            ->assertRedirect('/login');

        $this->get('/order-confirmation')
            ->assertRedirect('/login');

        $this->from('/cart')
            ->delete('/cart/1')
            ->assertRedirect('/cart')
            ->assertSessionHas('cart_status', 'Chicken Meal removed from your cart.');

        $this->get('/cart')
            ->assertOk()
            ->assertSee('Your cart is empty');

        $this->from('/products/1')
            ->post('/cart/1')
            ->assertRedirect('/products/1')
            ->assertSessionHas('cart.1.quantity', 1);
    }

    public function test_cart_quantity_controls_update_and_remove_items(): void
    {
        $this->from('/menu')->post('/cart/1');

        $this->from('/cart')
            ->patch('/cart/1/quantity', ['quantity' => 2])
            ->assertRedirect('/cart')
            ->assertSessionHas('cart.1.quantity', 2);

        $this->from('/cart')
            ->patch('/cart/1/quantity', ['quantity' => 0])
            ->assertRedirect('/cart')
            ->assertSessionMissing('cart.1');
    }
}
