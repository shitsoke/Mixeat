@extends('layouts.admin')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
    <div>
        <p class="small fw-bold text-uppercase text-warning-emphasis mb-1">Marketing Admin</p>
        <h1 class="h2 fw-bold mb-1">Catering Booking Requests</h1>
        <p class="text-secondary mb-0">Review requests, accept or decline bookings, and manage follow-up.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-dark rounded-pill" href="{{ route('admin.marketing.catering-calendar') }}">Event Calendar &amp; Fulfillment</a>
        <a class="btn btn-outline-dark rounded-pill" href="{{ route('admin.marketing.catering-partners') }}">Manage Catering Partners</a>
    </div>
</div>

<section class="card-custom p-3 p-md-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Event</th>
                    <th>Service</th>
                    <th>Guests</th>
                    <th>Submitted</th>
                    <th>Status</th>
                    <th><span class="visually-hidden">Actions</span></th>
                </tr>
            </thead>
            <tbody>
            @forelse ($bookings as $booking)
                <tr>
                    <td>
                        <strong>{{ $booking->client_name }}</strong>
                        @if ($booking->company_name)<br><span class="small text-secondary">{{ $booking->company_name }}</span>@endif
                        <br><a href="mailto:{{ $booking->email }}">{{ $booking->email }}</a>
                    </td>
                    <td class="text-nowrap">
                        {{ $booking->event_date->format('M j, Y') }}
                        @if ($booking->event_time)<br><span class="small text-secondary">{{ \Illuminate\Support\Carbon::parse($booking->event_time)->format('g:i A') }}</span>@endif
                    </td>
                    <td>{{ $booking->service_type }}</td>
                    <td>{{ number_format($booking->guest_count) }}</td>
                    <td class="text-nowrap">{{ $booking->created_at->format('M j, Y g:i A') }}</td>
                    <td><span class="badge {{ $booking->status === 'New' ? 'text-bg-warning' : 'text-bg-light border' }}">{{ $booking->status }}</span></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-dark rounded-pill text-nowrap" data-bs-toggle="modal" data-bs-target="#booking-{{ $booking->id }}">Full details</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="py-5 text-center text-secondary">No catering booking requests have been submitted yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $bookings->links() }}
</section>

@foreach ($bookings as $booking)
    <div class="modal fade" id="booking-{{ $booking->id }}" tabindex="-1" aria-labelledby="booking-title-{{ $booking->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <div>
                        <p class="small fw-bold text-uppercase text-warning-emphasis mb-1">Catering request</p>
                        <h2 class="modal-title h4 fw-bold" id="booking-title-{{ $booking->id }}">{{ $booking->client_name }}</h2>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6"><span class="small text-secondary d-block">Email</span><a href="mailto:{{ $booking->email }}">{{ $booking->email }}</a></div>
                        <div class="col-md-6"><span class="small text-secondary d-block">Contact number</span><a href="tel:{{ $booking->phone }}">{{ $booking->phone }}</a></div>
                        <div class="col-md-6"><span class="small text-secondary d-block">Company / Organization</span>{{ $booking->company_name ?: 'Not provided' }}</div>
                        <div class="col-md-6"><span class="small text-secondary d-block">Event type</span>{{ $booking->event_type ?: 'Not provided' }}</div>
                        <div class="col-md-6"><span class="small text-secondary d-block">Event date &amp; time</span>{{ $booking->event_date->format('F j, Y') }}{{ $booking->event_time ? ' at '.\Illuminate\Support\Carbon::parse($booking->event_time)->format('g:i A') : '' }}</div>
                        <div class="col-md-6"><span class="small text-secondary d-block">Venue / Location</span>{{ $booking->venue ?: 'Not provided' }}</div>
                        <div class="col-md-6"><span class="small text-secondary d-block">Service type</span>{{ $booking->service_type }}</div>
                        <div class="col-md-6"><span class="small text-secondary d-block">Package preference</span>{{ $booking->package_preference ?: 'No preference' }}</div>
                        <div class="col-md-6"><span class="small text-secondary d-block">Estimated guest count</span>{{ number_format($booking->guest_count) }}</div>
                        <div class="col-md-6"><span class="small text-secondary d-block">Budget range</span>{{ $booking->budget_range ?: 'Not provided' }}</div>
                        <div class="col-12"><span class="small text-secondary d-block">Special requests / notes</span><div class="text-break">{{ $booking->special_requests ?: 'None' }}</div></div>
                        <div class="col-12"><span class="small text-secondary d-block">Submitted</span>{{ $booking->created_at->format('F j, Y \a\t g:i A') }}</div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <span class="small text-secondary">Current status: <strong class="text-dark">{{ $booking->status }}</strong></span>
                    <form action="{{ route('admin.marketing.catering-bookings.status', $booking) }}" method="POST" class="d-flex flex-wrap gap-2 align-items-center">
                        @csrf
                        @method('PATCH')
                        <label class="visually-hidden" for="booking-status-{{ $booking->id }}">Booking status</label>
                        <select id="booking-status-{{ $booking->id }}" class="form-select form-select-sm" name="status" required>
                            @foreach ($statuses as $status)<option value="{{ $status }}" @selected($booking->status === $status)>{{ $status }}</option>@endforeach
                        </select>
                        <button type="submit" class="btn btn-mixeat-yellow btn-sm">Save decision</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection
