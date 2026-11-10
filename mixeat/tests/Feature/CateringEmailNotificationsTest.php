<?php

namespace Tests\Feature;

use App\Mail\AdminCateringBookingMail;
use App\Mail\CustomerCateringBookingMail;
use App\Models\CateringBooking;
use App\Models\User;
use App\Services\CateringNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CateringEmailNotificationsTest extends TestCase
{
    public function test_catering_events_queue_customer_and_business_emails_without_duplicates(): void
    {
        Mail::fake();
        config(['mixeat.catering_email' => 'business@mixeat.test']);

        $user = User::factory()->create(['email' => 'customer@mixeat.test']);
        $booking = CateringBooking::create([
            'client_name' => 'Customer Test',
            'user_id' => $user->id,
            'email' => 'customer@mixeat.test',
            'phone' => '09171234567',
            'event_date' => now()->addWeek()->toDateString(),
            'event_time' => '12:00',
            'venue' => 'MixEat Test Venue',
            'event_type' => 'Birthday',
            'service_type' => 'Custom Catering',
            'package_preference' => 'Option 3 - Customize Your Own Package',
            'menu_selections' => ['selections' => ['meat' => ['Pork Sisig']]],
            'guest_count' => 20,
            'estimated_total' => 15000,
            'status' => 'Pending Quotation',
            'payment_status' => 'Unpaid',
        ]);
        $service = app(CateringNotificationService::class);
        $adminMail = new AdminCateringBookingMail($booking, 'inquiry_submitted');
        $customerMail = new CustomerCateringBookingMail($booking, 'inquiry_submitted');
        $this->assertSame('New Catering Booking - MXC-2026-000001', $adminMail->envelope()->subject);
        $this->assertSame('MixEat Catering - Booking Confirmation', $customerMail->envelope()->subject);
        $this->assertStringContainsString('MixEat Test Venue', $adminMail->render());
        $this->assertStringContainsString('Pork Sisig', $adminMail->render());
        $this->assertStringContainsString('another email will be sent', strtolower($customerMail->render()));

        DB::transaction(function () use ($service, $booking): void {
            $service->send($booking, 'inquiry_submitted');
        });
        $this->assertSame(2, DB::table('catering_email_deliveries')->count());
        Mail::assertQueued(AdminCateringBookingMail::class, 1);
        Mail::assertQueued(CustomerCateringBookingMail::class, 1);
        Mail::assertQueued(AdminCateringBookingMail::class, function (AdminCateringBookingMail $mail): bool {
            return $mail->hasTo('business@mixeat.test');
        });
        Mail::assertQueued(CustomerCateringBookingMail::class, function (CustomerCateringBookingMail $mail): bool {
            return $mail->hasTo('customer@mixeat.test');
        });

        $service->send($booking->fresh(), 'inquiry_submitted');
        $this->assertSame(2, DB::table('catering_email_deliveries')->count());
        Mail::assertQueued(AdminCateringBookingMail::class, 1);
        Mail::assertQueued(CustomerCateringBookingMail::class, 1);

        $service->send($booking->fresh(), 'approved');
        $service->send($booking->fresh(), 'declined');
        $declinedHtml = (new CustomerCateringBookingMail($booking, 'declined', 'Event date is unavailable.'))->render();
        $this->assertStringContainsString('Event date is unavailable.', $declinedHtml);
        $service->send($booking->fresh(), 'quotation_revised');
        $service->send($booking->fresh(), 'quotation_accepted');

        $payment = $booking->payments()->create([
            'payment_type' => 'deposit',
            'payment_method' => 'Security Bank QR',
            'amount' => 4500,
            'status' => 'Verified',
            'verified_amount' => 4500,
        ]);
        $service->send($booking->fresh(), 'deposit_verified', null, (string) $payment->id);
        $service->send($booking->fresh(), 'payment_confirmed', null, (string) $payment->id);
        $service->send($booking->fresh(), 'cancelled');
        $service->send($booking->fresh(), 'completed');

        $this->assertGreaterThanOrEqual(11, DB::table('catering_email_deliveries')->count());
        $this->assertDatabaseHas('catering_email_deliveries', [
            'recipient_type' => 'business',
            'recipient_email' => 'business@mixeat.test',
            'status' => 'queued',
        ]);
        $this->assertDatabaseHas('catering_email_deliveries', [
            'event_key' => 'catering:'.$booking->id.':cancelled:business',
            'recipient_email' => 'business@mixeat.test',
        ]);
    }

    public function test_failed_catering_email_is_recorded_without_affecting_booking_data(): void
    {
        $booking = CateringBooking::create([
            'client_name' => 'Failure Test',
            'email' => 'customer@mixeat.test',
            'phone' => '09171234567',
            'event_date' => now()->addWeek()->toDateString(),
            'event_type' => 'Meeting',
            'service_type' => 'Packed Meals',
            'guest_count' => 10,
            'status' => 'Awaiting Deposit Verification',
            'payment_status' => 'Pending Verification',
        ]);
        $deliveryId = DB::table('catering_email_deliveries')->insertGetId([
            'catering_booking_id' => $booking->id,
            'event_key' => 'failure-test',
            'recipient_type' => 'customer',
            'recipient_email' => $booking->email,
            'status' => 'queued',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $mail = new CustomerCateringBookingMail($booking, 'deposit_verified', null, null, $deliveryId);
        $mail->failed(new \RuntimeException('SMTP unavailable'));

        $this->assertDatabaseHas('catering_email_deliveries', [
            'id' => $deliveryId,
            'status' => 'failed',
            'failure_message' => 'SMTP unavailable',
        ]);
        $this->assertDatabaseHas('catering_bookings', [
            'id' => $booking->id,
            'status' => 'Awaiting Deposit Verification',
        ]);
    }

    public function test_authenticated_customers_receive_notifications_using_their_saved_email(): void
    {
        Mail::fake();
        config(['mixeat.catering_email' => 'business@mixeat.test']);

        $firstCustomer = User::factory()->create([
            'role' => 'customer',
            'email' => 'ragnarokgenshin@gmail.com',
        ]);
        $secondCustomer = User::factory()->create([
            'role' => 'customer',
            'email' => 'second-customer@mixeat.test',
        ]);

        $submitBooking = function (User $customer, string $spoofedEmail): CateringBooking {
            $response = $this->actingAs($customer)->post(route('catering.inquiries.store'), [
                'client_name' => $customer->name,
                'email' => $spoofedEmail,
                'submission_token' => (string) \Illuminate\Support\Str::uuid(),
                'phone' => '09171234567',
                'event_date' => now()->addWeek()->toDateString(),
                'event_time' => '12:00',
                'venue' => 'MixEat Test Venue',
                'event_type' => 'Birthday',
                'service_type' => 'Custom Catering',
                'guest_count' => 20,
                'custom_counts' => [
                    'meat' => 1,
                    'vegetables' => 0,
                    'soup' => 0,
                    'pasta' => 0,
                    'dessert' => 0,
                    'rice' => 0,
                    'drinks' => 0,
                ],
                'menu_selections' => ['meat' => ['Pork Sisig']],
            ]);

            $response->assertRedirect();

            return CateringBooking::query()->latest('id')->firstOrFail();
        };

        $firstBooking = $submitBooking($firstCustomer, 'pamakevin774@gmail.com');
        $secondBooking = $submitBooking($secondCustomer, 'ragnarokgenshin@gmail.com');

        $this->assertSame($firstCustomer->id, $firstBooking->user_id);
        $this->assertSame($firstCustomer->email, $firstBooking->email);
        $this->assertSame($secondCustomer->id, $secondBooking->user_id);
        $this->assertSame($secondCustomer->email, $secondBooking->email);

        Mail::assertQueued(CustomerCateringBookingMail::class, 2);
        Mail::assertQueued(CustomerCateringBookingMail::class, function (CustomerCateringBookingMail $mail): bool {
            return $mail->hasTo('ragnarokgenshin@gmail.com');
        });
        Mail::assertQueued(CustomerCateringBookingMail::class, function (CustomerCateringBookingMail $mail): bool {
            return $mail->hasTo('second-customer@mixeat.test');
        });
        Mail::assertQueued(AdminCateringBookingMail::class, 2);
        Mail::assertQueued(AdminCateringBookingMail::class, function (AdminCateringBookingMail $mail): bool {
            return $mail->hasTo('business@mixeat.test');
        });
    }
}
