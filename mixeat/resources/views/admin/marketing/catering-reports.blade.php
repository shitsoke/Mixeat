@extends('layouts.admin')

@section('content')
@include('admin.marketing.partials.catering-admin-theme')
<div class="catering-admin-theme">
    <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-end gap-3 mb-4">
        <div>
            <p class="small fw-bold text-uppercase text-warning-emphasis mb-1">MixEat Commissary</p>
            <h1 class="h2 fw-bold mb-1">Catering Reports</h1>
            <p class="text-secondary mb-0">Booking activity and verified collections from the Commissary branch.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @foreach (['print' => 'Print Report', 'pdf' => 'PDF', 'xlsx' => 'Excel', 'csv' => 'CSV'] as $format => $label)
                <a class="btn btn-outline-dark rounded-pill" href="{{ route('admin.supervisors.catering-reports.export', [$format] + request()->query()) }}" target="{{ $format === 'print' ? '_blank' : '_self' }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    <form method="GET" class="card-custom p-3 p-md-4 mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-3"><label class="form-label small fw-bold">Date period</label><select name="period" class="form-select"><option value="today" @selected($filters['period'] === 'today')>Today</option><option value="this_week" @selected($filters['period'] === 'this_week')>This Week</option><option value="this_month" @selected($filters['period'] === 'this_month')>This Month</option><option value="last_month" @selected($filters['period'] === 'last_month')>Last Month</option><option value="this_year" @selected($filters['period'] === 'this_year')>This Year</option><option value="custom" @selected($filters['period'] === 'custom')>Custom Date Range</option></select></div>
            <div class="col-md-3"><label class="form-label small fw-bold">Filter dates by</label><select name="date_basis" class="form-select"><option value="event" @selected($filters['date_basis'] === 'event')>Event date</option><option value="created" @selected($filters['date_basis'] === 'created')>Booking creation date</option></select></div>
            <div class="col-md-3"><label class="form-label small fw-bold">From</label><input type="date" name="date_from" value="{{ $filters['date_from'] }}" class="form-control"></div>
            <div class="col-md-3"><label class="form-label small fw-bold">To</label><input type="date" name="date_to" value="{{ $filters['date_to'] }}" class="form-control"></div>
            <div class="col-md-3"><label class="form-label small fw-bold">Catering category</label><select name="category" class="form-select"><option value="">All categories</option>@foreach ($categories as $category)<option value="{{ $category }}" @selected($filters['category'] === $category)>{{ $category }}</option>@endforeach</select></div>
            <div class="col-md-3"><label class="form-label small fw-bold">Booking status</label><select name="status" class="form-select"><option value="">All statuses</option>@foreach ($statuses as $status)<option value="{{ $status }}" @selected($filters['status'] === $status)>{{ $status }}</option>@endforeach</select></div>
            <div class="col-md-3"><label class="form-label small fw-bold">Payment status</label><select name="payment_status" class="form-select"><option value="">All payment statuses</option>@foreach ($paymentStatuses as $status)<option value="{{ $status }}" @selected($filters['payment_status'] === $status)>{{ $status }}</option>@endforeach</select></div>
            <div class="col-md-3"><label class="form-label small fw-bold">Search</label><input name="search" value="{{ $filters['search'] }}" class="form-control" placeholder="Reference, customer, phone"></div>
            <div class="col-12"><button class="btn btn-warning rounded-pill fw-bold" type="submit">Apply Filters</button> <a class="btn btn-outline-dark rounded-pill" href="{{ route('admin.supervisors.catering-reports') }}">Reset</a></div>
        </div>
    </form>

    <div class="row g-3 mb-4">
        @foreach ([['Total Bookings', $summary['total_bookings'], false], ['Pending Bookings', $summary['pending_bookings'], false], ['Confirmed Bookings', $summary['confirmed_bookings'], false], ['Completed Bookings', $summary['completed_bookings'], false], ['Cancelled Bookings', $summary['cancelled_bookings'], false], ['Confirmed Booking Value', $summary['confirmed_value'], true], ['Verified Deposits Collected', $summary['verified_deposits'], true], ['Remaining Balance Outstanding', $summary['outstanding'], true], ['Total Payments Collected', $summary['total_collected'], true]] as [$label, $value, $money])
            <div class="col-6 col-xl-4"><div class="card-custom p-3 h-100"><div class="small text-secondary">{{ $label }}</div><div class="fs-4 fw-bold text-warning mt-1">{{ $money ? '₱'.number_format($value, 2) : number_format($value) }}</div></div></div>
        @endforeach
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6"><div class="card-custom p-3"><h2 class="h5 fw-bold">Bookings by Category</h2><canvas id="category-chart" height="180"></canvas></div></div>
        <div class="col-lg-6"><div class="card-custom p-3"><h2 class="h5 fw-bold">Booking Status Distribution</h2><canvas id="status-chart" height="180"></canvas></div></div>
        <div class="col-lg-6"><div class="card-custom p-3"><h2 class="h5 fw-bold">Monthly Booking Trends</h2><canvas id="booking-chart" height="180"></canvas></div></div>
        <div class="col-lg-6"><div class="card-custom p-3"><h2 class="h5 fw-bold">Monthly Payment Collections</h2><canvas id="payment-chart" height="180"></canvas></div></div>
    </div>

    <section class="card-custom p-3 p-md-4">
        <div class="d-flex justify-content-between align-items-center gap-2 mb-3"><h2 class="h5 fw-bold mb-0">Detailed Catering Report</h2><span class="small text-secondary">{{ $bookings->total() }} matching bookings</span></div>
        <div class="table-responsive">
            <table class="table table-hover align-middle small">
                <thead><tr><th>Reference</th><th>Customer / Contact</th><th>Category</th><th>Booking Date</th><th>Event Date</th><th>Guests</th><th>Total Billing</th><th>Deposit / Collected</th><th>Remaining</th><th>Payment</th><th>Status</th></tr></thead>
                <tbody>
                @forelse ($bookings as $booking)
                    <tr>
                        <td class="text-nowrap fw-bold">{{ $booking->report_reference }}</td>
                        <td>{{ $booking->client_name }}<br><span class="text-secondary">{{ $booking->phone }}</span></td>
                        <td>{{ $booking->service_type }}<br><span class="text-secondary">{{ $booking->package_preference ?: $booking->report_selected_items ?? '' }}</span></td>
                        <td class="text-nowrap">{{ $booking->created_at->timezone('Asia/Manila')->format('M j, Y') }}</td>
                        <td class="text-nowrap">{{ optional($booking->event_date)->format('M j, Y') }}</td>
                        <td>{{ number_format($booking->guest_count) }}</td>
                        <td class="text-nowrap">₱{{ number_format($booking->report_total, 2) }}</td>
                        <td class="text-nowrap">₱{{ number_format($booking->report_verified_deposit, 2) }}<br><span class="text-secondary">₱{{ number_format($booking->report_total_collected, 2) }} total</span></td>
                        <td class="text-nowrap">₱{{ number_format($booking->report_remaining, 2) }}</td>
                        <td>{{ $booking->payment_status }}</td>
                        <td>{{ $booking->status }}</td>
                    </tr>
                @empty
                    <tr><td colspan="11" class="py-5 text-center text-secondary">No bookings match the selected filters.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $bookings->links() }}
    </section>
