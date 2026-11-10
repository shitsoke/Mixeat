@extends('layouts.app')

@section('title', 'My Catering Bookings | MixEat')

@section('content')
<section class="mixeat-shell px-4 py-10">
    <h1 class="text-3xl font-heading font-extrabold text-white">My Catering Bookings</h1>
    <div class="mt-6 space-y-4">
        @forelse ($bookings as $booking)
            <article class="rounded-2xl border border-[#333333] bg-[#201D19] p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-bold text-white">MXC-{{ $booking->created_at->format('Y') }}-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</h2>
                        <p class="text-sm text-[#B8B8B8]">{{ $booking->service_type }} · Event {{ $booking->event_date->format('M j, Y') }}</p>
                    </div>
                    <span class="rounded-full bg-[#FBAB18] px-3 py-1 text-xs font-bold text-[#111111]">{{ $booking->status }}</span>
                </div>
                <div class="mt-4 grid gap-3 text-sm text-[#B8B8B8] sm:grid-cols-3">
                    <div>Quotation: <strong class="text-white">₱{{ number_format((float) ($booking->quote_amount ?: 0), 2) }}</strong></div>
                    <div>Payment: <strong class="text-white">{{ $booking->payment_status }}</strong></div>
                    <div>Package: <strong class="text-white">{{ $booking->package_preference ?: 'Custom quotation' }}</strong></div>
                </div>
                @if ($booking->service_type === 'Custom Catering' && in_array($booking->status, ['Quotation Sent', 'Quotation Declined'], true))
                    <a class="mt-4 inline-flex rounded-full bg-[#FBAB18] px-4 py-2 text-sm font-bold text-[#111111]" href="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('catering.quotation', now()->addDays(14), ['booking' => $booking->id]) }}">Review quotation</a>
                @endif
            </article>
        @empty
            <p class="rounded-2xl border border-[#333333] bg-[#201D19] p-6 text-[#B8B8B8]">No catering bookings are linked to your account yet.</p>
        @endforelse
    </div>
    <div class="mt-6">{{ $bookings->links() }}</div>
</section>
@endsection
