<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use Tests\TestCase;

class SupervisorLiveOrdersTest extends TestCase
{
    public function test_supervisor_live_feed_contains_new_orders_for_the_assigned_branch(): void
    {
        $branch = Branch::forceCreate([
            'name' => 'Live Supervisor Branch',
            'address' => '2 Main Street',
            'opening_hours' => '9am-9pm',
            'distance' => '1 km',
        ]);
        $otherBranch = Branch::forceCreate([
            'name' => 'Other Branch',
            'address' => '3 Main Street',
            'opening_hours' => '9am-9pm',
            'distance' => '2 km',
        ]);
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'branch_id' => $branch->id,
        ]);
        $preparingOrder = Order::create([
            'order_number' => 'MX-009212',
            'branch_id' => $branch->id,
            'status' => 'Preparing',
            'type' => 'Pickup',
            'total' => 400,
        ]);
        $newOrder = Order::create([
            'order_number' => 'MX-009201',
            'branch_id' => $branch->id,
            'status' => 'Pending',
            'type' => 'Pickup',
            'total' => 750,
            'payment_method' => 'QR Code',
            'gcash_reference_number' => '9876543210123',
        ]);
        OrderItem::create([
            'order_id' => $newOrder->id,
            'product_name' => 'Test chicken bowl',
            'quantity' => 1,
            'price' => 750,
            'addons' => [],
        ]);
        Order::create([
            'order_number' => 'MX-009210',
            'branch_id' => $branch->id,
            'status' => 'Completed',
            'type' => 'Pickup',
            'total' => 200,
        ]);
        Order::create([
            'order_number' => 'MX-009211',
            'branch_id' => $branch->id,
            'status' => 'Declined',
            'type' => 'Pickup',
            'total' => 300,
        ]);
        Order::create([
            'order_number' => 'MX-009202',
            'branch_id' => $otherBranch->id,
            'status' => 'Pending',
            'type' => 'Pickup',
            'total' => 950,
        ]);

        $dashboard = $this->actingAs($supervisor)
            ->get('/admin/supervisors')
            ->assertOk();
        $dashboardHtml = $dashboard->getContent();
        $tablePosition = strpos($dashboardHtml, 'id="live-orders-table"');
        $pollingPosition = strpos($dashboardHtml, 'setInterval(refreshOrders, 3000)');
        $bodyEndPosition = strpos($dashboardHtml, '</body>');

        $this->assertNotFalse($tablePosition);
        $this->assertNotFalse($pollingPosition);
        $this->assertNotFalse($bodyEndPosition);
        $this->assertGreaterThan($tablePosition, $pollingPosition);
        $this->assertLessThan($bodyEndPosition, $pollingPosition);
        $this->assertSame(1, substr_count($dashboardHtml, 'id="order-alert-sound"'));
        $this->assertStringContainsString('supervisor:live-state', $dashboardHtml);
        $this->assertStringContainsString($preparingOrder->order_number, $dashboardHtml);
        $this->assertStringContainsString($newOrder->order_number, $dashboardHtml);
        $this->assertStringNotContainsString('MX-009210', $dashboardHtml);
        $this->assertStringNotContainsString('MX-009211', $dashboardHtml);

        $liveResponse = $this->actingAs($supervisor)
            ->getJson('/admin/supervisors/orders/live')
            ->assertOk()
            ->assertJsonPath('order_count', 2)
            ->assertJsonPath('daily_summary.total_orders', 4)
            ->assertJsonPath('daily_summary.preparing_count', 1)
            ->assertJsonPath('daily_summary.ready_count', 0)
            ->assertJsonPath('daily_summary.completed_count', 1)
            ->assertJsonPath('daily_summary.declined_count', 1)
            ->assertJsonPath('daily_summary.pending_count', 1)
            ->assertJsonPath('daily_summary.total_revenue', 600)
            ->assertJsonPath('notification_state.count', 0)
            ->assertJsonPath('pending_orders.0.id', (string) $newOrder->id)
            ->assertJsonPath('pending_orders.0.number', $newOrder->order_number)
            ->assertJsonPath('orders_html', fn (string $html) => str_contains($html, $newOrder->order_number)
                && str_contains($html, 'data-order-id="'.$newOrder->id.'"')
                && str_contains($html, 'data-order-status="Pending"')
                && str_contains($html, $preparingOrder->order_number)
                && str_contains($html, 'data-order-status="Preparing"')
                && str_contains($html, 'Print Receipt')
                && str_contains($html, 'Out for Pickup')
                && str_contains($html, '9876543210123')
                && ! str_contains($html, 'MX-009210')
                && ! str_contains($html, 'MX-009211')
                && ! str_contains($html, 'MX-009202'));
        $liveResponse->assertHeader('Cache-Control', 'no-store, private');

        $laterOrder = Order::create([
            'order_number' => 'MX-009209',
            'branch_id' => $branch->id,
            'status' => 'Pending',
            'type' => 'Pickup',
            'total' => 800,
        ]);

        $this->getJson('/admin/supervisors/orders/live')
            ->assertOk()
            ->assertJsonPath('order_count', 3)
            ->assertJsonPath('daily_summary.total_orders', 5)
            ->assertJsonPath('daily_summary.completed_count', 1)
            ->assertJsonPath('daily_summary.declined_count', 1)
            ->assertJsonPath('daily_summary.pending_count', 2)
            ->assertJsonPath('pending_orders.0.id', (string) $laterOrder->id)
            ->assertJsonPath('orders_html', fn (string $html) => strpos($html, $laterOrder->order_number) < strpos($html, $newOrder->order_number)
                && str_contains($html, $preparingOrder->order_number)
                && ! str_contains($html, 'MX-009210')
                && ! str_contains($html, 'MX-009211'));
    }

    public function test_direct_notification_route_redirects_to_supervisor_dashboard(): void
    {
        $branch = Branch::forceCreate([
            'name' => 'Notification Branch',
            'address' => '4 Main Street',
            'opening_hours' => '9am-9pm',
            'distance' => '3 km',
        ]);
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'branch_id' => $branch->id,
        ]);

        $this->actingAs($supervisor)
            ->get('/admin/supervisors/notifications')
            ->assertRedirect(route('admin.supervisors.index'));
    }

    public function test_supervisor_can_fetch_and_mark_new_order_notifications_as_read(): void
    {
        $branch = Branch::forceCreate([
            'name' => 'Notification Branch',
            'address' => '4 Main Street',
            'opening_hours' => '9am-9pm',
            'distance' => '3 km',
        ]);
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'branch_id' => $branch->id,
        ]);
        $order = Order::create([
            'order_number' => 'MX-009203',
            'branch_id' => $branch->id,
            'status' => 'Pending',
            'type' => 'Pickup',
            'total' => 850,
        ]);
        $supervisor->notify(new NewOrderNotification($order));

        $this->actingAs($supervisor)
            ->getJson('/admin/supervisors/notifications')
            ->assertOk()
            ->assertJsonPath('count', 1)
            ->assertJsonPath('notifications.0.data.order_id', $order->id);

        $this->getJson('/admin/supervisors/orders/live')
            ->assertOk()
            ->assertJsonPath('notification_state.count', 1)
            ->assertJsonPath('notification_state.notifications.0.data.order_id', $order->id);

        $this->postJson('/admin/supervisors/notifications/read-all')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->getJson('/admin/supervisors/notifications')
            ->assertOk()
            ->assertJsonPath('count', 0);
    }

    public function test_supervisor_can_accept_an_order_for_their_branch(): void
    {
        $branch = Branch::forceCreate([
            'name' => 'Accept Order Branch',
            'address' => '5 Main Street',
            'opening_hours' => '9am-9pm',
            'distance' => '4 km',
        ]);
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'branch_id' => $branch->id,
        ]);
        $order = Order::create([
            'order_number' => 'MX-009204',
            'branch_id' => $branch->id,
            'status' => 'Pending',
            'type' => 'Pickup',
            'total' => 650,
        ]);

        $this->actingAs($supervisor)
            ->patchJson("/admin/supervisors/orders/{$order->id}/accept")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('status', 'Preparing')
            ->assertJsonPath('receipt_url', route('admin.supervisors.orders.receipt', [
                'order' => $order->id,
                'copy' => 'customer',
            ]));

        $this->patchJson("/admin/supervisors/orders/{$order->id}/accept")
            ->assertStatus(409)
            ->assertJsonMissingPath('receipt_url');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Preparing',
        ]);
    }

    public function test_supervisor_can_print_branch_receipt_and_delivery_rider_copy(): void
    {
        $branch = Branch::forceCreate([
            'name' => 'Receipt Test Branch',
            'address' => '12 Receipt Road',
            'opening_hours' => '9am-9pm',
            'distance' => '5 km',
        ]);
        $customer = User::factory()->create([
            'name' => 'Receipt Customer',
            'phone' => '09123456789',
        ]);
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'branch_id' => $branch->id,
        ]);
        $order = Order::create([
            'order_number' => 'MX-009240',
            'user_id' => $customer->id,
            'branch_id' => $branch->id,
            'status' => 'Preparing',
            'type' => 'Delivery',
            'total' => 220,
            'delivery_fee' => 20,
            'delivery_method' => 'In-house',
            'delivery_address' => 'Unit 4, 99 Sample Street, Manila',
            'payment_method' => 'Cash',
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_name' => 'Chicken Rice Bowl',
            'quantity' => 2,
            'price' => 100,
            'addons' => ['Extra Rice'],
        ]);

        $this->actingAs($supervisor)
            ->get(route('admin.supervisors.orders.receipt', [
                'order' => $order->id,
                'copy' => 'customer',
                'paper' => 58,
                'reprint' => 1,
            ]))
            ->assertOk()
            ->assertSee('Receipt Test Branch')
            ->assertSee('MX-009240')
            ->assertSee('Receipt Customer')
            ->assertSee('09123456789')
            ->assertSee('Unit 4, 99 Sample Street, Manila')
            ->assertSee('Chicken Rice Bowl')
            ->assertSee('Extra Rice')
            ->assertSee('58mm auto')
            ->assertSee('REPRINT')
            ->assertSee('Items Subtotal')
            ->assertSee('GRAND TOTAL');

        $this->get(route('admin.supervisors.orders.receipt', [
            'order' => $order->id,
            'copy' => 'rider',
            'paper' => 80,
            'auto' => 1,
        ]))
            ->assertOk()
            ->assertSee('DELIVERY RIDER COPY')
            ->assertSee('Cash to collect from customer: &#8369;220.00', false)
            ->assertSee('80mm auto')
            ->assertSee('window.print()');

        $order->update(['status' => 'Completed']);
        $this->get(route('admin.supervisors.orders.receipt', [
            'order' => $order->id,
            'copy' => 'rider',
        ]))
            ->assertOk()
            ->assertSee('Payment settlement is not tracked on this order.')
            ->assertDontSee('Cash to collect from customer:');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Completed',
        ]);
    }

    public function test_supervisor_cannot_print_receipts_for_another_branch_or_pending_orders(): void
    {
        $branch = Branch::forceCreate([
            'name' => 'Assigned Receipt Branch',
            'address' => '15 Receipt Road',
            'opening_hours' => '9am-9pm',
            'distance' => '6 km',
        ]);
        $otherBranch = Branch::forceCreate([
            'name' => 'Private Receipt Branch',
            'address' => '16 Receipt Road',
            'opening_hours' => '9am-9pm',
            'distance' => '7 km',
        ]);
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'branch_id' => $branch->id,
        ]);
        $otherOrder = Order::create([
            'order_number' => 'MX-009241',
            'branch_id' => $otherBranch->id,
            'status' => 'Preparing',
            'type' => 'Pickup',
            'total' => 100,
        ]);
        $pendingOrder = Order::create([
            'order_number' => 'MX-009242',
            'branch_id' => $branch->id,
            'status' => 'Pending',
            'type' => 'Pickup',
            'total' => 100,
        ]);

        $this->actingAs($supervisor)
            ->get(route('admin.supervisors.orders.receipt', ['order' => $otherOrder->id, 'copy' => 'customer']))
            ->assertForbidden();

        $this->get(route('admin.supervisors.orders.receipt', ['order' => $pendingOrder->id, 'copy' => 'customer']))
            ->assertNotFound();
    }

    public function test_pending_alarm_state_tracks_all_branch_orders_independently_of_notifications(): void
    {
        $branch = Branch::forceCreate([
            'name' => 'Pending Alarm Branch',
            'address' => '7 Main Street',
            'opening_hours' => '9am-9pm',
            'distance' => '6 km',
        ]);
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'branch_id' => $branch->id,
        ]);
        $firstOrder = Order::create([
            'order_number' => 'MX-009206',
            'branch_id' => $branch->id,
            'status' => 'Pending',
            'type' => 'Pickup',
            'total' => 500,
        ]);
        $secondOrder = Order::create([
            'order_number' => 'MX-009207',
            'branch_id' => $branch->id,
            'status' => 'Pending',
            'type' => 'Pickup',
            'total' => 600,
        ]);
        $recentPreparingOrder = Order::create([
            'order_number' => 'MX-009208',
            'branch_id' => $branch->id,
            'status' => 'Preparing',
            'type' => 'Pickup',
            'total' => 700,
        ]);
        $recentPreparingOrder->forceFill(['created_at' => now()->addMinute()])->save();
        $supervisor->notify(new NewOrderNotification($firstOrder));
        $supervisor->notify(new NewOrderNotification($secondOrder));

        $this->actingAs($supervisor)
            ->postJson('/admin/supervisors/notifications/read-all')
            ->assertOk();

        $this->getJson('/admin/supervisors/orders/live')
            ->assertOk()
            ->assertJsonPath('pending_orders.0.id', (string) $secondOrder->id)
            ->assertJsonPath('pending_orders.1.id', (string) $firstOrder->id)
            ->assertJsonPath('daily_summary.pending_count', 2)
            ->assertJsonPath('order_count', 3)
            ->assertJsonPath('orders_html', fn (string $html) => strpos($html, $secondOrder->order_number) < strpos($html, $firstOrder->order_number)
                && str_contains($html, $recentPreparingOrder->order_number));

        $this->patchJson("/admin/supervisors/orders/{$firstOrder->id}/accept")
            ->assertOk()
            ->assertJsonPath('status', 'Preparing');

        $this->getJson('/admin/supervisors/orders/live')
            ->assertOk()
            ->assertJsonPath('daily_summary.pending_count', 1)
            ->assertJsonPath('pending_orders.0.id', (string) $secondOrder->id)
            ->assertJsonPath('order_count', 3)
            ->assertJsonPath('orders_html', fn (string $html) => str_contains($html, $secondOrder->order_number)
                && str_contains($html, $firstOrder->order_number)
                && str_contains($html, $recentPreparingOrder->order_number)
                && str_contains($html, 'data-order-status="Preparing"'));

        $this->patchJson("/admin/supervisors/orders/{$firstOrder->id}/decline")
            ->assertStatus(409);

        $this->patchJson("/admin/supervisors/orders/{$secondOrder->id}/decline")
            ->assertOk()
            ->assertJsonPath('status', 'Declined')
            ->assertJsonMissingPath('receipt_url');

        $this->getJson('/admin/supervisors/orders/live')
            ->assertOk()
            ->assertJsonPath('daily_summary.pending_count', 0)
            ->assertJsonPath('pending_orders', [])
            ->assertJsonPath('order_count', 2)
            ->assertJsonPath('orders_html', fn (string $html) => ! str_contains($html, $secondOrder->order_number)
                && str_contains($html, $firstOrder->order_number)
                && str_contains($html, $recentPreparingOrder->order_number)
                && str_contains($html, 'data-order-status="Preparing"'));
    }

    public function test_viewing_an_order_notification_redirects_to_supervisor_dashboard(): void
    {
        $branch = Branch::forceCreate([
            'name' => 'View Notification Branch',
            'address' => '6 Main Street',
            'opening_hours' => '9am-9pm',
            'distance' => '5 km',
        ]);
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'branch_id' => $branch->id,
        ]);
        $order = Order::create([
            'order_number' => 'MX-009205',
            'branch_id' => $branch->id,
            'status' => 'Pending',
            'type' => 'Pickup',
            'total' => 700,
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_name' => 'Test rice bowl',
            'quantity' => 1,
            'price' => 700,
            'addons' => [],
        ]);
        $supervisor->notify(new NewOrderNotification($order));
        $notification = $supervisor->unreadNotifications()->firstOrFail();

        $this->actingAs($supervisor)
            ->get("/admin/supervisors/notifications/{$notification->id}/order")
            ->assertRedirect('/admin/supervisors')
            ->assertSessionHas('admin_status', "Viewing order {$order->order_number}");

        $this->assertNotNull($notification->fresh()->read_at);

        $supervisor->notify(new NewOrderNotification($order));
        $ajaxNotification = $supervisor->unreadNotifications()->latest()->firstOrFail();
        $this->actingAs($supervisor)
            ->getJson("/admin/supervisors/notifications/{$ajaxNotification->id}/order")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('order_id', $order->id);

        $this->assertNotNull($ajaxNotification->fresh()->read_at);
    }
}
