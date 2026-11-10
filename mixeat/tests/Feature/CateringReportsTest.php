<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CateringBooking;
use App\Models\User;
use Tests\TestCase;

class CateringReportsTest extends TestCase
{
    public function test_only_active_commissary_supervisor_can_view_reports_and_unverified_money_is_excluded(): void
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
        $supervisor = User::factory()->create(['role' => 'supervisor', 'branch_id' => $commissary->id]);
        $otherSupervisor = User::factory()->create(['role' => 'supervisor', 'branch_id' => $otherBranch->id]);
        $marketing = User::factory()->create(['role' => 'marketing_admin']);

        $booking = CateringBooking::create([
            'client_name' => 'Report Customer',
            'email' => 'report@example.com',
            'phone' => '09170000000',
            'event_date' => now()->toDateString(),
            'service_type' => 'Packed Meals',
            'guest_count' => 10,
            'estimated_total' => 10000,
            'quote_amount' => 10000,
            'status' => 'Confirmed',
            'payment_status' => 'Deposit Verified / Partially Paid',
            'branch_id' => $commissary->id,
        ]);
        $booking->payments()->create([
            'payment_type' => 'deposit',
            'payment_method' => 'Security Bank QR',
            'amount' => 3000,
            'verified_amount' => 3000,
            'status' => 'Verified',
        ]);
        $booking->payments()->create([
            'payment_type' => 'deposit',
            'payment_method' => 'Security Bank QR',
            'amount' => 3000,
            'status' => 'Pending Verification',
        ]);

        $this->actingAs($supervisor)
            ->get('/admin/supervisors/catering-reports?period=this_year')
            ->assertOk()
            ->assertSee('Catering Reports')
            ->assertSee('₱3,000.00')
            ->assertSee('₱7,000.00');

        $this->actingAs($otherSupervisor)->get('/admin/supervisors/catering-reports')->assertForbidden();
        $this->actingAs($marketing)->get('/admin/supervisors/catering-reports')->assertForbidden();
    }

    public function test_csv_export_uses_the_same_report_access_control(): void
    {
        $branch = Branch::forceCreate([
            'name' => 'MixEat Commissary',
            'address' => 'Yap Fraternity Building',
            'opening_hours' => '8:00 AM - 4:00 PM',
            'distance' => '0 km away',
        ]);
        $supervisor = User::factory()->create(['role' => 'supervisor', 'branch_id' => $branch->id]);

        $this->actingAs($supervisor)
            ->get('/admin/supervisors/catering-reports/export/csv?period=this_month')
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $this->actingAs($supervisor)
            ->get('/admin/supervisors/catering-reports/export/xlsx?period=this_month')
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $this->actingAs($supervisor)
            ->get('/admin/supervisors/catering-reports/export/pdf?period=this_month')
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }
}
