<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Order;
use App\Models\User;
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

    public function test_placed_order_is_added_to_order_history_and_clears_cart(): void
    {
        $user = User::factory()->create();
        $this->setCheckoutCart();

        $this->actingAs($user)
            ->post('/orders', [
                'order_type' => 'Pickup',
                'pickup_at' => now()->addDay()->format('Y-m-d\TH:i'),
                'payment_method' => 'Cash',
            ])
            ->assertRedirect('/order-confirmation')
            ->assertSessionHas('latest_order.id')
            ->assertSessionMissing('cart');

        $orderNumber = session('latest_order.id');
        $this->assertMatchesRegularExpression('/^MX-\d{6}$/', $orderNumber);

        $this->actingAs($user)
            ->get('/orders')
            ->assertOk()
            ->assertSee($orderNumber)
            ->assertSee('Pickup:');

        $this->actingAs($user)
            ->get('/orders/'.(int) substr($orderNumber, 3))
            ->assertOk()
            ->assertSee('Pickup')
            ->assertSee('Payment')
            ->assertSee('Cash');
    }

    public function test_checkout_requires_a_future_pickup_date_and_time(): void
    {
        $this->setCheckoutCart();

        $this->actingAs(User::factory()->create())
            ->from('/checkout')
            ->post('/orders', [
                'order_type' => 'Pickup',
                'pickup_at' => now()->subDay()->format('Y-m-d\TH:i'),
                'payment_method' => 'Cash',
            ])
            ->assertRedirect('/checkout')
            ->assertSessionHasErrors('pickup_at');
    }

    public function test_orders_above_1500_require_qr_code_payment(): void
    {
        $this->setCheckoutCart(11);
        $user = User::factory()->create();

        $checkoutResponse = $this->actingAs($user)
            ->get('/checkout')
            ->assertOk()
            ->assertSee('Pickup date and time')
            ->assertSee('Deliver via Maxim')
            ->assertSee(asset('images/Gcash.jpeg'));
        $this->assertMatchesRegularExpression('/<input[^>]*id="cash-payment"[^>]*disabled/', $checkoutResponse->getContent());
        $this->assertMatchesRegularExpression('/<input[^>]*id="qr-payment"[^>]*checked/', $checkoutResponse->getContent());
        $this->assertDoesNotMatchRegularExpression('/<div id="qr-payment-details" class="hidden/', $checkoutResponse->getContent());
        $this->assertMatchesRegularExpression('/<input[^>]*id="gcash_reference_number"[^>]*required/', $checkoutResponse->getContent());

        $this->actingAs($user)
            ->from('/checkout')
            ->post('/orders', [
                'order_type' => 'Pickup',
                'pickup_at' => now()->addDay()->format('Y-m-d\TH:i'),
                'payment_method' => 'Cash',
            ])
            ->assertRedirect('/checkout')
            ->assertSessionHasErrors('payment_method');

        $this->actingAs($user)
            ->from('/checkout')
            ->post('/orders', [
                'order_type' => 'Pickup',
                'pickup_at' => now()->addDay()->format('Y-m-d\TH:i'),
                'payment_method' => 'QR Code',
            ])
            ->assertRedirect('/checkout')
            ->assertSessionHasErrors('gcash_reference_number');

        $this->actingAs($user)
            ->post('/orders', [
                'order_type' => 'Pickup',
                'pickup_at' => now()->addDay()->format('Y-m-d\TH:i'),
                'payment_method' => 'QR Code',
                'gcash_reference_number' => '1234567890123',
            ])
            ->assertRedirect('/order-confirmation')
            ->assertSessionHas('latest_order.gcash_reference_number', '1234567890123');

        $this->assertSame('QR Code', Order::latest('id')->firstOrFail()->payment_method);
        $this->assertSame('1234567890123', Order::latest('id')->firstOrFail()->gcash_reference_number);
        $this->get('/order-confirmation')->assertSee('1234567890123');

        $this->setCheckoutCart(10, 150);
        $this->actingAs($user)
            ->post('/orders', [
                'order_type' => 'Pickup',
                'pickup_at' => now()->addDay()->format('Y-m-d\TH:i'),
                'payment_method' => 'Cash',
            ])
            ->assertRedirect('/order-confirmation');

        $order = Order::latest('id')->firstOrFail();
        $this->assertSame(1500.0, (float) $order->total);
        $this->assertSame('Cash', $order->payment_method);
        $this->assertNull($order->gcash_reference_number);
    }

    public function test_in_house_delivery_requires_a_3000_purchase_but_maxim_is_available(): void
    {
        $this->setCheckoutCart();
        $user = User::factory()->create();
        $delivery = [
            'order_type' => 'Delivery',
            'delivery_method' => 'In-house',
            'delivery_address' => '12 Sample Street, Cebu City',
            'payment_method' => 'Cash',
        ];

        $this->actingAs($user)
            ->from('/checkout')
            ->post('/orders', $delivery)
            ->assertRedirect('/checkout')
            ->assertSessionHasErrors('delivery_method');

        $this->actingAs($user)
            ->post('/orders', array_merge($delivery, ['delivery_method' => 'Maxim']))
            ->assertRedirect('/order-confirmation')
            ->assertSessionHas('latest_order.delivery_method', 'Maxim')
            ->assertSessionHas('latest_order.type', 'Delivery');

        $order = Order::latest('id')->firstOrFail();
        $this->assertSame('12 Sample Street, Cebu City', $order->delivery_address);
        $this->assertNull($order->pickup_at);

        $this->setCheckoutCart(21);
        $this->actingAs($user)
            ->post('/orders', [
                'order_type' => 'Delivery',
                'delivery_method' => 'In-house',
                'delivery_address' => '12 Sample Street, Cebu City',
                'payment_method' => 'QR Code',
            ])
            ->assertRedirect('/order-confirmation');

        $this->assertSame('In-house', Order::latest('id')->firstOrFail()->delivery_method);
    }

    public function test_customer_can_poll_only_their_own_orders_from_the_home_page(): void
    {
        $branch = Branch::create([
            'name' => 'Live Test Branch',
            'address' => '1 Main Street',
            'opening_hours' => '9am-9pm',
            'distance' => '1 km',
        ]);
        $customer = User::factory()->create();
        $anotherCustomer = User::factory()->create();

        Order::create([
            'order_number' => 'MX-009101',
            'user_id' => $customer->id,
            'branch_id' => $branch->id,
            'status' => 'Preparing',
            'type' => 'Pickup',
            'total' => 500,
            'pickup_at' => now()->addHour(),
            'payment_method' => 'Cash',
        ]);
        Order::create([
            'order_number' => 'MX-009102',
            'user_id' => $anotherCustomer->id,
            'branch_id' => $branch->id,
            'status' => 'Pending',
            'type' => 'Pickup',
            'total' => 250,
        ]);
        Order::create([
            'order_number' => 'MX-009103',
            'user_id' => $customer->id,
            'branch_id' => $branch->id,
            'status' => 'Completed',
            'type' => 'Pickup',
            'total' => 300,
        ]);

        $this->actingAs($customer)
            ->get('/')
            ->assertOk()
            ->assertSee('Track your orders')
            ->assertSee('MX-009101')
            ->assertDontSee('MX-009103');

        $this->actingAs($customer)
            ->getJson('/orders/live')
            ->assertOk()
            ->assertJsonPath('orders.0.id', 'MX-009101')
            ->assertJsonPath('orders.0.status', 'Preparing')
            ->assertJsonMissing(['id' => 'MX-009102'])
            ->assertJsonMissing(['id' => 'MX-009103']);

        $this->actingAs($customer)
            ->get('/orders')
            ->assertOk()
            ->assertSee('MX-009103')
            ->assertSee('Completed');
    }

    private function setCheckoutCart(int $quantity = 1, float $price = 149): void
    {
        $this->withSession([
            'cart' => [
                'test-item' => [
                    'id' => 1,
                    'name' => 'Chicken Meal',
                    'price' => $price,
                    'quantity' => $quantity,
                    'image' => 'images/chicken-meal.jpg',
                    'addons' => [],
                ],
            ],
        ]);
    }
}
