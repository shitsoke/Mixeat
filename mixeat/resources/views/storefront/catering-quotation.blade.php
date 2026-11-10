@extends('layouts.app')

@section('title', 'MixEat Catering Quotation')

@section('content')
<section class="bg-[#1A1A1A] px-4 py-12 text-white md:px-8">
    <div class="mx-auto max-w-4xl">
        <p class="text-xs font-bold uppercase tracking-widest text-[#FBAB18]">MixEat Commissary</p>
        <h1 class="mt-2 text-3xl font-bold">Your Customized Catering Quotation</h1>
        <p class="mt-2 text-[#B8B8B8]">Booking reference: MXC-{{ $booking->created_at->format('Y') }}-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</p>
        @if (session('catering_status'))
            <div class="mt-6 rounded-xl border border-green-700 bg-green-950/40 p-4 text-green-200">{{ session('catering_status') }}</div>
        @endif
        <div class="mt-8 grid gap-6 md:grid-cols-2">
            <div class="rounded-2xl border border-[#333333] bg-[#201D19] p-6">
                <h2 class="text-xl font-bold">Event Details</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div><dt class="text-[#B8B8B8]">Customer</dt><dd>{{ $booking->client_name }}</dd></div>
                    <div><dt class="text-[#B8B8B8]">Event</dt><dd>{{ $booking->event_date?->format('F j, Y') }} · {{ $booking->venue }}</dd></div>
                    <div><dt class="text-[#B8B8B8]">Guests</dt><dd>{{ number_format($booking->guest_count) }}</dd></div>
                    <div><dt class="text-[#B8B8B8]">Status</dt><dd>{{ $booking->status }}</dd></div>
                </dl>
            </div>
            <div class="rounded-2xl border border-[#333333] bg-[#201D19] p-6">
                <h2 class="text-xl font-bold">Quotation</h2>
                <p class="mt-4 text-3xl font-extrabold text-[#FBAB18]">₱{{ number_format((float) $quotation->amount, 2) }}</p>
                <p class="mt-3 whitespace-pre-line text-sm text-[#B8B8B8]">{{ $quotation->notes ?: 'No additional notes.' }}</p>
                <p class="mt-4 text-xs text-[#B8B8B8]">After acceptance, the required 30% deposit will be ₱{{ number_format((float) $quotation->amount * .30, 2) }}.</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    @if ($booking->status === 'Quotation Sent')
                        <form method="POST" action="{{ URL::signedRoute('catering.quotation.accept', ['booking' => $booking->id]) }}">@csrf<button class="rounded-full bg-[#FBAB18] px-5 py-3 font-bold text-[#111111]" type="submit">Accept Quotation</button></form>
                        <form method="POST" action="{{ URL::signedRoute('catering.quotation.decline', ['booking' => $booking->id]) }}">@csrf<button class="rounded-full border border-[#555555] px-5 py-3 font-bold text-white" type="submit">Decline Quotation</button></form>
                    @endif
                </div>
            </div>
        </div>
        @if ($booking->status === 'Awaiting Deposit')
            <div class="mt-6 rounded-2xl border border-[#333333] bg-[#201D19] p-6">
                <h2 class="text-xl font-bold">Submit your 30% deposit</h2>
                <p class="mt-2 text-sm text-[#B8B8B8]">Required deposit: <strong class="text-[#FBAB18]">₱{{ number_format((float) $quotation->amount * .30, 2) }}</strong>. Scan the Security Bank QR code, then enter your payment reference.</p>
                <img src="{{ asset('images/mixeat qr.jpeg') }}" alt="MixEat Security Bank QR code" class="mt-4 max-h-56 rounded-lg bg-white p-2">
                <form method="POST" action="{{ URL::signedRoute('catering.quotation.deposit', ['booking' => $booking->id]) }}" class="mt-5 grid gap-4 sm:grid-cols-2">
                    @csrf
                    <input name="payment_reference" required maxlength="64" pattern="[A-Za-z0-9]+" placeholder="Security Bank payment reference" class="rounded-lg border border-[#555555] bg-[#1A1A1A] px-4 py-3 text-white">
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="payment_confirmation" value="1" required class="accent-[#FBAB18]"> I confirm I paid this deposit.</label>
                    <button class="rounded-full bg-[#FBAB18] px-5 py-3 font-bold text-[#111111] sm:col-span-2" type="submit">Submit Deposit for Verification</button>
                </form>
            </div>
        @endif
        <div class="mt-6 rounded-2xl border border-[#333333] bg-[#201D19] p-6">
            <h2 class="text-xl font-bold">Selected Dishes</h2>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                @foreach (($booking->menu_selections['selections'] ?? []) as $category => $dishes)
                    @if (is_array($dishes) && count($dishes))
                        <div><p class="font-bold capitalize">{{ $category }}</p><p class="text-sm text-[#B8B8B8]">{{ implode(', ', $dishes) }}</p></div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection
