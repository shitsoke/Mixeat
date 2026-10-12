<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Order;
use App\Models\User;
use Tests\TestCase;

class SupervisorLiveOrdersTest extends TestCase
{
    public function test_supervisor_live_feed_contains_new_orders_for_the_assigned_branch(): void
    {
        $branch = Branch::create([
            'name' => 'Live Supervisor Branch',
            'address' => '2 Main Street',
            'opening_hours' => '9am-9pm',
            'distance' => '1 km',
        ]);
        $otherBranch = Branch::create([
            'name' => 'Other Branch',
            'address' => '3 Main Street',
            'opening_hours' => '9am-9pm',
            'distance' => '2 km',
        ]);
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'branch_id' => $branch->id,
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
        Order::create([
            'order_number' => 'MX-009202',
            'branch_id' => $otherBranch->id,
            'status' => 'Pending',
            'type' => 'Pickup',
            'total' => 950,
        ]);

        $this->actingAs($supervisor)
            ->get('/admin/supervisors')
            ->assertOk()
            ->assertSee('live-orders-table');

        $this->actingAs($supervisor)
            ->getJson('/admin/supervisors/orders/live')
            ->assertOk()
            ->assertJsonPath('daily_summary.total_orders', 1)
            ->assertJsonPath('daily_summary.preparing_count', 0)
            ->assertJsonPath('daily_summary.ready_count', 0)
            ->assertJsonPath('daily_summary.total_revenue', 0)
            ->assertJsonPath('orders_html', fn (string $html) => str_contains($html, $newOrder->order_number)
                && str_contains($html, '9876543210123')
                && ! str_contains($html, 'MX-009202'));
    }
}
