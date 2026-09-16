<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class BranchSelectionTest extends TestCase
{
    public function test_customer_can_select_a_branch_for_checkout(): void
    {
        $user = User::factory()->create();

        $this->from('/branches')
            ->post('/branches/2/select')
            ->assertRedirect('/branches')
            ->assertSessionHas('selected_branch', 2);

        $this->actingAs($user)
            ->get('/checkout')
            ->assertOk()
            ->assertSee('MixEat Talamban')
            ->assertSee('Talamban, Cebu City');
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
