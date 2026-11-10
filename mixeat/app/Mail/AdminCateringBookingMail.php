<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AdminCateringBookingMail extends CateringBookingMailBase
{
    public function envelope(): Envelope
    {
        $reference = $this->data()['booking_reference'];
        $subject = $this->event === 'inquiry_submitted'
            ? "New Catering Booking - {$reference}"
            : 'Catering Booking '.ucwords(str_replace('_', ' ', $this->event))." - {$reference}";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.catering-booking-admin', with: $this->data());
    }
}
