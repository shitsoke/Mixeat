<?php

namespace App\Mail;

use App\Models\CateringBooking;
use App\Notifications\CateringBookingNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

abstract class CateringBookingMailBase extends Mailable implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public CateringBooking $booking;
    public string $event;
    public ?string $reason;
    public ?string $eventKeySuffix;
    public ?int $deliveryId;

    public function __construct(
        CateringBooking $booking,
        string $event,
        ?string $reason = null,
        ?string $eventKeySuffix = null,
        ?int $deliveryId = null,
    ) {
        $this->booking = $booking;
        $this->event = $event;
        $this->reason = $reason;
        $this->eventKeySuffix = $eventKeySuffix;
        $this->deliveryId = $deliveryId;
        $this->onQueue('notifications');
        $this->afterCommit();
    }

    public function backoff(): array
    {
        return [60, 300];
    }

    protected function notification(): CateringBookingNotification
    {
        return new CateringBookingNotification($this->booking, $this->event, $this->reason, $this->eventKeySuffix);
    }

    protected function data(): array
    {
        $notification = $this->notification();
        $data = $notification->toArray((object) []);
        $data['booking'] = $this->booking;
        $data['event'] = $this->event;
        $data['phone'] = $this->booking->phone;
        $data['venue'] = $this->booking->venue;
        $data['event_time'] = $this->booking->event_time;
        $data['event_type'] = $this->booking->event_type;
        $data['event_type_details'] = $this->booking->event_type_details;
        $data['guest_count'] = $this->booking->guest_count;
        $data['service_type'] = $this->booking->service_type;
        $data['menu_selections'] = $this->booking->menu_selections ?? [];
        $data['quotation_notes'] = $this->booking->quotation_notes;
        $data['quotation_version'] = $this->booking->quotation_version;
        $data['payments'] = $this->booking->payments()->latest('id')->get();
        $data['total_paid'] = (float) $this->booking->payments()
            ->where('status', 'Verified')
            ->sum('verified_amount') + (float) $this->booking->payments()
            ->where('status', 'Collected')
            ->sum('amount');
        $data['remaining_balance'] = max(
            0,
            (float) ($this->booking->estimated_total ?? $this->booking->quote_amount ?? 0) - $data['total_paid']
        );

        return $data;
    }

    public function failed(Throwable $exception): void
    {
        if ($this->deliveryId) {
            DB::table('catering_email_deliveries')
                ->where('id', $this->deliveryId)
                ->update([
                    'status' => 'failed',
                    'failure_message' => mb_substr($exception->getMessage(), 0, 65535),
                    'updated_at' => now(),
                ]);
        }

        Log::error('Catering email delivery failed.', [
            'booking_id' => $this->booking->getKey(),
            'event' => $this->event,
            'mail' => static::class,
            'exception' => $exception,
        ]);
    }
}
