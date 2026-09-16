<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminFoodManagementTest extends TestCase
{
    public function test_admin_can_manage_food_and_branch_availability(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $product = Product::firstOrFail();
        $banilad = Branch::where('name', 'MixEat Banilad')->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin/products')
            ->assertOk()
            ->assertSee('Manage Foods');

        $this->actingAs($admin)
            ->patch('/admin/products/'.$product->id.'/branches/'.$banilad->id, ['available' => 0])
            ->assertRedirect();

        $this->assertDatabaseHas('branch_product', [
            'branch_id' => $banilad->id,
            'product_id' => $product->id,
            'available' => 0,
        ]);

        $this->actingAs(User::factory()->create())
            ->get('/admin/products')
            ->assertForbidden();
    }

    public function test_admin_uploads_product_image_and_selects_category(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->post('/admin/products', [
                'name' => 'Uploaded Special',
                'category' => 'Food Trays',
                'description' => 'A product with an uploaded image.',
                'price' => 199,
                'image' => UploadedFile::fake()->create('special.jpg', 100, 'image/jpeg'),
                'badge' => 'New',
            ])
            ->assertRedirect('/admin/products');

        $product = Product::where('name', 'Uploaded Special')->firstOrFail();

        $this->assertSame('Food Trays', $product->category);
        $this->assertStringStartsWith('products/', $product->image);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_admin_can_update_homepage_promotion(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->put('/admin/promotion', [
                'label' => 'Weekend promotion',
                'title' => 'Family Food Tray',
                'price' => 299,
                'image' => UploadedFile::fake()->create('promotion.jpg', 100, 'image/jpeg'),
            ])
            ->assertRedirect();

        $image = SiteSetting::where('key', 'promotion_image')->value('value');

        $this->assertSame('Weekend promotion', SiteSetting::where('key', 'promotion_label')->value('value'));
        $this->assertSame('Family Food Tray', SiteSetting::where('key', 'promotion_title')->value('value'));
        $this->assertSame('299', SiteSetting::where('key', 'promotion_price')->value('value'));
        $this->assertStringStartsWith('promotions/', $image);
        Storage::disk('public')->assertExists($image);
    }

    public function test_admin_can_add_edit_and_delete_a_branch(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->post('/admin/branches', [
                'name' => 'MixEat Downtown',
                'address' => 'Downtown Cebu City',
                'opening_hours' => '10:00 AM - 11:00 PM',
                'distance' => '6.0 km away',
            ])
            ->assertRedirect('/admin/branches');

        $branch = Branch::where('name', 'MixEat Downtown')->firstOrFail();

        $this->actingAs($admin)
            ->put('/admin/branches/'.$branch->id, [
                'name' => 'MixEat Downtown Updated',
                'address' => 'Updated Cebu City',
                'opening_hours' => '9:00 AM - 10:00 PM',
                'distance' => '6.5 km away',
            ])
            ->assertRedirect('/admin/branches');

        $this->assertDatabaseHas('branches', ['id' => $branch->id, 'name' => 'MixEat Downtown Updated']);

        $this->actingAs($admin)
            ->delete('/admin/branches/'.$branch->id)
            ->assertRedirect();

        $this->assertDatabaseMissing('branches', ['id' => $branch->id]);
    }
}
