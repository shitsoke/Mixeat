<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CateringBooking;
use App\Models\SiteSetting;
use App\Services\CateringContent;
use Illuminate\Http\Request;

class CateringController extends Controller
{
    public function content()
    {
        return view('admin.catering.content', [
            'content' => app(CateringContent::class)->get(),
        ]);
    }

    public function marketing()
    {
        $bookings = CateringBooking::query()->latest()->paginate(20);
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

    public function updateContent(Request $request)
    {
        $data = $request->validate([
            'packages' => ['required', 'array', 'min:1', 'max:8'],
            'packages.*.name' => ['required', 'string', 'max:100'],
            'packages.*.price' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'packages.*.description' => ['required', 'string', 'max:1000'],
            'banner_title' => ['required', 'string', 'max:120'],
            'banner_copy' => ['required', 'string', 'max:255'],
            'highlights' => ['required', 'array', 'min:1', 'max:3'],
            'highlights.*' => ['required', 'string', 'max:120'],
        ]);

        foreach (['packages', 'highlights'] as $key) {
            SiteSetting::updateOrCreate(
                ['key' => 'catering_'.$key],
                ['value' => json_encode($data[$key], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)]
            );
        }
        foreach (['banner_title', 'banner_copy'] as $key) {
            SiteSetting::updateOrCreate(['key' => 'catering_'.$key], ['value' => $data[$key]]);
        }

        return back()->with('admin_status', 'Catering packages and landing page content updated.');
    }

}
