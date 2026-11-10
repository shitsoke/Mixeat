<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CateringBooking;
use App\Models\CateringMenuItem;
use App\Models\CateringPackage;
use App\Models\PackedMealPackage;
use App\Models\PartyTray;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class CateringMenuManagementTest extends TestCase
{
    public function test_marketing_admin_can_manage_all_catering_catalog_types(): void
    {
        $admin = User::factory()->create(['role' => 'marketing_admin']);
        $this->actingAs($admin);

        $this->post(route('admin.marketing.catering-menu.catalog.store', 'packages'), [
            'code' => 'seasonal_package',
            'name' => 'Seasonal Package',
            'description' => 'Seasonal selections.',
            'price' => '725.00',
            'min_guests' => 10,
            'max_guests' => 100,
            'included_dishes' => '{"meat_count":2,"categories":["rice"]}',
            'is_active' => 1,
        ])->assertRedirect();
        $package = CateringPackage::where('code', 'seasonal_package')->firstOrFail();
        $this->assertSame('725.00', $package->price);

        $this->post(route('admin.marketing.catering-menu.catalog.store', 'packed-meals'), [
            'code' => 'seasonal_meal',
            'name' => 'Seasonal Meal',
            'price' => '180',
            'included_dishes' => '{"sides":["Bam-i"],"fixed":["Rice"]}',
            'is_active' => 1,
        ])->assertRedirect();
        $this->assertDatabaseHas('packed_meal_packages', ['code' => 'seasonal_meal']);

        $this->post(route('admin.marketing.catering-menu.catalog.store', 'party-trays'), [
            'name' => 'Seasonal Tray',
            'description' => 'Tray description.',
            'full_price' => '1500',
            'half_price' => '800',
            'sizes' => '{"full":"Full","half":"Half"}',
            'dishes' => '["Chicken Adobo"]',
            'is_active' => 1,
        ])->assertRedirect();
        $tray = PartyTray::where('name', 'Seasonal Tray')->firstOrFail();

        $this->patch(route('admin.marketing.catering-menu.catalog.toggle', ['type' => 'packages', 'id' => $package->id]))->assertRedirect();
        $this->assertDatabaseHas('catering_packages', ['id' => $package->id, 'is_active' => false]);
        $this->delete(route('admin.marketing.catering-menu.catalog.destroy', ['type' => 'party-trays', 'id' => $tray->id]))->assertRedirect();
        $this->assertSoftDeleted('party_trays', ['id' => $tray->id]);
    }

    public function test_only_active_catalog_items_are_shown_to_customers(): void
    {
        $active = CateringPackage::create([
            'code' => 'active_test_package', 'name' => 'Active Test Package', 'price' => 700,
            'included_dishes' => ['meat_count' => 1], 'is_active' => true,
        ]);
        CateringPackage::create([
            'code' => 'inactive_test_package', 'name' => 'Inactive Test Package', 'price' => 700,
            'included_dishes' => ['meat_count' => 1], 'is_active' => false,
        ]);

        $this->get(route('catering'))
            ->assertSee('Active Test Package')
            ->assertDontSee('Inactive Test Package');

        $this->assertSame('700.00', $active->fresh()->price);
    }

    public function test_only_marketing_admin_can_manage_catering_menu(): void
    {
        $marketing = User::factory()->create(['role' => 'marketing']);
        $this->actingAs($marketing)->get(route('admin.marketing.catering-menu.index'))->assertForbidden();

        $admin = User::factory()->create(['role' => 'marketing_admin']);
        $this->actingAs($admin)
            ->get(route('admin.catering-menu.index'))
            ->assertOk()
            ->assertSee('Manage Catering Menu');

        $legacyAdmin = User::factory()->create(['role' => 'marketing', 'is_admin' => true]);
        $this->actingAs($legacyAdmin)
            ->get(route('admin.marketing.catering-menu.index'))
            ->assertOk()
            ->assertSee('Manage Catering Menu');
    }

    public function test_marketing_admin_can_create_update_toggle_and_soft_delete_menu_item(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'marketing_admin']);
        $this->actingAs($admin);

        $this->post(route('admin.marketing.catering-menu.store'), [
            'name' => 'New Signature Dish',
            'description' => 'A new catering dish.',
            'category' => 'meat',
            'is_active' => '1',
            'image' => UploadedFile::fake()->createWithContent('dish.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=')),
        ])->assertRedirect(route('admin.marketing.catering-menu.index'));

        $item = CateringMenuItem::query()->where('name', 'New Signature Dish')->firstOrFail();
        Storage::disk('public')->assertExists($item->image);

        $this->put(route('admin.marketing.catering-menu.update', $item), [
            'name' => 'Updated Signature Dish',
            'description' => 'Updated description.',
            'category' => 'vegetables',
            'is_active' => '1',
        ])->assertRedirect(route('admin.marketing.catering-menu.index'));
        $this->assertDatabaseHas('catering_menu_items', [
            'id' => $item->id,
            'name' => 'Updated Signature Dish',
            'category' => 'vegetables',
        ]);

        $this->patch(route('admin.marketing.catering-menu.toggle', $item))->assertRedirect();
        $this->assertDatabaseHas('catering_menu_items', ['id' => $item->id, 'is_active' => false]);

        $this->delete(route('admin.marketing.catering-menu.destroy', $item))->assertRedirect();
        $this->assertSoftDeleted('catering_menu_items', ['id' => $item->id]);
    }

    public function test_only_active_items_are_shown_and_used_for_new_custom_bookings(): void
    {
        $active = CateringMenuItem::create(['name' => 'Active Custom Dish', 'category' => 'meat', 'is_active' => true]);
        CateringMenuItem::create(['name' => 'Inactive Custom Dish', 'category' => 'meat', 'is_active' => false]);

        $this->get(route('catering'))
            ->assertSee('Active Custom Dish')
            ->assertDontSee('Inactive Custom Dish');

        $branch = Branch::forceCreate([
            'name' => 'MixEat Commissary',
            'address' => 'Yap Fraternity Building',
            'opening_hours' => '8:00 AM - 4:00 PM',
            'distance' => '0 km away',
        ]);
        $payload = [
            'client_name' => 'Menu Customer',
            'email' => 'menu@example.com',
            'phone' => '09170000000',
            'event_date' => now()->addDays(10)->toDateString(),
            'event_time' => '12:00',
            'venue' => 'Cebu City',
            'event_type' => 'Birthday',
            'service_type' => 'Custom Catering',
            'guest_count' => 20,
            'menu_selections' => ['meat' => [$active->name]],
            'submission_token' => (string) Str::uuid(),
        ];

        $this->post(route('catering.inquiries.store'), $payload)->assertRedirect();
        $booking = CateringBooking::query()->latest('id')->firstOrFail();
        $this->assertSame([$active->name], $booking->menu_selections['selections']['meat']);
        $this->assertSame($branch->id, $booking->branch_id);

        $payload['submission_token'] = (string) Str::uuid();
        $payload['menu_selections'] = ['meat' => ['Inactive Custom Dish']];
        $this->post(route('catering.inquiries.store'), $payload)->assertSessionHasErrors('menu_selections.meat.0');
    }

    public function test_booking_keeps_historical_menu_details_after_item_changes(): void
    {
        $item = CateringMenuItem::create([
            'name' => 'Historical Dish',
            'description' => 'Original description.',
            'category' => 'meat',
            'is_active' => true,
        ]);
        $branch = Branch::forceCreate([
            'name' => 'MixEat Commissary',
            'address' => 'Yap Fraternity Building',
            'opening_hours' => '8:00 AM - 4:00 PM',
            'distance' => '0 km away',
        ]);

        $this->post(route('catering.inquiries.store'), [
            'client_name' => 'History Customer',
            'email' => 'history@example.com',
            'phone' => '09170000000',
            'event_date' => now()->addDays(10)->toDateString(),
            'event_time' => '12:00',
            'venue' => 'Cebu City',
            'event_type' => 'Birthday',
            'service_type' => 'Custom Catering',
            'guest_count' => 20,
            'menu_selections' => ['meat' => [$item->name]],
            'submission_token' => (string) Str::uuid(),
        ])->assertRedirect();

        $booking = CateringBooking::query()->latest('id')->firstOrFail();
        $item->update(['name' => 'Renamed Dish', 'description' => 'New description.']);
        $item->delete();

        $this->assertSame('Historical Dish', $booking->menu_selections['details']['meat'][0]['name']);
        $this->assertSame('Original description.', $booking->menu_selections['details']['meat'][0]['description']);
        $this->assertNotNull($branch->id);
    }
}
