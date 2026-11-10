@extends('layouts.admin')

@section('content')
@include('admin.marketing.partials.catering-admin-theme')
<div class="catering-admin-theme">
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
    <div>
        <p class="small fw-bold text-uppercase text-warning-emphasis mb-1">Branch Supervisor</p>
        <h1 class="h2 fw-bold mb-1">Catering Booking Requests</h1>
        <p class="text-secondary mb-0">Review requests, accept or decline bookings, and manage follow-up.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
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
                    <th>Booking Status</th>
                    <th>Payment Status</th>
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
                        @php
                            $paymentBadge = match ($booking->payment_summary['status']) {
                                'Pending Verification' => 'text-bg-warning',
                                'Deposit Paid (30%)' => 'text-bg-warning',
                                'Fully Paid' => 'text-bg-success',
                                'Payment Rejected' => 'text-bg-danger',
                                default => 'text-bg-secondary',
                            };
                        @endphp
                        <span class="badge {{ $paymentBadge }}">{{ $booking->payment_summary['status'] }}</span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-dark rounded-pill text-nowrap" data-bs-toggle="modal" data-bs-target="#booking-{{ $booking->id }}">Full details</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="py-5 text-center text-secondary">No catering booking requests have been submitted yet.</td></tr>
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
                        <div class="col-md-6"><span class="small text-secondary d-block">Event type</span>{{ $booking->event_type ?: 'Not provided' }}{{ $booking->event_type_details ? ': '.$booking->event_type_details : '' }}</div>
                        <div class="col-md-6"><span class="small text-secondary d-block">Event date &amp; time</span>{{ $booking->event_date->format('F j, Y') }}{{ $booking->event_time ? ' at '.\Illuminate\Support\Carbon::parse($booking->event_time)->format('g:i A') : '' }}</div>
                        <div class="col-md-6"><span class="small text-secondary d-block">Venue / Location</span>{{ $booking->venue ?: 'Not provided' }}</div>
                        <div class="col-md-6"><span class="small text-secondary d-block">Service type</span>{{ $booking->service_type }}</div>
                        <div class="col-md-6"><span class="small text-secondary d-block">Package preference</span>{{ $booking->package_preference ?: 'No preference' }}</div>
                        <div class="col-md-6"><span class="small text-secondary d-block">{{ $booking->service_type === 'Packed Meals' ? 'Number of meals' : 'Estimated guest count' }}</span>{{ number_format($booking->guest_count) }}</div>
                        @if ($booking->package_price_per_pax)
                            <div class="col-md-6"><span class="small text-secondary d-block">{{ $booking->service_type === 'Packed Meals' ? 'Price per meal' : 'Package price per pax' }}</span>₱{{ number_format((float) $booking->package_price_per_pax, 2) }}</div>
                        @endif
                        @if ($booking->estimated_total)
                            <div class="col-md-6"><span class="small text-secondary d-block">{{ $booking->service_type === 'Packed Meals' ? 'Estimated total' : ($booking->service_type === 'Party Trays' ? 'Estimated tray total' : 'Estimated package total') }}</span>₱{{ number_format((float) $booking->estimated_total, 2) }} <span class="small text-secondary">(estimate only)</span></div>
                        @endif
                        <div class="col-md-6"><span class="small text-secondary d-block">Budget range</span>{{ $booking->budget_range ?: 'Not provided' }}</div>
                        @if ($booking->menu_selections)
                            <div class="col-12">
                                <span class="small text-secondary d-block">Selected menu</span>
                                <dl class="mb-0 mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
                                    @foreach (['package' => 'Packed meal package', 'main_dish' => 'Main dish', 'side' => 'Side', 'meat' => 'Meat dishes', 'soup' => 'Soup', 'vegetables' => 'Vegetable', 'pasta' => 'Pasta/Noodles', 'dessert' => 'Dessert', 'rice' => 'Rice', 'drinks' => 'Drink'] as $key => $label)
                                        @if (!empty($booking->menu_selections[$key]))
                                            <div>
                                                <dt class="font-semibold">{{ $label }}</dt>
                                                <dd class="mb-0 text-break">{{ is_array($booking->menu_selections[$key]) ? implode(', ', $booking->menu_selections[$key]) : $booking->menu_selections[$key] }}</dd>
                                            </div>
                                        @endif
                                    @endforeach
                                    @if ($booking->service_type === 'Custom Catering')
                                        <div class="col-12 rounded-3 border p-3">
                                            <span class="small text-secondary d-block">Custom package selections</span>
                                            <div class="row g-2 mt-1">
                                                @foreach (($booking->menu_selections['selections'] ?? []) as $category => $dishes)
                                                    @if (!empty($dishes))
                                                        <div class="col-md-6">
                                                            <strong class="text-capitalize">{{ $category }} ({{ count($dishes) }} selected)</strong>: {{ implode(', ', $dishes) }}
                                                            @if (!empty($booking->menu_selections['details'][$category]))
                                                                <ul class="small text-secondary mb-0 mt-1">
                                                                    @foreach ($booking->menu_selections['details'][$category] as $dish)
                                                                        <li>{{ $dish['name'] }}{{ !empty($dish['description']) ? ' — '.$dish['description'] : '' }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </div>
                                                    @endif
                                                @endforeach
                                                @if (empty($booking->menu_selections['selections']) && !empty($booking->menu_selections['counts']))
                                                    @foreach ($booking->menu_selections['counts'] as $category => $count)
                                                        @if ((int) $count > 0)
                                                            <div class="col-md-6"><strong class="text-capitalize">{{ $category }} ({{ $count }})</strong>: {{ implode(', ', $booking->menu_selections['selections'][$category] ?? []) }}</div>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                            </div>
                                            @if (in_array($booking->status, ['Pending Quotation', 'Quotation Sent', 'Quotation Declined'], true))
                                                <form action="{{ route('admin.supervisors.catering-bookings.quotation', $booking) }}" method="POST" class="row g-2 align-items-end mt-3">
                                                    @csrf
                                                    <div class="col-md-4"><label class="form-label small">Manual quotation amount</label><input type="number" name="quote_amount" min="0.01" step="0.01" required value="{{ $booking->quote_amount }}" class="form-control"></div>
                                                    <div class="col-md-5"><label class="form-label small">Notes and inclusions</label><textarea name="quotation_notes" rows="2" class="form-control">{{ $booking->quotation_notes }}</textarea></div>
                                                    <div class="col-md-3"><button class="btn btn-mixeat-yellow w-100" type="submit">{{ $booking->quotation_version ? 'Revise quotation' : 'Send quotation' }}</button></div>
                                                </form>
                                                <p class="small text-secondary mt-2">Quotation status: {{ $booking->quotation_status ?: 'Pending' }}</p>
                                                @if ($booking->quotation_status === 'Sent')
                                                    @php
                                                        $quotationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute('catering.quotation', now()->addDays(14), ['booking' => $booking->id]);
                                                    @endphp
                                                    <label class="form-label small mt-2">Secure quotation link (expires in 14 days)</label>
                                                    <input type="url" readonly value="{{ $quotationUrl }}" class="form-control form-control-sm">
                                                @endif
                                            @endif
                                        </div>
                                    @endif
                                </dl>
                            </div>
                        @endif
                        @if ($booking->trayItems->isNotEmpty())
                            <div class="col-12">
                                <span class="small text-secondary d-block">Food Trays &amp; Buffet Setups</span>
                                <div class="table-responsive mt-2">
                                    <table class="table table-sm table-bordered align-middle mb-0">
                                        <thead><tr><th>Category</th><th>Dish</th><th>Size</th><th>Quantity</th><th>Unit price</th><th>Subtotal</th></tr></thead>
                                        <tbody>
                                            @foreach ($booking->trayItems as $trayItem)
                                                <tr>
                                                    <td>{{ $trayItem->category }}</td>
                                                    <td>{{ $trayItem->dish }}</td>
                                                    <td>{{ ucfirst($trayItem->tray_size) }}</td>
                                                    <td>{{ number_format($trayItem->quantity) }}</td>
                                                    <td>₱{{ number_format((float) $trayItem->unit_price, 2) }}</td>
                                                    <td>₱{{ number_format((float) $trayItem->subtotal, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                        @php
                            $paymentSummary = $booking->payment_summary;
                        @endphp
                        <div class="col-12">
                            <span class="small text-secondary d-block">Payment Summary</span>
                            @if (! $paymentSummary['payment_required'])
                                <div class="mt-2 rounded-3 border p-3 text-warning">Awaiting Quotation Acceptance &ndash; Payment Not Yet Required</div>
                            @else
                                <div class="row g-2 mt-1 small">
                                    <div class="col-md-4"><strong>Total booking amount:</strong> ₱{{ number_format($paymentSummary['total_cents'] / 100, 2) }}</div>
                                    <div class="col-md-4"><strong>Required deposit (30%):</strong> ₱{{ number_format($paymentSummary['required_deposit_cents'] / 100, 2) }}</div>
                                    <div class="col-md-4"><strong>Verified deposit:</strong> ₱{{ number_format($paymentSummary['verified_deposit_cents'] / 100, 2) }}</div>
                                    <div class="col-md-4"><strong>Total collected:</strong> ₱{{ number_format($paymentSummary['collected_cents'] / 100, 2) }}</div>
                                    <div class="col-md-4"><strong>Remaining balance:</strong> ₱{{ number_format($paymentSummary['remaining_cents'] / 100, 2) }}</div>
                                    @if ($paymentSummary['latest_payment'])
                                        <div class="col-md-4"><strong>Payment method:</strong> {{ $paymentSummary['latest_payment']->payment_method }}</div>
                                        <div class="col-md-4"><strong>Reference:</strong> {{ $paymentSummary['latest_payment']->payment_reference ?: 'Not provided' }}</div>
                                        <div class="col-md-4"><strong>Verification:</strong> {{ $paymentSummary['latest_payment']->status }}</div>
                                        <div class="col-md-4"><strong>Verified date:</strong> {{ $paymentSummary['latest_payment']->verified_at?->format('M j, Y g:i A') ?: 'Not verified' }}</div>
                                        <div class="col-md-4"><strong>Verified by:</strong> {{ $paymentSummary['latest_payment']->verifiedBy?->name ?: 'Not verified' }}</div>
                                    @endif
                                </div>
                            @endif
                        </div>
                        @if ($booking->payments->isNotEmpty())
                            @php
                                $verifiedDeposit = $booking->payments->first(fn ($payment) => $payment->payment_type === 'deposit' && $payment->status === 'Verified');
                                $paidCents = $paymentSummary['collected_cents'];
                                $totalCents = $paymentSummary['total_cents'];
                                $remainingCents = $paymentSummary['remaining_cents'];
                                $latestDeposit = $booking->payments->where('payment_type', 'deposit')->sortByDesc('id')->first();
                            @endphp
                            <div class="col-12">
                                <span class="small text-secondary d-block">Security Bank payment review · {{ $booking->payment_status }}</span>
                                <div class="mt-2 space-y-3">
                                    @foreach ($booking->payments->sortBy('id') as $payment)
                                        <div class="rounded-3 border p-3">
                                            <div class="d-flex flex-wrap justify-content-between gap-2">
                                                <strong>{{ ucfirst($payment->payment_type) }} · {{ $payment->payment_method }}</strong>
                                                <span>{{ $payment->status }} · ₱{{ number_format((float) ($payment->verified_amount ?? $payment->amount), 2) }}</span>
                                            </div>
                                            @if ($payment->payment_reference)
                                                <div class="mt-1 small">Reference: <code>{{ $payment->payment_reference }}</code></div>
                                            @endif
                                            @if ($payment->duplicate_reference)
                                                <div class="mt-1 small fw-bold text-danger">Duplicate reference: review this submission carefully before verifying.</div>
                                            @endif
                                            @if ($payment->verification_remarks)
                                                <div class="mt-1 small text-danger">Review notes: {{ $payment->verification_remarks }}</div>
                                            @endif
                                            @if ($payment->verified_at)
                                                <div class="mt-1 small text-secondary">{{ $payment->status }} by {{ $payment->verifiedBy?->name ?? 'Former staff member' }} · {{ $payment->verified_at->format('M j, Y g:i A') }}</div>
                                            @endif
                                            @if ($payment->collected_at)
                                                <div class="mt-1 small text-secondary">Collected by {{ $payment->collectedBy?->name ?? 'Former staff member' }} · {{ $payment->collected_at->format('M j, Y g:i A') }}</div>
                                            @endif
                                            @if ($payment->payment_type === 'deposit' && $payment->status === 'Pending Verification')
                                                <div class="d-flex flex-wrap gap-2 mt-3">
                                                    <form action="{{ route('admin.supervisors.catering-bookings.payments.verify', [$booking, $payment]) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success">Verify deposit &amp; confirm</button>
                                                    </form>
                                                    <form action="{{ route('admin.supervisors.catering-bookings.payments.reject', [$booking, $payment]) }}" method="POST" class="d-flex flex-grow-1 gap-2">
                                                        @csrf
                                                        <input type="text" name="verification_remarks" maxlength="1000" required class="form-control form-control-sm" placeholder="Reason for rejection">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Reject</button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                @if ($latestDeposit?->status === 'Rejected')
                                    @php($correctionUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute('catering.payments.correction', now()->addDays(7), ['booking' => $booking->id]))
                                    <div class="mt-3">
                                        <label for="correction-link-{{ $booking->id }}" class="form-label small fw-semibold">Secure correction link (expires in 7 days)</label>
                                        <input id="correction-link-{{ $booking->id }}" type="url" readonly value="{{ $correctionUrl }}" class="form-control form-control-sm">
                                        <a href="{{ $correctionUrl }}" target="_blank" rel="noopener" class="small">Open link to verify it before sharing</a>
                                    </div>
                                @endif
                                @if ($verifiedDeposit && $remainingCents > 0)
                                    <div class="mt-3 rounded-3 border p-3">
                                        <p class="mb-2"><strong>Remaining balance:</strong> ₱{{ number_format($remainingCents / 100, 2) }} of ₱{{ number_format($totalCents / 100, 2) }}</p>
                                        <form action="{{ route('admin.supervisors.catering-bookings.payments.balance', $booking) }}" method="POST" class="row g-2 align-items-end">
                                            @csrf
                                            <div class="col-sm-5">
                                                <label for="balance-amount-{{ $booking->id }}" class="form-label small">Amount collected</label>
                                                <input id="balance-amount-{{ $booking->id }}" type="number" name="amount" min="0.01" max="{{ number_format($remainingCents / 100, 2, '.', '') }}" step="0.01" required class="form-control form-control-sm">
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="balance-method-{{ $booking->id }}" class="form-label small">Collection method</label>
                                                <select id="balance-method-{{ $booking->id }}" name="payment_method" required class="form-select form-select-sm">
                                                    @foreach (['Cash', 'Bank Transfer', 'Security Bank QR'] as $method)<option value="{{ $method }}">{{ $method }}</option>@endforeach
                                                </select>
                                            </div>
                                            <div class="col-sm-3"><button type="submit" class="btn btn-sm btn-mixeat-yellow w-100">Record payment</button></div>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endif
                        <div class="col-12"><span class="small text-secondary d-block">Special requests / notes</span><div class="text-break">{{ $booking->special_requests ?: 'None' }}</div></div>
                        <div class="col-12"><span class="small text-secondary d-block">Submitted</span>{{ $booking->created_at->format('F j, Y \a\t g:i A') }}</div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <span class="small text-secondary">Current status: <strong class="text-dark">{{ $booking->status }}</strong></span>
                    @if ($booking->payments->isEmpty())
                    <form action="{{ route('admin.supervisors.catering-bookings.status', $booking) }}" method="POST" class="d-flex flex-wrap gap-2 align-items-center">
                        @csrf
                        @method('PATCH')
                        <label class="visually-hidden" for="booking-status-{{ $booking->id }}">Booking status</label>
                        <select id="booking-status-{{ $booking->id }}" class="form-select form-select-sm" name="status" required>
                            @foreach ($statuses as $status)<option value="{{ $status }}" @selected($booking->status === $status)>{{ $status }}</option>@endforeach
                        </select>
                        <button type="submit" class="btn btn-mixeat-yellow btn-sm">Save decision</button>
                    </form>
                    @else
                        <span class="small text-secondary">Booking confirmation is controlled by payment verification; fulfillment status is managed in the event calendar.</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach
</div>
@endsection
