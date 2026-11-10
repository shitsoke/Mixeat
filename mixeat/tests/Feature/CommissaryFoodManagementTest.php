<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Product;
use App\Models\User;
use Tests\TestCase;

class CommissaryFoodManagementTest extends TestCase
{
    public function test_only_commissary_supervisor_can_manage_commissary_foods(): void
    {
        $commissary = Branch::forceCreate([
            'name' => 'MixEat Commissary',
            'address' => 'Yap Fraternity Building',
            'opening_hours' => '8:00 AM - 4:00 PM',
            'distance' => '0 km away',
        ]);
        $otherBranch = Branch::forceCreate([
            'name' => 'MixEat Robinson Galleria',
            'address' => 'Robinson Galleria',
            'opening_hours' => '8:00 AM - 4:00 PM',
            'distance' => '2 km away',
        ]);

        $commissarySupervisor = User::factory()->create(['role' => 'supervisor', 'branch_id' => $commissary->id]);
        $otherSupervisor = User::factory()->create(['role' => 'supervisor', 'branch_id' => $otherBranch->id]);
        $inactiveSupervisor = User::factory()->create(['role' => 'supervisor', 'branch_id' => $commissary->id, 'is_active' => false]);
        $marketing = User::factory()->create(['role' => 'marketing_admin']);

        $this->actingAs($commissarySupervisor)
            ->get('/admin/supervisors/foods')
            ->assertOk()
            ->assertSee('MixEat Commissary');

        $this->actingAs($otherSupervisor)
            ->get('/admin/supervisors/foods')
            ->assertForbidden();

        $this->actingAs($inactiveSupervisor)
            ->get('/admin/supervisors/foods')
            ->assertForbidden();

        $this->actingAs($marketing)
            ->get('/admin/supervisors/foods')
            ->assertForbidden();
    }

    public function test_commissary_price_and_availability_do_not_change_other_branch(): void
    {
        $commissary = Branch::forceCreate([
            'name' => 'MixEat Commissary',
            'address' => 'Yap Fraternity Building',
            'opening_hours' => '8:00 AM - 4:00 PM',
            'distance' => '0 km away',
        ]);
        $otherBranch = Branch::forceCreate([
            'name' => 'MixEat Gaisano Mall',
            'address' => 'Gaisano Mall',
            'opening_hours' => '8:00 AM - 4:00 PM',
            'distance' => '2 km away',
        ]);
        $product = Product::create([
            'name' => 'Shared Commissary Test Food',
            'category' => 'Food Bowls',
            'description' => 'Shared product.',
            'price' => 100,
        ]);
        $product->branches()->attach([
            $commissary->id => ['available' => true, 'price' => 125],
            $otherBranch->id => ['available' => true, 'price' => 150],
        ]);
        $supervisor = User::factory()->create(['role' => 'supervisor', 'branch_id' => $commissary->id]);

        $this->actingAs($supervisor)
            ->put('/admin/supervisors/foods/'.$product->id, [
                'name' => $product->name,
                'category' => 'Food Bowls',
                'description' => $product->description,
                'price' => 135,
            ])
            ->assertRedirect('/admin/supervisors/foods');

        $this->actingAs($supervisor)
            ->patch('/admin/supervisors/foods/'.$product->id.'/availability', ['available' => 0])
            ->assertRedirect();

        $this->assertDatabaseHas('branch_product', [
            'branch_id' => $commissary->id,
            'product_id' => $product->id,
            'available' => 0,
            'price' => 135,
        ]);
        $this->assertDatabaseHas('branch_product', [
            'branch_id' => $otherBranch->id,
            'product_id' => $product->id,
            'available' => 1,
            'price' => 150,
        ]);
    }
}
