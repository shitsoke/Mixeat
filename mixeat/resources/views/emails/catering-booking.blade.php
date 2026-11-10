<!doctype html>
<html lang="en">
<body style="margin:0;background:#111111;color:#ffffff;font-family:Arial,sans-serif;padding:16px;">
    <div style="max-width:620px;margin:0 auto;background:#201D19;padding:28px;border:1px solid #333333;border-radius:12px;">
        <div style="border-bottom:1px solid #333333;padding-bottom:16px;">
            <p style="color:#FBAB18;font-weight:bold;letter-spacing:2px;margin:0 0 8px;">MIXEAT CATERING</p>
            <h1 style="color:#FBAB18;margin:0;">{{ $title }}</h1>
        </div>
        <p>Hello {{ $client_name ?? 'Customer' }},</p>
        <p>{{ $notification_message }}</p>
        <table role="presentation" style="width:100%;border-collapse:collapse;background:#1A1A1A;">
            <tr><td style="padding:10px;font-weight:bold;">Booking reference</td><td style="padding:10px;">{{ $booking_reference }}</td></tr>
            <tr><td style="padding:10px;font-weight:bold;">Customer email</td><td style="padding:10px;">{{ $booking->email ?? '' }}</td></tr>
            <tr><td style="padding:10px;font-weight:bold;">Phone</td><td style="padding:10px;">{{ $phone }}</td></tr>
            @if ($event_date)<tr><td style="padding:10px;font-weight:bold;">Event</td><td style="padding:10px;">{{ $event_date }} @if($event_time) at {{ $event_time }}@endif</td></tr>@endif
            @if ($event_type)<tr><td style="padding:10px;font-weight:bold;">Event type</td><td style="padding:10px;">{{ $event_type }}{{ $event_type_details ? ': '.$event_type_details : '' }}</td></tr>@endif
            @if ($venue)<tr><td style="padding:10px;font-weight:bold;">Venue</td><td style="padding:10px;">{{ $venue }}</td></tr>@endif
            @if ($service_type)<tr><td style="padding:10px;font-weight:bold;">Service</td><td style="padding:10px;">{{ $service_type }}</td></tr>@endif
            @if ($package)<tr><td style="padding:10px;font-weight:bold;">Package</td><td style="padding:10px;">{{ $package }}</td></tr>@endif
            @if ($guest_count)<tr><td style="padding:10px;font-weight:bold;">Guests</td><td style="padding:10px;">{{ $guest_count }}</td></tr>@endif
            <tr><td style="padding:10px;font-weight:bold;">Status</td><td style="padding:10px;">{{ $status }}</td></tr>
            @if ($amount)<tr><td style="padding:10px;font-weight:bold;">Total amount</td><td style="padding:10px;">₱{{ number_format((float) $amount, 2) }}</td></tr>@endif
            @if ($total_paid)<tr><td style="padding:10px;font-weight:bold;">Amount paid</td><td style="padding:10px;">₱{{ number_format((float) $total_paid, 2) }}</td></tr>@endif
            @if ($remaining_balance !== null)<tr><td style="padding:10px;font-weight:bold;">Remaining balance</td><td style="padding:10px;">₱{{ number_format((float) $remaining_balance, 2) }}</td></tr>@endif
        </table>
        @if (!empty($menu_selections))
            <h2 style="font-size:18px;color:#FBAB18;margin-bottom:8px;">Selected foods</h2>
            <ul style="padding-left:20px;">
                @foreach ($menu_selections as $category => $items)
                    @if (is_array($items))
                        @foreach (($items['selections'] ?? $items) as $foodCategory => $foodItems)
                            @if (is_array($foodItems))
                                @foreach ($foodItems as $item)
                                    @if (is_string($item))<li>{{ $foodCategory }}: {{ $item }}</li>@endif
                                @endforeach
                            @elseif (is_string($foodItems))
                                <li>{{ $category }}: {{ $foodItems }}</li>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </ul>
        @endif
        @if ($quotation_notes)<p><strong>Quotation details:</strong> {{ $quotation_notes }}</p>@endif
        @if ($reason)<p><strong>Remarks:</strong> {{ $reason }}</p>@endif
        @if ($recipient_type === 'customer')
            <p><a href="{{ $booking_url }}" style="display:inline-block;background:#FBAB18;color:#111111;padding:12px 18px;text-decoration:none;font-weight:bold;border-radius:20px;">View booking</a></p>
        @endif
        <p style="color:#B8B8B8;font-size:12px;">MixEat Commissary</p>
    </div>
</body>
</html>
