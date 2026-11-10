<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use Tests\TestCase;

class BranchSelectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Branch::forceCreate([
            'name' => 'MixEat Commissary',
            'address' => 'Yap Fraternity Building, Jose L. Briones Street, Cebu City',
            'opening_hours' => '8:00am - 4:00pm',
            'distance' => '2 km away',
        ]);
    }

    public function test_customer_selection_does_not_override_the_default_order_branch(): void
    {
        $user = User::factory()->create();

        $this->from('/branches')
            ->post('/branches/2/select')
            ->assertRedirect('/branches')
            ->assertSessionHas('selected_branch', 2);

        $this->actingAs($user)
            ->get('/checkout')
            ->assertOk()
            ->assertSee('Default order branch')
            ->assertSee('MixEat Commissary')
            ->assertSee('Yap Fraternity Building');
    }

    public function test_customer_can_select_a_branch_from_home_dropdown(): void
    {
        $this->from('/')
            ->post('/branches/select', ['branch' => 1])
            ->assertRedirect('/')
            ->assertSessionHas('selected_branch', 1)
            ->assertSessionHas('branch_status', 'MixEat Mandaue selected.');
    }
}
