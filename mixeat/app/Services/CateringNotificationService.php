<?php

namespace App\Services;

use App\Mail\AdminCateringBookingMail;
use App\Mail\CustomerCateringBookingMail;
use App\Models\CateringBooking;
use App\Notifications\CateringBookingNotification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class CateringNotificationService
{
    public function send(CateringBooking $booking, string $event, ?string $reason = null, ?string $eventKeySuffix = null): void
    {
        $booking->loadMissing(['user', 'payments']);
        $user = $booking->user;
        if (! $user && $booking->email) {
            $user = User::query()
                ->where('role', 'customer')
                ->whereRaw('LOWER(email) = ?', [strtolower($booking->email)])
                ->first();
            if ($user) {
                $booking->forceFill(['user_id' => $user->id])->save();
            }
        }

        $notification = new CateringBookingNotification($booking, $event, $reason, $eventKeySuffix);
        $data = $notification->toArray((object) []);
        $eventKey = $data['event_key'];

        if ($user && ! $user->notifications()
            ->where('type', CateringBookingNotification::class)
            ->whereJsonContains('data->event_key', $eventKey)
            ->exists()) {
            $user->notifications()->create([
                'id' => (string) Str::uuid(),
                'type' => CateringBookingNotification::class,
                'data' => $data,
            ]);
        }

        $recipients = [];
        if ($booking->email && filter_var($booking->email, FILTER_VALIDATE_EMAIL)) {
            $recipients['customer'] = strtolower($booking->email);
        }
        if (in_array($event, ['inquiry_submitted', 'quotation_accepted', 'cancelled'], true)
            && filter_var(config('mixeat.catering_email'), FILTER_VALIDATE_EMAIL)) {
            $recipients['business'] = strtolower((string) config('mixeat.catering_email'));
        }

        foreach ($recipients as $recipientType => $email) {
            $deliveryKey = $eventKey.':'.$recipientType;
            $delivery = DB::table('catering_email_deliveries')->where([
                'event_key' => $deliveryKey,
                'recipient_email' => $email,
            ])->first();
            if ($delivery) {
                continue;
            }

            $inserted = DB::table('catering_email_deliveries')->insertOrIgnore([
                'catering_booking_id' => $booking->id,
                'event_key' => $deliveryKey,
                'recipient_type' => $recipientType,
                'recipient_email' => $email,
                'status' => 'queued',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            if ($inserted === 0) {
                continue;
            }
            $deliveryId = DB::table('catering_email_deliveries')
                ->where('event_key', $deliveryKey)
                ->where('recipient_email', $email)
                ->value('id');
            if (! $deliveryId) {
                continue;
            }

            $mailClass = $recipientType === 'business'
                ? AdminCateringBookingMail::class
                : CustomerCateringBookingMail::class;
            $mail = new $mailClass($booking, $event, $reason, $eventKeySuffix, $deliveryId);
            DB::afterCommit(function () use ($mail, $email): void {
                try {
                    Mail::to($email)->queue($mail);
                } catch (Throwable $exception) {
                    Log::error('Catering email could not be queued.', [
                        'booking_id' => $mail->booking->getKey(),
                        'event' => $mail->event,
                        'recipient' => $email,
                        'exception' => $exception,
                    ]);
                }
            });
        }
    }
}
