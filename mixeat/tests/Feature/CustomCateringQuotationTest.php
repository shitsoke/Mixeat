<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CateringBooking;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class CustomCateringQuotationTest extends TestCase
{
    private function commissary(): Branch
    {
        return Branch::forceCreate([
            'name' => 'MixEat Commissary',
            'address' => 'Yap Fraternity Building',
            'opening_hours' => '8:00 AM - 4:00 PM',
            'distance' => '0 km away',
        ]);
    }

    public function test_custom_request_is_saved_without_payment_without_category_counts(): void
    {
        $branch = $this->commissary();
        $payload = [
            'client_name' => 'Custom Customer',
            'email' => 'custom@example.com',
            'phone' => '09170000000',
            'event_date' => now()->addDays(10)->toDateString(),
            'event_time' => '12:00',
            'venue' => 'Cebu City',
            'event_type' => 'Birthday',
            'service_type' => 'Custom Catering',
            'guest_count' => 30,
            'menu_selections' => ['meat' => ['Pork Sisig']],
            'submission_token' => (string) \Illuminate\Support\Str::uuid(),
        ];

        $this->post(route('catering.inquiries.store'), $payload)
            ->assertRedirect(route('catering').'#catering-inquiry')
            ->assertSessionHas('catering_status');
        $booking = CateringBooking::firstOrFail();
        $this->assertSame('Custom Catering', $booking->service_type);
        $this->assertSame('Pending Quotation', $booking->status);
        $this->assertSame('Unpaid', $booking->payment_status);
        $this->assertSame(0, $booking->payments()->count());
        $this->assertSame($branch->id, $booking->branch_id);

    }

    public function test_custom_request_accepts_multiple_dishes_per_category_and_rejects_unknown_dishes(): void
    {
        $this->commissary();
        $payload = [
            'client_name' => 'Multiple Dish Customer',
            'email' => 'multiple@example.com',
            'phone' => '09170000001',
            'event_date' => now()->addDays(10)->toDateString(),
            'event_time' => '12:00',
            'venue' => 'Cebu City',
            'event_type' => 'Birthday',
            'service_type' => 'Custom Catering',
            'guest_count' => 40,
            'menu_selections' => [
                'meat' => ['Pork Sisig', 'Beef Tapa', 'Chicken Curry', 'Salted Bangus'],
                'vegetables' => ['Chopsuey', 'Pakbet'],
                'soup' => ['Pochero Soup', 'Egg Drop Soup'],
                'pasta' => ['Bam-i'],
                'dessert' => ['Buko Pandan', 'Fruit Salad'],
                'rice' => ['Plain Rice', 'Garlic Rice'],
                'drinks' => ['Iced Tea', 'Lemonade'],
            ],
            'submission_token' => (string) \Illuminate\Support\Str::uuid(),
        ];

        $this->post(route('catering.inquiries.store'), $payload)
            ->assertRedirect(route('catering').'#catering-inquiry')
            ->assertSessionHas('catering_status');

        $booking = CateringBooking::firstOrFail();
        $this->assertSame($payload['menu_selections'], $booking->menu_selections['selections']);

        $payload['submission_token'] = (string) \Illuminate\Support\Str::uuid();
        $payload['menu_selections']['soup'][1] = 'Unknown Soup';
        $this->post(route('catering.inquiries.store'), $payload)
            ->assertSessionHasErrors('menu_selections.soup.1');
        $this->assertDatabaseCount('catering_bookings', 1);
    }

    public function test_commissary_supervisor_can_quote_and_customer_can_accept_then_submit_deposit(): void
    {
        $branch = $this->commissary();
        $supervisor = User::factory()->create(['role' => 'supervisor', 'branch_id' => $branch->id]);
        $customer = User::factory()->create(['role' => 'customer', 'email' => 'quote@example.com']);
        $booking = CateringBooking::create([
            'client_name' => 'Quote Customer',
            'email' => 'quote@example.com',
            'phone' => '09170000000',
            'event_date' => now()->addDays(10)->toDateString(),
            'event_time' => '12:00',
            'venue' => 'Cebu City',
            'event_type' => 'Birthday',
            'service_type' => 'Custom Catering',
            'package_preference' => 'Option 3 - Customize Your Own Package',
            'menu_selections' => ['counts' => ['meat' => 1], 'selections' => ['meat' => ['Pork Sisig']]],
            'guest_count' => 30,
            'status' => 'Pending Quotation',
            'payment_status' => 'Unpaid',
            'branch_id' => $branch->id,
            'user_id' => $customer->id,
        ]);

        $this->actingAs($supervisor)->post(route('admin.supervisors.catering-bookings.quotation', $booking), [
            'quote_amount' => 25000,
            'quotation_notes' => 'Includes delivery and setup.',
        ])->assertRedirect();
        $booking->refresh();
        $this->assertSame('Quotation Sent', $booking->status);
        $this->assertSame('25000.00', (string) $booking->quote_amount);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $customer->id,
            'type' => \App\Notifications\CateringBookingNotification::class,
        ]);

        $quotationUrl = URL::temporarySignedRoute('catering.quotation', now()->addMinutes(10), ['booking' => $booking->id]);
        $this->actingAs($customer)->get($quotationUrl)->assertOk()->assertSee('₱25,000.00');
        $acceptUrl = URL::temporarySignedRoute('catering.quotation.accept', now()->addMinutes(10), ['booking' => $booking->id]);
        $this->actingAs($customer)->post($acceptUrl)->assertRedirect();
        $booking->refresh();
        $this->assertSame('Awaiting Deposit', $booking->status);
        $this->assertSame('25000.00', (string) $booking->estimated_total);

        $depositUrl = URL::temporarySignedRoute('catering.quotation.deposit', now()->addMinutes(10), ['booking' => $booking->id]);
        $this->actingAs($customer)->post($depositUrl, ['payment_reference' => 'CUSTOM123', 'payment_confirmation' => '1'])->assertRedirect();
        $this->assertDatabaseHas('catering_payments', ['catering_booking_id' => $booking->id, 'amount' => 7500, 'status' => 'Pending Verification']);
    }

    public function test_commissary_supervisor_can_render_catering_bookings_page(): void
    {
        $branch = $this->commissary();
        $supervisor = User::factory()->create(['role' => 'supervisor', 'branch_id' => $branch->id]);
        CateringBooking::create([
            'client_name' => 'Page Customer',
            'email' => 'page@example.com',
            'phone' => '09170000002',
            'event_date' => now()->addDays(10)->toDateString(),
            'event_time' => '12:00',
            'venue' => 'Cebu City',
            'event_type' => 'Birthday',
            'service_type' => 'Custom Catering',
            'guest_count' => 20,
            'status' => 'Pending Quotation',
            'payment_status' => 'Unpaid',
            'branch_id' => $branch->id,
            'menu_selections' => [
                'counts' => ['meat' => 1],
                'selections' => ['meat' => ['Pork Sisig']],
            ],
        ]);

        $this->actingAs($supervisor)
            ->get(route('admin.supervisors.catering-bookings'))
            ->assertOk()
            ->assertSee('Catering Booking Requests');
    }

    public function test_authenticated_customer_can_open_legacy_unsigned_quotation_url(): void
    {
        $branch = $this->commissary();
        $customer = User::factory()->create(['role' => 'customer', 'email' => 'legacy@example.com']);
        $booking = CateringBooking::create([
            'client_name' => 'Legacy Customer',
            'email' => $customer->email,
            'phone' => '09170000003',
            'event_date' => now()->addDays(10)->toDateString(),
            'event_time' => '12:00',
            'venue' => 'Cebu City',
            'event_type' => 'Birthday',
            'service_type' => 'Custom Catering',
            'guest_count' => 20,
            'status' => 'Quotation Sent',
            'payment_status' => 'Unpaid',
            'branch_id' => $branch->id,
            'user_id' => $customer->id,
            'quotation_version' => 1,
            'quotation_status' => 'Sent',
            'quote_amount' => 10000,
        ]);
        $booking->quotationVersions()->create([
            'version' => 1,
            'amount' => 10000,
            'status' => 'Sent',
            'sent_at' => now(),
        ]);

        $this->actingAs($customer)
            ->get(route('catering.quotation', $booking))
            ->assertOk()
            ->assertSee('Your Customized Catering Quotation');
    }

    public function test_supervisor_page_displays_payment_status_separately(): void
    {
        $branch = $this->commissary();
        $supervisor = User::factory()->create(['role' => 'supervisor', 'branch_id' => $branch->id]);
        $booking = CateringBooking::create([
            'client_name' => 'Payment Customer',
            'email' => 'payment@example.com',
            'phone' => '09170000004',
            'event_date' => now()->addDays(10)->toDateString(),
            'event_time' => '12:00',
            'venue' => 'Cebu City',
            'event_type' => 'Birthday',
            'service_type' => 'Full-Service Catering',
            'guest_count' => 10,
            'estimated_total' => 10000,
            'status' => 'Confirmed',
            'payment_status' => 'Deposit Verified / Partially Paid',
            'branch_id' => $branch->id,
        ]);
        $booking->payments()->create([
            'payment_type' => 'deposit',
            'payment_method' => 'Security Bank QR',
            'amount' => 3000,
            'verified_amount' => 3000,
            'payment_reference' => 'PAYMENT123',
            'status' => 'Verified',
            'verified_at' => now(),
            'verified_by' => $supervisor->id,
        ]);

        $this->actingAs($supervisor)
            ->get(route('admin.supervisors.catering-bookings'))
            ->assertOk()
            ->assertSee('Booking Status')
            ->assertSee('Payment Status')
            ->assertSee('Deposit Paid (30%)')
            ->assertSee('Payment Summary');
    }
}
