<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CateringBooking;

class CateringController extends Controller
{
    public function marketing()
    {
        $bookings = CateringBooking::query()->with('trayItems')->latest()->paginate(20);
        $allBookings = CateringBooking::query()->get(['status', 'campaign_source', 'package_preference', 'created_at']);
        $conversionStatuses = ['Approved', 'In Preparation', 'Dispatched', 'Completed'];
        $conversionStatuses[] = 'Converted';
        $conversionStatuses[] = 'Contacted';
        $conversionRate = $allBookings->isEmpty() ? 0 : round(
            $allBookings->whereIn('status', $conversionStatuses)->count() / $allBookings->count() * 100,
            1
        );
        $inquiryTrends = $allBookings
            ->groupBy(fn (CateringBooking $booking) => $booking->created_at->format('Y-m'))
            ->map->count()
            ->sortKeysDesc()
            ->take(6);
        $popularPackages = $allBookings
            ->filter(fn (CateringBooking $booking) => filled($booking->package_preference))
            ->countBy('package_preference')
            ->sortDesc();
        $campaignStats = $allBookings
            ->groupBy(fn (CateringBooking $booking) => $booking->campaign_source ?: 'direct')
            ->map(fn ($campaignBookings) => [
                'inquiries' => $campaignBookings->count(),
                'converted' => $campaignBookings->whereIn('status', $conversionStatuses)->count(),
            ]);

        return view('admin.catering.marketing', [
            'bookings' => $bookings,
            'conversionRate' => $conversionRate,
            'inquiryTrends' => $inquiryTrends,
            'popularPackages' => $popularPackages,
            'campaignStats' => $campaignStats,
        ]);
    }

}
