<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CateringBooking;
use App\Models\CateringPartner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CateringManagementController extends Controller
{
    private const BOOKING_STATUSES = [
        'Pending Review',
        'New',
        'In Review',
        'Contacted',
        'Accepted',
        'Declined',
        'Approved',
        'Rejected',
        'In Preparation',
        'Dispatched',
        'Completed',
        'Converted',
        'Cancelled',
    ];

    public function dashboard()
    {
        return view('admin.marketing.dashboard', [
            'totalBookings' => CateringBooking::query()->count(),
            'awaitingReview' => CateringBooking::query()
                ->whereIn('status', ['New', 'Pending Review', 'In Review'])
                ->count(),
            'acceptedBookings' => CateringBooking::query()
                ->whereIn('status', ['Accepted', 'Approved', 'In Preparation', 'Dispatched', 'Completed'])
                ->count(),
            'visiblePartners' => CateringPartner::query()->where('is_visible', true)->count(),
        ]);
    }

    public function bookings()
    {
        return view('admin.marketing.catering-bookings', [
            'bookings' => CateringBooking::query()->latest()->paginate(20),
            'statuses' => self::BOOKING_STATUSES,
        ]);
    }

    public function updateBookingStatus(Request $request, CateringBooking $booking)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(self::BOOKING_STATUSES)],
        ]);

        $booking->update($data);

        return back()->with('admin_status', 'Catering booking status updated.');
    }

    public function calendar()
    {
        return view('admin.marketing.catering-calendar', [
            'bookings' => CateringBooking::query()
                ->with(['branch', 'assignedStaff'])
                ->orderBy('event_date')
                ->orderBy('event_time')
                ->get(),
            'branches' => Branch::query()->orderBy('name')->get(),
            'staffByBranch' => User::query()
                ->where('role', 'supervisor')
                ->with('branch')
                ->orderBy('name')
                ->get()
                ->groupBy('branch_id'),
            'statuses' => self::BOOKING_STATUSES,
        ]);
    }

    public function updateCalendarBooking(Request $request, CateringBooking $booking)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(self::BOOKING_STATUSES)],
            'payment_status' => ['required', Rule::in(['Unpaid', 'Deposit Paid', 'Fully Paid'])],
            'quote_amount' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'branch_id' => ['nullable', 'integer', Rule::exists('branches', 'id')],
            'assigned_staff_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query
                    ->where('role', 'supervisor')
                    ->where('branch_id', $request->input('branch_id'))),
            ],
        ]);

        $booking->update($data);

        return back()->with('admin_status', "Catering booking for {$booking->client_name} updated.");
    }

    public function partners()
    {
        return view('admin.marketing.catering-partners', [
            'partners' => CateringPartner::query()->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function storePartner(Request $request)
    {
        $data = $this->validatedPartner($request, true);
        unset($data['logo']);
        $data['logo_path'] = $request->file('logo')->store('catering-partners', 'public');
        $data['logo_url'] = null;

        CateringPartner::create($data);

        return back()->with('admin_status', 'Catering partner added.');
    }

    public function updatePartner(Request $request, CateringPartner $partner)
    {
        $data = $this->validatedPartner($request, false);
        unset($data['logo']);
        $oldLogo = $partner->logo_path;

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('catering-partners', 'public');
            $data['logo_url'] = null;
        }

        $partner->update($data);

        if ($request->hasFile('logo') && $oldLogo) {
            Storage::disk('public')->delete($oldLogo);
        }

        return back()->with('admin_status', 'Catering partner updated.');
    }

    public function deletePartner(CateringPartner $partner)
    {
        $logoPath = $partner->logo_path;
        $partner->delete();

        if ($logoPath) {
            Storage::disk('public')->delete($logoPath);
        }

        return back()->with('admin_status', 'Catering partner deleted.');
    }

    private function validatedPartner(Request $request, bool $requireLogo): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'website_url' => ['nullable', 'url', 'max:2048'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'is_visible' => ['nullable', 'boolean'],
            'logo' => [
                $requireLogo ? 'required' : 'nullable',
                'file',
                'mimes:png,jpg,jpeg,svg,webp',
                'max:5120',
            ],
        ]) + ['is_visible' => $request->boolean('is_visible')];
    }
}

