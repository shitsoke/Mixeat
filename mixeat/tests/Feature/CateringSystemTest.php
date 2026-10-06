<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CateringBooking;
use App\Models\CateringPartner;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CateringSystemTest extends TestCase
{
    public function test_expired_session_page_explains_how_to_recover(): void
    {
        $this->view('errors.419')
            ->assertSee('Your session has expired')
            ->assertSee('Reopen Catering Admin')
            ->assertSee(route('login'));
    }

    public function test_catering_content_and_inquiry_form_live_on_their_own_page(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('Planning an Event or Office Party?')
            ->assertSee('Order your favorites online and enjoy delicious meals from your nearest MixEat branch.')
            ->assertSee('Good Food.')
            ->assertSee('Great Moments.')
            ->assertSee('text-5xl font-black leading-[1.1]', false)
            ->assertSee('Order Online')
            ->assertSee(route('menu'))
            ->assertSee('Book Catering')
            ->assertSee(route('catering'));

        $this->get('/catering?campaign=office-autumn')
            ->assertOk()
            ->assertSee('w-full px-4 md:px-7')
            ->assertSee('Planning an Event or Office Party?')
            ->assertSee('OUR CATERING CLIENTS &amp; PARTNERS', false)
            ->assertSee('data-package-name="Packed Meal Boxes"', false)
            ->assertSee('data-package-name="Party Tray Spread"', false)
            ->assertSee('data-package-name="Full Buffet Service"', false)
            ->assertSee('id="catering-package-dialog"', false)
            ->assertSee('catering-package-dialog', false)
            ->assertSee('Choose Package &amp; Book', false)
            ->assertSee('Book Catering')
            ->assertSee('id="catering-inquiry" class="w-full scroll-mt-24"', false)
            ->assertSee('office-autumn');
    }

    public function test_public_can_submit_a_valid_catering_inquiry(): void
    {
        $this->post('/catering/inquiries', [
            'client_name' => 'Morgan Lee',
            'email' => 'morgan@example.com',
            'phone' => '+639171234567',
            'company_name' => 'MixEat Test Company',
            'event_date' => now()->addDays(10)->toDateString(),
            'event_time' => '18:30',
            'venue' => 'Cebu City',
            'event_type' => 'Corporate',
            'service_type' => 'Party Trays',
            'package_preference' => 'Party Tray Spread',
            'guest_count' => 45,
            'budget_range' => '₱20,000–₱30,000',
            'special_requests' => 'Vegetarian options for six guests.',
            'campaign_source' => 'office-autumn',
        ])->assertRedirect('http://localhost/catering#catering-inquiry')
            ->assertSessionHas('catering_status');

        $this->assertDatabaseHas('catering_bookings', [
            'client_name' => 'Morgan Lee',
            'service_type' => 'Party Trays',
            'company_name' => 'MixEat Test Company',
            'event_type' => 'Corporate',
            'venue' => 'Cebu City',
            'budget_range' => '₱20,000–₱30,000',
            'package_preference' => 'Party Tray Spread',
            'guest_count' => 45,
            'campaign_source' => 'office-autumn',
            'status' => 'New',
            'payment_status' => 'Unpaid',
        ]);
    }

    public function test_invalid_catering_inquiry_is_rejected(): void
    {
        $this->from('/')->post('/catering/inquiries', [
            'client_name' => 'A',
            'email' => 'not-an-email',
            'phone' => '123',
            'event_date' => now()->subDay()->toDateString(),
            'service_type' => 'Delivery',
            'guest_count' => 0,
        ])->assertRedirect('/')
            ->assertSessionHasErrors(['client_name', 'email', 'phone', 'event_date', 'service_type', 'guest_count']);

        $this->assertDatabaseCount('catering_bookings', 0);
    }

    public function test_marketing_admin_can_view_leads_and_update_catering_content(): void
    {
        $admin = User::factory()->create(['role' => 'marketing']);
        $booking = CateringBooking::create([
            'client_name' => 'Alex Customer',
            'email' => 'alex@example.com',
            'phone' => '09171234567',
            'event_date' => now()->addDays(4),
            'service_type' => 'Packed Meals',
            'package_preference' => 'Team Lunch',
            'guest_count' => 30,
            'campaign_source' => 'spring-launch',
        ]);

        $response = $this->actingAs($admin)->get('/admin/catering')
            ->assertOk()
            ->assertSee('Alex Customer')
            ->assertSee('spring-launch')
            ->assertSee('Packages &amp; Promotions', false)
            ->assertDontSee('Catering Packages &amp; Promotions', false)
            ->assertSee('href="'.route('home').'">Home</a>', false);
        $response->assertSee('Catering', false);
        $this->assertSame(1, substr_count($response->getContent(), '>Catering</a>'), 'Catering should appear once in the Marketing Admin navbar.');
        foreach (['Branches', 'Master Menu', 'Promotions', 'About Page', 'Contact Page'] as $label) {
            $this->assertStringNotContainsString('>'.$label.'</a>', $response->getContent(), "{$label} should not appear in the Marketing Admin navbar.");
        }
        foreach (['Menu', 'Featured', 'Branches', 'About', 'Contact'] as $label) {
            $this->assertStringNotContainsString('>'.$label.'</a>', $response->getContent(), "{$label} should not appear in the Marketing Admin navigation.");
        }
        $this->actingAs($admin)->get('/admin/marketing/dashboard')
            ->assertOk()
            ->assertSee('Marketing Dashboard')
            ->assertSee('Catering booking requests')
            ->assertSee('Event Calendar &amp; Fulfillment', false);
        $this->actingAs($admin)->get('/admin/marketing/catering-bookings')->assertOk();
        $this->actingAs($admin)->get('/admin/marketing/catering-partners')->assertOk();
        $this->actingAs($admin)->get('/admin/catering/content')
            ->assertOk()
            ->assertSee('Catering Packages &amp; Promotions', false)
            ->assertSee('Packed Meal Boxes')
            ->assertSee('Save Catering Content');

        $this->actingAs($admin)->put('/admin/catering/content', [
            'packages' => [
                ['name' => 'Team Lunch', 'price' => 250, 'description' => 'An individual lunch box for teams.'],
            ],
            'banner_title' => 'Holiday Catering',
            'banner_copy' => 'Plan a festive event with MixEat.',
            'highlights' => ['Packed meals for teams'],
        ])->assertRedirect('/admin/catering/content');

        $this->assertSame('Holiday Catering', SiteSetting::where('key', 'catering_banner_title')->value('value'));
        $this->assertDatabaseHas('catering_bookings', ['id' => $booking->id]);

        $this->actingAs(User::factory()->create(['role' => 'supervisor', 'branch_id' => null]))
            ->get('/admin/catering')
            ->assertForbidden();
    }

    public function test_marketing_admin_can_review_booking_details_and_update_status(): void
    {
        $admin = User::factory()->create(['role' => 'marketing_admin']);
        $booking = CateringBooking::create([
            'client_name' => 'Taylor Client',
            'email' => 'taylor@example.com',
            'phone' => '09170000000',
            'company_name' => 'Taylor Corp',
            'event_date' => now()->addDays(3),
            'event_time' => '14:00',
            'venue' => 'Cebu Business Park',
            'event_type' => 'Office Party',
            'service_type' => 'Packed Meals',
            'guest_count' => 25,
            'budget_range' => '₱10,000–₱15,000',
            'special_requests' => 'Vegetarian options',
            'status' => 'New',
        ]);

        $this->actingAs($admin)->get('/admin/marketing/catering-bookings')
            ->assertOk()
            ->assertSee('Taylor Client')
            ->assertSee('Event Calendar &amp; Fulfillment', false)
            ->assertSee('Full details')
            ->assertSee('Taylor Corp')
            ->assertSee('Cebu Business Park')
            ->assertSee('Vegetarian options');
        $this->actingAs($admin)->patch('/admin/marketing/catering-bookings/'.$booking->id.'/status', [
            'status' => 'Accepted',
        ])->assertRedirect();

        $this->assertDatabaseHas('catering_bookings', ['id' => $booking->id, 'status' => 'Accepted']);

        $this->actingAs($admin)->patch('/admin/marketing/catering-bookings/'.$booking->id.'/status', [
            'status' => 'Declined',
        ])->assertRedirect();

        $this->assertDatabaseHas('catering_bookings', ['id' => $booking->id, 'status' => 'Declined']);

        $this->actingAs(User::factory()->create(['role' => 'customer']))
            ->get('/admin/marketing/catering-bookings')
            ->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'supervisor']))
            ->get('/admin/marketing/catering-partners')
            ->assertForbidden();
    }

    public function test_existing_marketing_admin_can_open_partner_logo_management(): void
    {
        $admin = User::factory()->create(['role' => 'marketing']);

        $this->actingAs($admin)->get('/admin/catering')
            ->assertOk()
            ->assertSee('Manage Clients &amp; Partners', false);

        $this->actingAs($admin)->get('/admin/marketing/catering-partners')
            ->assertOk()
            ->assertSee('Add a Partner')
            ->assertSee('Replace logo');
    }

    public function test_marketing_admin_role_can_open_catering_dashboard_but_supervisor_cannot(): void
    {
        $admin = User::factory()->create(['role' => 'marketing_admin']);

        $this->actingAs($admin)->get('/admin/catering')
            ->assertOk()
            ->assertSee('Catering Leads & Content', false)
            ->assertSee('href="'.route('home').'">Home</a>', false);

        $this->actingAs($admin)->get('/admin/marketing/dashboard')
            ->assertOk()
            ->assertSee('Marketing Dashboard');

        $this->actingAs(User::factory()->create(['role' => 'supervisor']))
            ->get('/admin/catering')
            ->assertForbidden();
    }

    public function test_marketing_admin_can_manage_partner_logos_and_public_grid_syncs(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'marketing_admin']);

        $this->actingAs($admin)->get('/admin/marketing/catering-partners')->assertOk()->assertSee('Add a Partner');
        $this->actingAs($admin)->post('/admin/marketing/catering-partners', [
            'name' => 'Cebu General Hospital',
            'website_url' => 'https://hospital.example',
            'sort_order' => 1,
            'is_visible' => 1,
            'logo' => UploadedFile::fake()->create('hospital.png', 1, 'image/png'),
        ])->assertRedirect();

        $partner = CateringPartner::where('name', 'Cebu General Hospital')->firstOrFail();
        Storage::disk('public')->assertExists($partner->logo_path);

        $this->get('/catering')
            ->assertOk()
            ->assertSee('Cebu General Hospital')
            ->assertSee('hospital.example')
            ->assertSee('catering-partner-marquee')
            ->assertSee('catering-partner-track')
            ->assertSee('catering-partner-logo')
            ->assertSee('aria-hidden="true" inert', false);

        $oldLogo = $partner->logo_path;
        $this->actingAs($admin)->put('/admin/marketing/catering-partners/'.$partner->id, [
            'name' => 'Cebu General Hospital',
            'website_url' => 'https://hospital.example',
            'sort_order' => 1,
            'is_visible' => 1,
            'logo' => UploadedFile::fake()->create('replacement.svg', 1, 'image/svg+xml'),
        ])->assertRedirect();
        $partner->refresh();
        $this->assertNotSame($oldLogo, $partner->logo_path);
        Storage::disk('public')->assertMissing($oldLogo);
        Storage::disk('public')->assertExists($partner->logo_path);

        $this->actingAs($admin)->put('/admin/marketing/catering-partners/'.$partner->id, [
            'name' => 'Cebu Medical Center',
            'website_url' => 'https://medical.example',
            'sort_order' => 0,
            'is_visible' => 0,
        ])->assertRedirect();

        $this->assertDatabaseHas('catering_partners', [
            'id' => $partner->id,
            'name' => 'Cebu Medical Center',
            'is_visible' => 0,
            'sort_order' => 0,
        ]);
        $this->get('/catering')->assertDontSee('Cebu Medical Center');

        $this->actingAs($admin)->delete('/admin/marketing/catering-partners/'.$partner->id)->assertRedirect();
        $this->assertDatabaseMissing('catering_partners', ['id' => $partner->id]);
        Storage::disk('public')->assertMissing($partner->logo_path);
    }

    public function test_marketing_can_manage_catering_calendar_and_supervisors_cannot(): void
    {
        $branch = Branch::create([
            'name' => 'Catering Branch',
            'address' => '1 Main Street',
            'opening_hours' => '9am-9pm',
            'distance' => '1 km',
        ]);
        $admin = User::factory()->create(['role' => 'marketing']);
        $staff = User::factory()->create(['role' => 'supervisor', 'branch_id' => $branch->id]);
        $booking = CateringBooking::create([
            'client_name' => 'Pat Client',
            'email' => 'pat@example.com',
            'phone' => '09170000000',
            'event_date' => now()->addDays(5),
            'service_type' => 'Full Buffet',
            'guest_count' => 80,
        ]);

        $this->actingAs($admin)->get('/admin/marketing/catering-calendar')
            ->assertOk()
            ->assertSee('Catering Event Calendar')
            ->assertSee('Pat Client')
            ->assertSee('Accepted')
            ->assertSee('Declined')
            ->assertSee('Catering Branch');

        $this->actingAs($admin)->patch('/admin/marketing/catering-calendar/'.$booking->id, [
            'status' => 'Accepted',
            'payment_status' => 'Deposit Paid',
            'quote_amount' => 12500,
            'branch_id' => $branch->id,
            'assigned_staff_id' => $staff->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('catering_bookings', [
            'id' => $booking->id,
            'branch_id' => $branch->id,
            'assigned_staff_id' => $staff->id,
            'status' => 'Accepted',
            'payment_status' => 'Deposit Paid',
            'quote_amount' => 12500,
        ]);

        $this->actingAs(User::factory()->create(['role' => 'supervisor', 'branch_id' => $branch->id]))
            ->get('/admin/marketing/catering-calendar')
            ->assertForbidden();

        $this->get('/admin/catering/bookings')->assertNotFound();
        $this->patch('/admin/catering/'.$booking->id)->assertNotFound();
    }
}
