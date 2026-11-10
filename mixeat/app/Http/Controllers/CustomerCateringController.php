<?php

namespace App\Http\Controllers;

use App\Models\CateringBooking;
use Illuminate\Http\Request;

class CustomerCateringController extends Controller
{
    public function index(Request $request)
    {
        $bookings = CateringBooking::query()
            ->where('user_id', $request->user()->id)
            ->with(['payments', 'quotationVersions'])
            ->latest()
            ->paginate(10);

        return view('storefront.catering-bookings', compact('bookings'));
    }

    public function notifications(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()
            ->where('type', 'App\\Notifications\\CateringBookingNotification')
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn ($notification): array => [
                'id' => $notification->id,
                'data' => $notification->data,
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at?->diffForHumans(),
            ]);

        return response()->json([
            'count' => $user->unreadNotifications()->where('type', 'App\\Notifications\\CateringBookingNotification')->count(),
            'notifications' => $notifications,
        ]);
    }

    public function markNotificationRead(Request $request, string $id)
    {
        $notification = $request->user()->notifications()
            ->where('type', 'App\\Notifications\\CateringBookingNotification')
            ->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['ok' => true]);
    }
}
