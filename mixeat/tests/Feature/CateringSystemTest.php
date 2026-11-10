<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CateringBooking;
use App\Models\CateringPartner;
use App\Models\CateringPayment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
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
            ->assertSee('value="Meeting"', false)
            ->assertSee('value="Wedding"', false)
            ->assertSee('value="Birthday"', false)
            ->assertSee('value="Anniversary"', false)
            ->assertSee('value="Graduation"', false)
            ->assertSee('value="Other"', false)
            ->assertSee('name="event_type_details"', false)
            ->assertSee('Option 1')
            ->assertSee('₱550')
            ->assertSee('Pochero Soup')
            ->assertSee('Pork Salpicao')
            ->assertSee('data-catering-flow-panel', false)
            ->assertSee('id="catering-package-dialog"', false)
            ->assertSee('catering-package-dialog', false)
            ->assertSee('Choose Package &amp; Book', false)
            ->assertSee('Book Catering')
            ->assertSee('id="catering-inquiry"', false)
            ->assertSee('office-autumn');
    }

    public function test_catering_flow_renders_package_cards_and_menu_categories(): void
    {
        $this->get('/catering')
            ->assertOk()
            ->assertSee('Option 1')
            ->assertSee('₱550')
            ->assertSee('Option 2')
            ->assertSee('₱650')
            ->assertSee('Meat dishes: 0/0 selected')
            ->assertSee('Pochero Soup')
            ->assertSee('Sweet &amp; Sour Beef Balls', false)
            ->assertSee('Stir Fried Vegetables w/ Oyster Sauce')
            ->assertSee('Cucumber Juice')
            ->assertSee('data-catering-flow-panel', false)
            ->assertSee('form="catering-inquiry-form"', false)
            ->assertSee('id="catering-inquiry-form"', false)
            ->assertSee('id="catering-inquiry" class="w-full scroll-mt-24 hidden"', false);

        $this->get('/catering?service=Party%20Trays')
            ->assertOk()
            ->assertSee('id="party-trays-menu"', false)
            ->assertSee('data-party-trays-panel', false)
            ->assertSee('id="catering-inquiry" class="w-full scroll-mt-24 hidden"', false);
    }

    public function test_inquiry_form_displays_single_choice_event_types(): void
    {
        $response = $this->get('/catering')->assertOk();

        foreach (['Meeting', 'Wedding', 'Birthday', 'Anniversary', 'Graduation', 'Other'] as $eventType) {
            $response->assertSee('value="'.$eventType.'"', false);
        }

        $this->assertSame(6, substr_count($response->getContent(), 'data-event-type-option'));
        $response->assertSee('data-event-type-other-container', false)
            ->assertSee('name="event_type_details"', false);
    }

    public function test_public_can_submit_a_valid_party_tray_inquiry_with_server_pricing_and_merged_duplicates(): void
    {
        $payload = $this->partyTrayInquiryPayload((string) Str::uuid());
        $payload['tray_items'] = [
            ['category' => 'Pork Dishes', 'dish' => 'Pork Sisig', 'size' => 'full', 'quantity' => 2],
            ['category' => 'Pork Dishes', 'dish' => 'Pork Sisig', 'size' => 'full', 'quantity' => 1],
            ['category' => 'Seafood Dishes', 'dish' => 'Salted Bangus', 'size' => 'half', 'quantity' => 2],
        ];
        $payload['campaign_source'] = 'office-autumn';

        $this->post('/catering/inquiries', $payload)->assertRedirect('http://localhost/catering#catering-inquiry')
            ->assertSessionHas('catering_status');

        $this->assertDatabaseHas('catering_bookings', [
            'client_name' => 'Morgan Lee',
            'service_type' => 'Party Trays',
            'company_name' => 'MixEat Test Company',
            'event_type' => 'Corporate',
            'venue' => 'Cebu City',
            'budget_range' => '₱20,000–₱30,000',
            'package_preference' => 'Food Trays & Buffet Setups (2 tray selections)',
            'guest_count' => 45,
            'campaign_source' => 'office-autumn',
            'status' => 'Awaiting Deposit Verification',
            'payment_status' => 'Pending Verification',
            'estimated_total' => 3915,
        ]);

        $booking = CateringBooking::where('email', 'morgan@example.com')->firstOrFail();
        $this->assertSame('1174.50', $booking->payments()->firstOrFail()->amount);
        $this->assertSame('Pending Verification', $booking->payments()->firstOrFail()->status);
        $this->assertCount(2, $booking->trayItems);
        $this->assertDatabaseHas('catering_tray_items', [
            'catering_booking_id' => $booking->id,
            'category' => 'Pork Dishes',
            'dish' => 'Pork Sisig',
            'tray_size' => 'full',
            'quantity' => 3,
            'unit_price' => 799,
            'subtotal' => 2397,
        ]);
        $this->assertDatabaseHas('catering_tray_items', [
            'catering_booking_id' => $booking->id,
            'dish' => 'Salted Bangus',
            'tray_size' => 'half',
            'quantity' => 2,
            'unit_price' => 759,
            'subtotal' => 1518,
        ]);
    }

    public function test_party_trays_require_valid_nonempty_items_and_reject_client_prices(): void
    {
        $payload = $this->partyTrayInquiryPayload((string) Str::uuid());
        $this->post('/catering/inquiries', $payload)->assertSessionHasErrors(['tray_items']);

        $payload['tray_items'] = [[
            'category' => 'Pork Dishes',
            'dish' => 'Beef Tapa',
            'size' => 'half',
            'quantity' => 1,
        ]];
        $payload['submission_token'] = (string) Str::uuid();
        $this->post('/catering/inquiries', $payload)->assertSessionHasErrors(['tray_items.0.dish']);

        $payload['tray_items'] = [[
            'category' => 'Beef Dishes',
            'dish' => 'Beef Tapa',
            'size' => 'half',
            'quantity' => 1,
            'unit_price' => 1,
        ]];
        $payload['submission_token'] = (string) Str::uuid();
        $this->post('/catering/inquiries', $payload)->assertSessionHasErrors(['tray_items.0']);

        $payload['tray_items'] = [[
            'category' => 'Beef Dishes',
            'dish' => 'Beef Tapa',
            'size' => 'half',
            'quantity' => 0,
        ]];
        $payload['submission_token'] = (string) Str::uuid();
        $this->post('/catering/inquiries', $payload)->assertSessionHasErrors(['tray_items.0.quantity']);

        $payload['tray_items'][0]['quantity'] = 1;
        $payload['submission_token'] = (string) Str::uuid();
        $this->post('/catering/inquiries', $payload)->assertRedirect();
        $this->assertDatabaseCount('catering_bookings', 1);
    }

    public function test_party_trays_require_a_unique_submission_token(): void
    {
        $payload = $this->partyTrayInquiryPayload((string) Str::uuid());
        $payload['tray_items'] = [[
            'category' => 'Beef Dishes',
            'dish' => 'Beef Tapa',
            'size' => 'half',
            'quantity' => 1,
        ]];
        $this->post('/catering/inquiries', $payload)->assertRedirect();
        $this->post('/catering/inquiries', $payload)->assertSessionHasErrors(['submission_token']);
        $this->assertDatabaseCount('catering_bookings', 1);
    }

    public function test_admin_can_view_complete_party_tray_inquiry_details(): void
    {
        $payload = $this->partyTrayInquiryPayload((string) Str::uuid());
        $payload['tray_items'] = [[
            'category' => 'Chicken Dishes',
            'dish' => 'Chicken Cordon Bleu',
            'size' => 'full',
            'quantity' => 2,
        ]];
        $this->post('/catering/inquiries', $payload);

        $admin = User::factory()->create(['role' => 'marketing_admin']);
        $this->actingAs($admin)->get('/admin/marketing/catering-bookings')
            ->assertOk()
            ->assertSee('Food Trays &amp; Buffet Setups', false)
            ->assertSee('Chicken Dishes')
            ->assertSee('Chicken Cordon Bleu')
            ->assertSee('Full')
            ->assertSee('1,398.00');
        $this->actingAs($admin)->get('/admin/catering')
            ->assertOk()
            ->assertSee('Chicken Cordon Bleu')
            ->assertSee('1,398.00');
    }

    public function test_packed_meal_packages_show_all_seven_options_and_inclusions(): void
    {
        $response = $this->get('/catering')
            ->assertOk()
            ->assertSee('Choose your meal package')
            ->assertSee('Bam-i or Vegetables')
            ->assertSee('Dessert/Fruits')
            ->assertSee('Select Package A')
            ->assertSee('Select Package G');

        foreach ([
            'Crispy Fried Chicken',
            '2 pc Burger Steak w/ Gravy',
            'Pork Sisig',
            'Sweet &amp; Sour Fish Fillet',
            'Creamy Pork Steak',
            'Pork Bistek Tagalog',
            'Chicken Bicol Express',
        ] as $mainDish) {
            $response->assertSee($mainDish, false);
        }

        $this->assertSame(7, substr_count($response->getContent(), 'data-packed-meal-card='));
        $this->assertSame(7, substr_count($response->getContent(), '₱250</span>'));
    }

    public function test_public_can_submit_a_packed_meal_inquiry_and_duplicate_token_is_rejected(): void
    {
        $token = (string) Str::uuid();
        $payload = $this->packedMealInquiryPayload($token);

        $this->from('/catering')->post('/catering/inquiries', $payload)
            ->assertRedirect('http://localhost/catering#catering-inquiry')
            ->assertSessionHas('catering_status');

        $booking = CateringBooking::firstOrFail();
        $this->assertSame('Package G - Chicken Bicol Express', $booking->package_preference);
        $this->assertSame('250.00', $booking->package_price_per_pax);
        $this->assertSame('3000.00', $booking->estimated_total);
        $this->assertSame('900.00', $booking->payments()->firstOrFail()->amount);
        $this->assertSame('000012345678', $booking->payments()->firstOrFail()->payment_reference);
        $this->assertSame('Awaiting Deposit Verification', $booking->status);
        $this->assertSame('Pending Verification', $booking->payment_status);
        $this->assertSame(12, $booking->guest_count);
        $this->assertSame([
            'package' => 'Package G',
            'main_dish' => 'Chicken Bicol Express',
            'side' => 'Vegetables',
            'rice' => 'Rice',
            'dessert' => 'Dessert/Fruits',
            'drinks' => 'Drinks',
        ], $booking->menu_selections);

        $this->from('/catering')->post('/catering/inquiries', $payload)
            ->assertSessionHasErrors('submission_token');
        $this->assertDatabaseCount('catering_bookings', 1);
    }

    public function test_packed_meal_inquiry_rejects_invalid_package_side_and_quantity(): void
    {
        $payload = $this->packedMealInquiryPayload((string) Str::uuid());
        $payload['packed_meal_package'] = 'H';
        $payload['packed_meal_side'] = 'Fries';
        $payload['guest_count'] = 0;

        $this->from('/catering')->post('/catering/inquiries', $payload)
            ->assertSessionHasErrors(['packed_meal_package', 'packed_meal_side', 'guest_count']);

        $payload = $this->packedMealInquiryPayload((string) Str::uuid());
        $payload['packed_meal_package'] = ['A'];
        $payload['packed_meal_side'] = ['Vegetables'];
        $this->from('/catering')->post('/catering/inquiries', $payload)
            ->assertSessionHasErrors(['packed_meal_package', 'packed_meal_side']);

        $this->assertDatabaseCount('catering_bookings', 0);
    }

    public function test_packed_meal_inquiry_requires_reference_and_payment_confirmation(): void
    {
        $payload = $this->packedMealInquiryPayload((string) Str::uuid());
        unset($payload['payment_reference'], $payload['payment_confirmation']);

        $this->from('/catering')->post('/catering/inquiries', $payload)
            ->assertSessionHasErrors(['payment_reference', 'payment_confirmation']);
        $this->assertDatabaseCount('catering_bookings', 0);
    }

    public function test_duplicate_payment_references_are_flagged_for_staff_review(): void
    {
        $first = $this->packedMealInquiryPayload((string) Str::uuid());
        $this->post('/catering/inquiries', $first)->assertRedirect();

        $second = $this->packedMealInquiryPayload((string) Str::uuid());
        $second['payment_reference'] = $first['payment_reference'];
        $this->post('/catering/inquiries', $second)->assertRedirect();

        $this->assertSame(2, CateringPayment::query()->where('duplicate_reference', true)->count());
    }

    public function test_marketing_admin_can_verify_reject_and_recollect_deposit_payments(): void
    {
        $payload = $this->packedMealInquiryPayload((string) Str::uuid());
        $this->post('/catering/inquiries', $payload)->assertRedirect();
        $booking = CateringBooking::firstOrFail();
        $deposit = $booking->payments()->firstOrFail();
        $admin = User::factory()->create(['role' => 'marketing_admin']);

        $this->actingAs($admin)->get('/admin/marketing/catering-bookings')
            ->assertOk()
            ->assertSee($payload['payment_reference'])
            ->assertSee('Verify deposit &amp; confirm', false);
        $this->actingAs($admin)->patch("/admin/marketing/catering-calendar/{$booking->id}", [
            'status' => 'Delivered',
            'payment_status' => 'Fully Paid',
            'quote_amount' => 3000,
        ])->assertSessionHasErrors('status');
        $this->assertSame('Pending Verification', $booking->fresh()->payment_status);

        $this->actingAs($admin)->post("/admin/marketing/catering-bookings/{$booking->id}/payments/{$deposit->id}/verify")
            ->assertRedirect();
        $this->assertDatabaseHas('catering_bookings', [
            'id' => $booking->id,
            'status' => 'Confirmed',
            'payment_status' => 'Deposit Verified / Partially Paid',
        ]);
        $this->assertDatabaseHas('catering_payments', [
            'id' => $deposit->id,
            'status' => 'Verified',
            'verified_by' => $admin->id,
            'verified_amount' => 900,
        ]);

        $this->post("/admin/marketing/catering-bookings/{$booking->id}/payments/balance", [
            'amount' => 2100.01,
            'payment_method' => 'Cash',
        ])->assertSessionHasErrors('amount');

        $this->post("/admin/marketing/catering-bookings/{$booking->id}/payments/balance", [
            'amount' => 500,
            'payment_method' => 'Cash',
        ])->assertRedirect();
        $this->assertDatabaseHas('catering_bookings', [
            'id' => $booking->id,
            'payment_status' => 'Deposit Verified / Partially Paid',
        ]);

        $this->post("/admin/marketing/catering-bookings/{$booking->id}/payments/balance", [
            'amount' => 1600,
            'payment_method' => 'Bank Transfer',
        ])->assertRedirect();
        $this->assertDatabaseHas('catering_bookings', [
            'id' => $booking->id,
            'payment_status' => 'Fully Paid',
        ]);

        $this->patch("/admin/marketing/catering-calendar/{$booking->id}", [
            'status' => 'Delivered',
            'quote_amount' => 3000,
            'branch_id' => '',
            'assigned_staff_id' => '',
            'payment_status' => 'Unpaid',
        ])->assertRedirect();
        $this->assertDatabaseHas('catering_bookings', [
            'id' => $booking->id,
            'status' => 'Delivered',
            'payment_status' => 'Fully Paid',
        ]);
    }

    public function test_rejected_deposit_can_be_corrected_using_a_signed_link(): void
    {
        $payload = $this->packedMealInquiryPayload((string) Str::uuid());
        $this->post('/catering/inquiries', $payload)->assertRedirect();
        $booking = CateringBooking::firstOrFail();
        $deposit = $booking->payments()->firstOrFail();
        $admin = User::factory()->create(['role' => 'marketing']);

        $this->actingAs($admin)->post("/admin/marketing/catering-bookings/{$booking->id}/payments/{$deposit->id}/reject", [
            'verification_remarks' => 'The submitted reference does not match the deposit.',
        ])->assertRedirect();

        $url = URL::temporarySignedRoute(
            'catering.payments.correction',
            now()->addMinutes(10),
            ['booking' => $booking->id]
        );
        $this->get($url)->assertOk()->assertSee('Update your deposit reference');
        $this->post(str_replace('/correction?', '/correction?', $url), [
            'payment_reference' => '000098765432',
            'payment_confirmation' => '1',
        ])->assertOk()->assertSee('Corrected reference submitted');
        $this->assertSame('Pending Verification', $booking->fresh()->payment_status);
        $this->assertSame(2, $booking->payments()->count());
    }

    public function test_other_event_type_requires_and_saves_details(): void
    {
        $payload = $this->packedMealInquiryPayload((string) Str::uuid());
        $payload['event_type'] = 'Other';

        $this->from('/catering')->post('/catering/inquiries', $payload)
            ->assertSessionHasErrors('event_type_details');
        $this->assertDatabaseCount('catering_bookings', 0);

        $payload['event_type_details'] = 'Family reunion';
        $this->post('/catering/inquiries', $payload)
            ->assertRedirect('http://localhost/catering#catering-inquiry')
            ->assertSessionHas('catering_status');

        $this->assertDatabaseHas('catering_bookings', [
            'event_type' => 'Other',
            'event_type_details' => 'Family reunion',
        ]);
        $this->actingAs(User::factory()->create(['role' => 'marketing_admin']))
            ->get('/admin/marketing/catering-bookings')
            ->assertOk()
            ->assertSee('Other: Family reunion');
    }

    public function test_admin_can_view_complete_packed_meal_inquiry_details(): void
    {
        $admin = User::factory()->create(['role' => 'marketing_admin']);
        CateringBooking::create([
            'client_name' => 'Packed Meal Client',
            'email' => 'packed@example.com',
            'phone' => '09171234567',
            'event_date' => now()->addDays(3),
            'event_time' => '12:00',
            'venue' => 'Cebu City',
            'event_type' => 'Corporate',
            'event_type_details' => null,
            'service_type' => 'Packed Meals',
            'package_preference' => 'Package B - 2 pc Burger Steak w/ Gravy',
            'package_price_per_pax' => 250,
            'estimated_total' => 5000,
            'guest_count' => 20,
            'menu_selections' => [
                'package' => 'Package B',
                'main_dish' => '2 pc Burger Steak w/ Gravy',
                'side' => 'Bam-i',
                'rice' => 'Rice',
                'dessert' => 'Dessert/Fruits',
                'drinks' => 'Drinks',
            ],
        ]);

        $this->actingAs($admin)->get('/admin/marketing/catering-bookings')
            ->assertOk()
            ->assertSee('Package B')
            ->assertSee('2 pc Burger Steak w/ Gravy')
            ->assertSee('Bam-i')
            ->assertSee('Rice')
            ->assertSee('Dessert/Fruits')
            ->assertSee('Drinks')
            ->assertSee('Number of meals')
            ->assertSee('Price per meal')
            ->assertSee('5,000.00');
        $this->actingAs($admin)->get('/admin/catering')
            ->assertOk()
            ->assertSee('Packed meal package')
            ->assertSee('2 pc Burger Steak w/ Gravy')
            ->assertSee('Dessert/Fruits');
    }

    public function test_public_can_submit_a_full_service_package_with_valid_menu_selections(): void
    {
        $token = (string) Str::uuid();
        $this->from('/catering')->post('/catering/inquiries', $this->fullServiceInquiryPayload($token))
            ->assertRedirect('http://localhost/catering#catering-inquiry')
            ->assertSessionHas('catering_status');

        $booking = CateringBooking::firstOrFail();
        $this->assertSame('Option 1 (₱550 per pax)', $booking->package_preference);
        $this->assertSame('550.00', $booking->package_price_per_pax);
        $this->assertSame('27500.00', $booking->estimated_total);
        $this->assertSame('8250.00', $booking->payments()->firstOrFail()->amount);
        $this->assertSame(['Pork Salpicao', 'Beef Tapa'], $booking->menu_selections['meat']);
        $this->assertSame('Pochero Soup', $booking->menu_selections['soup']);
        $this->assertSame($token, $booking->submission_token);

        $this->from('/catering')->post('/catering/inquiries', $this->fullServiceInquiryPayload($token))
            ->assertSessionHasErrors('submission_token');
        $this->assertDatabaseCount('catering_bookings', 1);
    }

    public function test_full_service_package_rejects_wrong_meat_count_and_invalid_menu_items(): void
    {
        $payload = $this->fullServiceInquiryPayload((string) Str::uuid());
        $payload['menu_selections']['meat'][] = 'Chicken Curry';

        $this->from('/catering')->post('/catering/inquiries', $payload)
            ->assertSessionHasErrors('menu_selections.meat');
        $this->assertDatabaseCount('catering_bookings', 0);

        $payload = $this->fullServiceInquiryPayload((string) Str::uuid());
        $payload['menu_selections']['soup'] = 'Invented Soup';
        $this->from('/catering')->post('/catering/inquiries', $payload)
            ->assertSessionHasErrors('menu_selections.soup');
        $this->assertDatabaseCount('catering_bookings', 0);
    }

    public function test_option_two_accepts_exactly_three_meat_dishes(): void
    {
        $payload = $this->fullServiceInquiryPayload((string) Str::uuid(), 'option_650');
        $payload['menu_selections']['meat'][] = 'Chicken Curry';

        $this->post('/catering/inquiries', $payload)
            ->assertRedirect('http://localhost/catering#catering-inquiry')
            ->assertSessionHas('catering_status');

        $booking = CateringBooking::firstOrFail();
        $this->assertSame('Option 2 (₱650 per pax)', $booking->package_preference);
        $this->assertSame('650.00', $booking->package_price_per_pax);
        $this->assertSame('32500.00', $booking->estimated_total);
        $this->assertCount(3, $booking->menu_selections['meat']);
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

    public function test_marketing_admin_can_view_leads_without_catering_content_management(): void
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
            ->assertDontSee('Catering Content')
            ->assertDontSee('Packages &amp; Promotions', false)
            ->assertDontSee('View Catering Landing Page')
            ->assertSee('Manage Clients &amp; Partners', false)
            ->assertDontSee('Catering Packages &amp; Promotions', false)
            ->assertSee('href="'.route('home').'">Home</a>', false)
            ->assertSee('class="btn btn-sm rounded-pill px-3 logout-button" type="submit">Logout</button>', false);
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
            ->assertSee('Event Calendar &amp; Fulfillment', false)
            ->assertDontSee('Catering Content');
        $this->actingAs($admin)->get('/admin/marketing/catering-bookings')->assertOk();
        $this->actingAs($admin)->get('/admin/marketing/catering-partners')->assertOk();
        $this->actingAs($admin)->get('/admin/catering/content')->assertNotFound();
        $this->actingAs($admin)->put('/admin/catering/content', [])->assertNotFound();
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
            'package_price_per_pax' => 550,
            'estimated_total' => 13750,
            'menu_selections' => [
                'meat' => ['Pork Salpicao', 'Beef Tapa'],
                'soup' => 'Pochero Soup',
                'vegetables' => 'Chopsuey',
                'pasta' => 'Bam-i',
                'dessert' => 'Buko Pandan',
                'rice' => 'Plain Rice',
                'drinks' => 'Iced Tea',
            ],
            'status' => 'New',
        ]);

        $this->actingAs($admin)->get('/admin/marketing/catering-bookings')
            ->assertOk()
            ->assertSee('catering-admin-theme', false)
            ->assertSee('Taylor Client')
            ->assertSee('Event Calendar &amp; Fulfillment', false)
            ->assertSee('Full details')
            ->assertSee('Taylor Corp')
            ->assertSee('Cebu Business Park')
            ->assertSee('Vegetarian options')
            ->assertSee('Pork Salpicao')
            ->assertSee('Pochero Soup')
            ->assertSee('13,750.00');
        $this->actingAs($admin)->get('/admin/catering')
            ->assertOk()
            ->assertSee('Catering Inquiries')
            ->assertSee('Meat:')
            ->assertSee('Pork Salpicao')
            ->assertSee('Soup:')
            ->assertSee('Pochero Soup');
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
            ->assertSee('Catering Leads & Inquiries', false)
            ->assertDontSee('Catering Content')
            ->assertSee('href="'.route('home').'">Home</a>', false);

        $this->actingAs($admin)->get('/admin/marketing/dashboard')
            ->assertOk()
            ->assertSee('Marketing Dashboard')
            ->assertDontSee('Catering Content');
        $this->actingAs($admin)->get('/admin/catering/content')->assertNotFound();

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
        $branch = Branch::forceCreate([
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
            ->assertSee('catering-admin-theme', false)
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

    private function fullServiceInquiryPayload(string $token, string $package = 'option_550'): array
    {
        return [
            'client_name' => 'Jordan Rivera',
            'email' => 'jordan@example.com',
            'phone' => '09171234567',
            'event_date' => now()->addDays(10)->toDateString(),
            'event_time' => '18:30',
            'venue' => 'Cebu City',
            'event_type' => 'Birthday',
            'service_type' => 'Full-Service Catering',
            'catering_package' => $package,
            'guest_count' => 50,
            'menu_selections' => [
                'meat' => ['Pork Salpicao', 'Beef Tapa'],
                'soup' => 'Pochero Soup',
                'vegetables' => 'Chopsuey',
                'pasta' => 'Bam-i',
                'dessert' => 'Buko Pandan',
                'rice' => 'Plain Rice',
                'drinks' => 'Iced Tea',
            ],
            'submission_token' => $token,
            'payment_reference' => 'TXN'.strtoupper(substr(str_replace('-', '', $token), 0, 12)),
            'payment_confirmation' => '1',
        ];
    }

    private function packedMealInquiryPayload(string $token): array
    {
        return [
            'client_name' => 'Jordan Rivera',
            'email' => 'jordan@example.com',
            'phone' => '09171234567',
            'event_date' => now()->addDays(10)->toDateString(),
            'event_time' => '18:30',
            'venue' => 'Cebu City',
            'event_type' => 'Corporate',
            'service_type' => 'Packed Meals',
            'packed_meal_package' => 'G',
            'packed_meal_side' => 'Vegetables',
            'guest_count' => 12,
            'submission_token' => $token,
            'payment_reference' => '000012345678',
            'payment_confirmation' => '1',
        ];
    }

    private function partyTrayInquiryPayload(string $token): array
    {
        return [
            'client_name' => 'Morgan Lee',
            'email' => 'morgan@example.com',
            'phone' => '+639171234567',
            'company_name' => 'MixEat Test Company',
            'event_date' => now()->addDays(10)->toDateString(),
            'event_time' => '18:30',
            'venue' => 'Cebu City',
            'event_type' => 'Corporate',
            'service_type' => 'Party Trays',
            'guest_count' => 45,
            'budget_range' => '₱20,000–₱30,000',
            'special_requests' => 'Vegetarian options for six guests.',
            'submission_token' => $token,
            'payment_reference' => 'TXN'.strtoupper(substr(str_replace('-', '', $token), 0, 12)),
            'payment_confirmation' => '1',
        ];
    }
}