</div>
@php
    $chartPayload = [
        'categories' => $charts['categories'],
        'statuses' => $charts['statuses'],
        'monthlyBookings' => $charts['monthly_bookings'],
        'monthlyPayments' => $charts['monthly_payments']->map(fn ($row) => [
            'deposits' => (float) $row->deposits,
            'final' => (float) $row->final_payments,
        ]),
    ];
@endphp
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
const chartData = @json($chartPayload);
const colors = ['#FBAB18', '#FFC247', '#B8B8B8', '#777777'];
function doughnut(id, data) { new Chart(document.getElementById(id), { type: 'doughnut', data: { labels: Object.keys(data), datasets: [{ data: Object.values(data), backgroundColor: colors }] }, options: { plugins: { legend: { labels: { color: '#fff' } } } } }); }
function line(id, labels, datasets) { new Chart(document.getElementById(id), { type: 'line', data: { labels, datasets }, options: { scales: { x: { ticks: { color: '#B8B8B8' } }, y: { ticks: { color: '#B8B8B8' }, beginAtZero: true } }, plugins: { legend: { labels: { color: '#fff' } } } } }); }
doughnut('category-chart', chartData.categories); doughnut('status-chart', chartData.statuses);
line('booking-chart', Object.keys(chartData.monthlyBookings), [{ label: 'Bookings', data: Object.values(chartData.monthlyBookings), borderColor: '#FBAB18', backgroundColor: '#FBAB18' }]);
line('payment-chart', Object.keys(chartData.monthlyPayments), [{ label: 'Verified deposits', data: Object.values(chartData.monthlyPayments).map(x => x.deposits), borderColor: '#FBAB18' }, { label: 'Final payments', data: Object.values(chartData.monthlyPayments).map(x => x.final), borderColor: '#FFC247' }]);
</script>
@endsection
