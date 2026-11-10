<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class CustomerCateringBookingMail extends CateringBookingMailBase
{
    public function envelope(): Envelope
    {
        $subject = $this->event === 'inquiry_submitted'
            ? 'MixEat Catering - Booking Confirmation'
            : $this->notification()->toArray((object) [])['title'];

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        $data = $this->data();
        $data['notification_message'] = $data['message'];

        return new Content(view: 'emails.catering-booking-customer', with: $data);
    }
}
