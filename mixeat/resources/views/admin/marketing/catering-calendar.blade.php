@extends('layouts.admin')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
    <div>
        <p class="small fw-bold text-uppercase text-warning-emphasis mb-1">Marketing Admin</p>
        <h1 class="h2 fw-bold mb-1">Catering Event Calendar</h1>
        <p class="text-secondary mb-0">Accept or decline catering bookings, prepare quotes, track payment, and assign fulfillment.</p>
    </div>
    <a class="btn btn-outline-dark rounded-pill" href="{{ route('admin.marketing.catering-bookings') }}">Back to Catering Bookings</a>
</div>

@php($calendar = $bookings->groupBy(fn ($booking) => $booking->event_date->format('Y-m-d')))
@forelse ($calendar as $date => $dayBookings)
    <section class="card-custom p-3 p-md-4 mb-3" aria-label="Events on {{ $date }}">
        <div class="d-flex flex-column flex-sm-row justify-content-between gap-1 mb-3">
            <h2 class="h5 fw-bold mb-0">{{ \Illuminate\Support\Carbon::parse($date)->format('l, F j, Y') }}</h2>
            <span class="badge {{ $dayBookings->count() > 3 ? 'text-bg-danger' : 'text-bg-warning' }} align-self-start">{{ $dayBookings->count() }} event{{ $dayBookings->count() === 1 ? '' : 's' }} scheduled</span>
        </div>
        <div class="row g-3">
        @foreach ($dayBookings as $booking)
            <div class="col-12">
                <article class="rounded-4 border p-3">
                    <div class="row g-3">
                        <div class="col-lg-4">
                            <h3 class="h6 fw-bold mb-1">{{ $booking->client_name }} <span class="badge text-bg-light border">{{ $booking->status }}</span></h3>
                            <div>{{ $booking->service_type }} &middot; {{ number_format($booking->guest_count) }} guests</div>
                            <div class="small text-secondary">{{ $booking->package_preference ?: 'No package preference' }}</div>
                            @if ($booking->venue)
                                <div class="small text-secondary mt-1">{{ $booking->venue }}</div>
                            @endif
                            @if ($booking->special_requests)
                                <p class="small mt-2 mb-1"><strong>Requests:</strong> {{ $booking->special_requests }}</p>
                            @endif
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <a class="btn btn-sm btn-outline-dark" href="mailto:{{ $booking->email }}?subject={{ rawurlencode('MixEat Catering - '.$booking->event_date->format('M j, Y')) }}">Email client</a>
                                <a class="btn btn-sm btn-outline-dark" href="tel:{{ $booking->phone }}">Call {{ $booking->phone }}</a>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <form action="{{ route('admin.marketing.catering-calendar.update', $booking) }}" method="POST" class="row g-2 align-items-end">
                                @csrf
                                @method('PATCH')
                                <div class="col-sm-6 col-lg-4">
                                    <label for="booking-status-{{ $booking->id }}" class="form-label small fw-semibold">Booking decision / status</label>
                                    <select id="booking-status-{{ $booking->id }}" class="form-select form-select-sm" name="status" required>
                                        @foreach ($statuses as $status)<option value="{{ $status }}" @selected($booking->status === $status)>{{ $status }}</option>@endforeach
                                    </select>
                                </div>
                                <div class="col-sm-6 col-lg-4">
                                    <label for="booking-payment-{{ $booking->id }}" class="form-label small fw-semibold">Payment status</label>
                                    <select id="booking-payment-{{ $booking->id }}" class="form-select form-select-sm" name="payment_status" required>
                                        @foreach (['Unpaid', 'Deposit Paid', 'Fully Paid'] as $payment)<option value="{{ $payment }}" @selected($booking->payment_status === $payment)>{{ $payment }}</option>@endforeach
                                    </select>
                                </div>
                                <div class="col-sm-6 col-lg-4">
                                    <label for="booking-quote-{{ $booking->id }}" class="form-label small fw-semibold">Quote (PHP)</label>
                                    <input id="booking-quote-{{ $booking->id }}" class="form-control form-control-sm" type="number" name="quote_amount" min="0" step="0.01" value="{{ $booking->quote_amount }}">
                                </div>
                                <div class="col-sm-6">
                                    <label for="booking-branch-{{ $booking->id }}" class="form-label small fw-semibold">Fulfilling branch</label>
                                    <select id="booking-branch-{{ $booking->id }}" class="form-select form-select-sm" name="branch_id">
                                        <option value="">Unassigned</option>
                                        @foreach ($branches as $branch)<option value="{{ $branch->id }}" @selected($booking->branch_id === $branch->id)>{{ $branch->name }}</option>@endforeach
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label for="booking-staff-{{ $booking->id }}" class="form-label small fw-semibold">Kitchen staff</label>
                                    <select id="booking-staff-{{ $booking->id }}" class="form-select form-select-sm" name="assigned_staff_id">
                                        <option value="">Unassigned</option>
                                        @foreach ($staffByBranch as $branchId => $members)
                                            @if ($branchId)
                                                <optgroup label="{{ $members->first()->branch?->name ?? 'Branch staff' }}">
                                                    @foreach ($members as $member)
                                                        <option value="{{ $member->id }}" @selected($booking->assigned_staff_id === $member->id)>{{ $member->name }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                                    <span class="small text-secondary">
                                        @if ($booking->quote_amount !== null)
                                            Quote preview: <strong class="text-dark">&#8369;{{ number_format((float) $booking->quote_amount, 2) }}</strong>
                                        @else
                                            Quote not prepared
                                        @endif
                                        &middot; {{ $booking->payment_status }}
                                    </span>
                                    <button type="submit" class="btn btn-sm btn-mixeat-yellow">Save booking</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </article>
            </div>
        @endforeach
        </div>
    </section>
@empty
    <div class="card-custom p-5 text-center text-secondary">No catering events are scheduled yet.</div>
@endforelse
@endsection
