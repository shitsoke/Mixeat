@extends('layouts.admin')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
    <div>
        <p class="small fw-bold text-uppercase text-warning-emphasis mb-1">Marketing Admin</p>
        <h1 class="h2 fw-bold mb-1">Catering Leads & Content</h1>
        <p class="text-secondary mb-0">Review inquiries, manage landing page content, and track campaign performance.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-dark rounded-pill" href="{{ route('admin.catering.content') }}">Packages &amp; Promotions</a>
        <a class="btn btn-outline-dark rounded-pill" href="{{ route('admin.marketing.catering-partners') }}">Manage Clients &amp; Partners</a>
        <a class="btn btn-outline-dark rounded-pill" href="{{ route('home') }}#catering" target="_blank" rel="noopener">View Catering Landing Page</a>
    </div>
</div>

<section class="row g-3 mb-4" aria-label="Catering inquiry analytics">
    <div class="col-sm-6 col-lg-3"><div class="card-custom p-3 h-100"><div class="small text-secondary">Total inquiries</div><div class="fs-3 fw-bold">{{ $bookings->total() }}</div></div></div>
    <div class="col-sm-6 col-lg-3"><div class="card-custom p-3 h-100"><div class="small text-secondary">Inquiry conversion</div><div class="fs-3 fw-bold">{{ number_format($conversionRate, 1) }}%</div><div class="small text-secondary">Approved or fulfilled</div></div></div>
    <div class="col-sm-6 col-lg-3"><div class="card-custom p-3 h-100"><div class="small text-secondary">Popular packages</div>
        @forelse ($popularPackages->take(2) as $package => $count)<div class="fw-semibold">{{ $package }} <span class="badge text-bg-warning">{{ $count }}</span></div>@empty<p class="mb-0 text-secondary">No package preferences yet.</p>@endforelse
    </div></div>
    <div class="col-sm-6 col-lg-3"><div class="card-custom p-3 h-100"><div class="small text-secondary">Recent inquiry trends</div>
        @forelse ($inquiryTrends as $month => $count)<div class="d-flex justify-content-between"><span>{{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $month)->format('M Y') }}</span><strong>{{ $count }}</strong></div>@empty<p class="mb-0 text-secondary">No inquiries yet.</p>@endforelse
    </div></div>
</section>

<section class="card-custom p-4 mb-4">
    <h2 class="h4 fw-bold mb-3">Campaign Conversion</h2>
    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead><tr><th>Campaign</th><th>Inquiries</th><th>Approved / fulfilled</th><th>Conversion</th></tr></thead>
            <tbody>
            @forelse ($campaignStats as $campaign => $stats)
                <tr><td>{{ $campaign }}</td><td>{{ $stats['inquiries'] }}</td><td>{{ $stats['converted'] }}</td><td>{{ $stats['inquiries'] ? number_format($stats['converted'] / $stats['inquiries'] * 100, 1) : '0.0' }}%</td></tr>
            @empty
                <tr><td colspan="4" class="text-secondary">Campaign conversion data will appear when inquiries arrive.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="card-custom p-4">
    <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
        <div><h2 class="h4 fw-bold mb-1">Catering Inquiries</h2><p class="text-secondary mb-0">New requests are added with New status for supervisor follow-up.</p></div>
        <span class="badge text-bg-warning align-self-start">{{ $bookings->total() }} total</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th>Event</th><th>Client</th><th>Service / Package</th><th>Guests</th><th>Special requests</th><th>Campaign</th><th>Status</th></tr></thead>
            <tbody>
            @forelse ($bookings as $booking)
                <tr>
                    <td class="text-nowrap">{{ $booking->event_date->format('M j, Y') }}</td>
                    <td><strong>{{ $booking->client_name }}</strong><br><a href="mailto:{{ $booking->email }}">{{ $booking->email }}</a><br><a href="tel:{{ $booking->phone }}">{{ $booking->phone }}</a></td>
                    <td>{{ $booking->service_type }}<br><span class="small text-secondary">{{ $booking->package_preference ?: 'No package preference' }}</span></td>
                    <td>{{ number_format($booking->guest_count) }}</td>
                    <td>{{ $booking->special_requests ?: '—' }}</td>
                    <td>{{ $booking->campaign_source }}</td>
                    <td><span class="badge text-bg-light border">{{ $booking->status }}</span></td>
                </tr>
            @empty
                <tr><td colspan="7" class="py-4 text-center text-secondary">No catering inquiries yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $bookings->links() }}
</section>
@endsection
