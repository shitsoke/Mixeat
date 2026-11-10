<?php

namespace App\Notifications;

use App\Models\CateringBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class CateringBookingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public CateringBooking $booking,
        public string $event,
        public ?string $reason = null,
        public ?string $eventKeySuffix = null,
    ) {
        $this->onQueue('notifications');
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return $this->payload() + [
            'type' => 'catering_booking',
            'event_key' => $this->eventKey(),
        ];
    }

    private function payload(): array
    {
        return [
            'title' => $this->title(),
            'client_name' => $this->booking->client_name,
            'message' => $this->message(),
            'status' => $this->booking->status,
            'booking_id' => $this->booking->id,
            'booking_reference' => $this->reference(),
            'booking_url' => $this->url(),
            'event_date' => $this->booking->event_date?->format('F j, Y'),
            'package' => $this->booking->package_preference,
            'amount' => $this->booking->quote_amount ?: $this->booking->estimated_total,
            'reason' => $this->reason,
        ];
    }

    private function title(): string
    {
        return match ($this->event) {
            'inquiry_submitted' => 'Catering inquiry received',
            'quotation_sent' => 'Your catering quotation is ready',
            'quotation_revised' => 'Your catering quotation was revised',
            'quotation_accepted' => 'Catering quotation accepted',
            'quotation_declined' => 'Catering quotation declined',
            'deposit_submitted' => 'Catering deposit submitted',
            'deposit_verified' => 'Catering booking confirmed',
            'payment_confirmed' => 'Catering payment confirmed',
            'deposit_rejected' => 'Catering deposit needs correction',
            'confirmed' => 'Catering booking confirmed',
            'approved' => 'Catering booking approved',
            'declined' => 'Catering booking declined',
            'cancelled' => 'Catering booking cancelled',
            'preparing' => 'Your catering order is being prepared',
            'out_for_delivery' => 'Your catering order is out for delivery',
            'delivered', 'completed' => 'Catering booking completed',
            default => 'Catering booking updated',
        };
    }

    private function message(): string
    {
        return match ($this->event) {
            'inquiry_submitted' => 'Your catering inquiry was received and is pending review.',
            'quotation_sent', 'quotation_revised' => 'Review your quotation before accepting or declining it.',
            'quotation_accepted' => 'Your quotation was accepted. You may now submit the required deposit.',
            'quotation_declined' => 'Your quotation was declined.',
            'deposit_submitted' => 'Your deposit reference was submitted and is awaiting verification.',
            'deposit_verified', 'confirmed' => 'Your deposit was verified and your booking is confirmed.',
            'payment_confirmed' => 'Your payment was confirmed and your remaining balance has been updated.',
            'deposit_rejected' => 'Your deposit could not be verified. Please review the correction instructions.',
            'approved' => 'Your catering booking was approved by MixEat.',
            'declined' => 'Your catering booking was declined.',
            'cancelled' => 'Your catering booking was cancelled.',
            'preparing' => 'Your catering order is now being prepared.',
            'out_for_delivery' => 'Your catering order is out for delivery.',
            'delivered', 'completed' => 'Your catering booking has been completed.',
            default => "Your catering booking status is now {$this->booking->status}.",
        };
    }

    private function reference(): string
    {
        return sprintf('MXC-%s-%06d', $this->booking->created_at->format('Y'), $this->booking->id);
    }

    private function url(): string
    {
        return URL::temporarySignedRoute(
            'catering.quotation',
            now()->addDays(14),
            ['booking' => $this->booking->id]
        );
    }

    private function eventKey(): string
    {
        return implode(':', array_filter([
            'catering',
            $this->booking->id,
            $this->event,
            $this->booking->quotation_version ?: null,
            $this->eventKeySuffix,
        ], static fn (?string $value): bool => $value !== null && $value !== ''));
    }
}
