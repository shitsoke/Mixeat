<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderingHours;
use Carbon\Carbon;
use Tests\TestCase;

class OrderingHoursTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_ordering_hours_follow_the_philippine_daily_schedule(): void
    {
        $cases = [
            ['2026-11-05 07:59:00', false, 'Online Ordering Closed • Opens at 8:00 AM'],
            ['2026-11-05 08:00:00', true, 'Online Ordering Open • Until 4:00 PM'],
            ['2026-11-05 12:00:00', true, 'Online Ordering Open • Until 4:00 PM'],
            ['2026-11-05 15:59:59', true, 'Online Ordering Open • Until 4:00 PM'],
            ['2026-11-05 16:00:00', false, 'Online Ordering Closed • Opens at 8:00 AM'],
            ['2026-11-05 17:00:00', false, 'Online Ordering Closed • Opens at 8:00 AM'],
            ['2026-11-06 00:00:00', false, 'Online Ordering Closed • Opens at 8:00 AM'],
            ['2026-11-06 07:59:59', false, 'Online Ordering Closed • Opens at 8:00 AM'],
            ['2026-11-06 08:00:00', true, 'Online Ordering Open • Until 4:00 PM'],
        ];

        foreach ($cases as [$time, $expectedOpen, $expectedLabel]) {
            Carbon::setTestNow(Carbon::parse($time, 'Asia/Manila'));
            $status = app(OrderingHours::class)->status();

            $this->assertSame($expectedOpen, $status['open'], $time);
            $this->assertSame($expectedLabel, $status['label'], $time);
            $this->assertSame('Asia/Manila', $status['timezone']);
            $this->assertGreaterThan(0, $status['next_transition_in_ms'], $time);
        }
    }

    public function test_closed_status_endpoint_is_not_cached_and_includes_the_required_message(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-05 16:00:00', 'Asia/Manila'));

        $this->getJson('/ordering-status')
            ->assertOk()
            ->assertJsonPath('open', false)
            ->assertJsonPath('label', 'Online Ordering Closed • Opens at 8:00 AM')
            ->assertJsonPath('message', 'Online ordering is currently closed. Our ordering hours are from 8:00 AM to 4:00 PM daily. Please come back during our operating hours.')
            ->assertJsonPath('timezone', 'Asia/Manila')
            ->assertHeader('Cache-Control', 'no-store, private');
    }

    public function test_closed_hours_block_basket_additions_and_order_submissions_without_saving_orders(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-05 17:00:00', 'Asia/Manila'));
        $customer = User::factory()->create();
        $cart = [
            'test-item' => [
                'id' => 1,
                'name' => 'Chicken Meal',
                'price' => 149,
                'quantity' => 1,
                'image' => 'images/chicken-meal.jpg',
                'addons' => [],
            ],
        ];
        $orderDetails = [
            'order_type' => 'Pickup',
            'pickup_at' => Carbon::now('Asia/Manila')->addDay()->format('Y-m-d\TH:i'),
            'payment_method' => 'Cash',
        ];

        $this->actingAs($customer)
            ->withSession(['cart' => $cart])
            ->from('/checkout')
            ->post('/orders', $orderDetails)
            ->assertRedirect('/checkout')
            ->assertSessionHasErrors('ordering_hours');

        $this->actingAs($customer)
            ->withSession(['cart' => $cart])
            ->postJson('/orders', $orderDetails)
            ->assertUnprocessable()
            ->assertJsonPath('errors.ordering_hours.0', 'Online ordering is currently closed. Our ordering hours are from 8:00 AM to 4:00 PM daily. Please come back during our operating hours.');

        $this->withSession(['cart' => []])
            ->from('/menu')
            ->post('/cart/1')
            ->assertRedirect('/menu')
            ->assertSessionHasErrors('ordering_hours')
            ->assertSessionHas('cart', []);

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_order_can_be_placed_during_open_hours(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-05 12:00:00', 'Asia/Manila'));
        Branch::forceCreate([
            'name' => 'MixEat Commissary',
            'address' => 'Yap Fraternity Building, Jose L. Briones Street, Cebu City',
            'opening_hours' => '8:00am - 4:00pm',
            'distance' => '2 km away',
        ]);
        $customer = User::factory()->create();
        $cart = [
            'test-item' => [
                'id' => 1,
                'name' => 'Chicken Meal',
                'price' => 149,
                'quantity' => 1,
                'image' => 'images/chicken-meal.jpg',
                'addons' => [],
            ],
        ];

        $this->actingAs($customer)
            ->withSession(['cart' => $cart])
            ->post('/orders', [
                'order_type' => 'Pickup',
                'pickup_at' => Carbon::now('Asia/Manila')->addDay()->format('Y-m-d\TH:i'),
                'payment_method' => 'Cash',
            ])
            ->assertRedirect('/order-confirmation');

        $this->assertDatabaseHas('orders', [
            'user_id' => $customer->id,
            'status' => 'Pending',
            'total' => 149,
            'delivery_fee' => 0,
        ]);
    }

    public function test_closed_ordering_disables_checkout_and_add_to_cart_but_allows_browsing(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-05 16:01:00', 'Asia/Manila'));
        $branch = Branch::forceCreate([
            'name' => 'MixEat Commissary',
            'address' => 'Yap Fraternity Building, Jose L. Briones Street, Cebu City',
            'opening_hours' => '8:00am - 4:00pm',
            'distance' => '2 km away',
        ]);
        $customer = User::factory()->create();
        $cart = [
            'test-item' => [
                'id' => 1,
                'name' => 'Chicken Meal',
                'price' => 149,
                'quantity' => 1,
                'image' => 'images/chicken-meal.jpg',
                'addons' => [],
            ],
        ];

        $this->get('/menu')
            ->assertOk()
            ->assertSee('Online Ordering Closed')
            ->assertSee('Online ordering is currently closed. Our ordering hours are from 8:00 AM to 4:00 PM daily. Please come back during our operating hours.')
            ->assertSee('disabled');

        $this->withSession(['cart' => $cart])
            ->get('/cart')
            ->assertOk()
            ->assertSee('aria-disabled="true"', false)
            ->assertSee('Online Ordering Closed');

        $this->actingAs($customer)
            ->withSession(['cart' => $cart])
            ->get('/checkout')
            ->assertOk()
            ->assertSee('id="checkout-form"', false)
            ->assertSee('data-ordering-action="place-order"', false)
            ->assertSee('disabled', false);

        $this->actingAs($customer)
            ->get('/orders')
            ->assertOk();

        $this->assertSame('MixEat Commissary', $branch->name);
    }

    public function test_catering_submission_route_remains_unrestricted_by_ordering_hours(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-05 17:00:00', 'Asia/Manila'));

        $this->get('/catering')->assertOk();

        $this->postJson('/catering/inquiries', [])
            ->assertUnprocessable()
            ->assertJsonMissingPath('errors.ordering_hours');
    }

    public function test_supervisors_can_manage_orders_and_print_receipts_after_ordering_closes(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-05 17:00:00', 'Asia/Manila'));
        $branch = Branch::forceCreate([
            'name' => 'After Hours Supervisor Branch',
            'address' => '20 Main Street',
            'opening_hours' => '8:00am - 4:00pm',
            'distance' => '2 km away',
        ]);
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'branch_id' => $branch->id,
        ]);
        $order = Order::create([
            'order_number' => 'MX-009901',
            'branch_id' => $branch->id,
            'status' => 'Pending',
            'type' => 'Pickup',
            'total' => 300,
        ]);

        $this->actingAs($supervisor)
            ->get('/admin/supervisors')
            ->assertOk()
            ->assertSee($order->order_number);

        $this->patchJson("/admin/supervisors/orders/{$order->id}/accept")
            ->assertOk()
            ->assertJsonPath('status', 'Preparing')
            ->assertJsonPath('receipt_url', route('admin.supervisors.orders.receipt', [
                'order' => $order->id,
                'copy' => 'customer',
            ]));

        $this->get(route('admin.supervisors.orders.receipt', [
            'order' => $order->id,
            'copy' => 'customer',
        ]))
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('MIXEAT');
    }
}
