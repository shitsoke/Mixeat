<?php

namespace App\Services;

use App\Models\CateringBooking;
use App\Models\CateringPayment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CateringReportService
{
    private const CONFIRMED_STATUSES = [
        'Accepted',
        'Confirmed',
        'Approved',
        'In Preparation',
        'Preparing',
        'Dispatched',
        'Out for Delivery',
        'Completed',
        'Delivered',
    ];

    public function filters(Request $request): array
    {
        $data = $request->validate([
            'period' => ['nullable', 'in:today,this_week,this_month,last_month,this_year,custom'],
            'date_basis' => ['nullable', 'in:event,created'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'category' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'string', 'max:40'],
            'payment_status' => ['nullable', 'string', 'max:40'],
            'search' => ['nullable', 'string', 'max:120'],
        ]);

        $timezone = 'Asia/Manila';
        $now = Carbon::now($timezone);
        $period = $data['period'] ?? 'this_month';
        $from = null;
        $to = null;

        if ($period === 'today') {
            $from = $to = $now->toDateString();
        } elseif ($period === 'this_week') {
            $from = $now->copy()->startOfWeek()->toDateString();
            $to = $now->copy()->endOfWeek()->toDateString();
        } elseif ($period === 'this_month') {
            $from = $now->copy()->startOfMonth()->toDateString();
            $to = $now->copy()->endOfMonth()->toDateString();
        } elseif ($period === 'last_month') {
            $from = $now->copy()->subMonthNoOverflow()->startOfMonth()->toDateString();
            $to = $now->copy()->subMonthNoOverflow()->endOfMonth()->toDateString();
        } elseif ($period === 'this_year') {
            $from = $now->copy()->startOfYear()->toDateString();
            $to = $now->copy()->endOfYear()->toDateString();
        } elseif ($period === 'custom') {
            $from = $data['date_from'] ?? null;
            $to = $data['date_to'] ?? null;
        }

        return [
            'period' => $period,
            'date_basis' => $data['date_basis'] ?? 'event',
            'date_from' => $from,
            'date_to' => $to,
            'category' => $data['category'] ?? null,
            'status' => $data['status'] ?? null,
            'payment_status' => $data['payment_status'] ?? null,
            'search' => trim($data['search'] ?? ''),
        ];
    }

    public function query(array $filters, int $branchId): Builder
    {
        $query = CateringBooking::query()
            ->where('branch_id', $branchId)
            ->with(['trayItems', 'payments']);

        $dateColumn = $filters['date_basis'] === 'created' ? 'created_at' : 'event_date';
        if ($filters['date_from']) {
            $query->whereDate($dateColumn, '>=', $filters['date_from']);
        }
        if ($filters['date_to']) {
            $query->whereDate($dateColumn, '<=', $filters['date_to']);
        }
        if ($filters['category']) {
            $query->where('service_type', $filters['category']);
        }
        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }
        if ($filters['payment_status']) {
            $query->where('payment_status', $filters['payment_status']);
        }
        if ($filters['search'] !== '') {
            $search = $filters['search'];
            $query->where(function (Builder $searchQuery) use ($search): void {
                $searchQuery->where('client_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
                if (ctype_digit($search)) {
                    $searchQuery->orWhereKey((int) $search);
                }
            });
        }

        return $query;
    }

    public function summary(Builder $bookings): array
    {
        $totalBookings = (clone $bookings)->count();
        $pendingBookings = (clone $bookings)->whereIn('status', ['New', 'Pending Review', 'In Review', 'Awaiting Deposit Verification', 'Pending Quotation', 'Quotation Sent', 'Quotation Declined', 'Awaiting Deposit'])->count();
        $confirmedBookings = (clone $bookings)->whereIn('status', self::CONFIRMED_STATUSES)->count();
        $completedBookings = (clone $bookings)->whereIn('status', ['Completed', 'Delivered'])->count();
        $cancelledBookings = (clone $bookings)->where('status', 'Cancelled')->count();
        $confirmedValue = (float) (clone $bookings)
            ->whereIn('status', self::CONFIRMED_STATUSES)
            ->selectRaw('COALESCE(SUM(COALESCE(quote_amount, estimated_total, 0)), 0) as total')
            ->value('total');

        $paymentTotals = $this->paymentTotals($bookings);
        $outstanding = max(0, $confirmedValue - $paymentTotals['total_collected']);

        return [
            'total_bookings' => $totalBookings,
            'pending_bookings' => $pendingBookings,
            'confirmed_bookings' => $confirmedBookings,
            'completed_bookings' => $completedBookings,
            'cancelled_bookings' => $cancelledBookings,
            'confirmed_value' => $confirmedValue,
            'required_deposit' => round($confirmedValue * 0.30, 2),
            'verified_deposits' => $paymentTotals['verified_deposits'],
            'final_payments' => $paymentTotals['final_payments'],
            'total_collected' => $paymentTotals['total_collected'],
            'outstanding' => $outstanding,
        ];
    }

    public function paymentTotals(Builder $bookings): array
    {
        $bookingIds = (clone $bookings)->select('catering_bookings.id');
        $deposits = CateringPayment::query()
            ->whereIn('catering_booking_id', $bookingIds)
            ->where('payment_type', 'deposit')
            ->where('status', 'Verified')
            ->sum(DB::raw('COALESCE(verified_amount, amount)'));
        $finalPayments = CateringPayment::query()
            ->whereIn('catering_booking_id', $bookingIds)
            ->where('payment_type', 'balance')
            ->where('status', 'Collected')
            ->sum('amount');

        return [
            'verified_deposits' => (float) $deposits,
            'final_payments' => (float) $finalPayments,
            'total_collected' => (float) $deposits + (float) $finalPayments,
        ];
    }

    public function charts(Builder $bookings, array $filters): array
    {
        $categoryRows = (clone $bookings)
            ->select('service_type', DB::raw('COUNT(*) as total'))
            ->groupBy('service_type')
            ->pluck('total', 'service_type');
        $statusRows = (clone $bookings)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $periodExpression = $filters['date_basis'] === 'created'
            ? $this->monthExpression('created_at')
            : $this->monthExpression('event_date');
        $monthlyBookings = (clone $bookings)
            ->selectRaw("{$periodExpression} as period, COUNT(*) as total")
            ->groupByRaw($periodExpression)
            ->orderBy('period')
            ->pluck('total', 'period');

        $paymentBookingIds = (clone $bookings)->select('catering_bookings.id');
        $paymentDateExpression = $this->monthExpression('catering_payments.created_at');
        $monthlyPayments = CateringPayment::query()
            ->whereIn('catering_booking_id', $paymentBookingIds)
            ->where(function (Builder $query): void {
                $query->where(function (Builder $deposit): void {
                    $deposit->where('payment_type', 'deposit')->where('status', 'Verified');
                })->orWhere(function (Builder $balance): void {
                    $balance->where('payment_type', 'balance')->where('status', 'Collected');
                });
            })
            ->selectRaw("{$paymentDateExpression} as period")
            ->selectRaw("SUM(CASE WHEN payment_type = 'deposit' THEN COALESCE(verified_amount, amount) ELSE 0 END) as deposits")
            ->selectRaw("SUM(CASE WHEN payment_type = 'balance' THEN amount ELSE 0 END) as final_payments")
            ->groupByRaw($paymentDateExpression)
            ->orderBy('period')
            ->get()
            ->keyBy('period');

        return [
            'categories' => collect([
                'Packed Meals' => (int) ($categoryRows['Packed Meals'] ?? 0),
                'Party Trays' => (int) ($categoryRows['Party Trays'] ?? 0),
                'Catering Packages' => (int) (($categoryRows['Full-Service Catering'] ?? 0) + ($categoryRows['Custom Catering'] ?? 0)),
            ]),
            'statuses' => collect([
                'Pending' => (int) $statusRows->only(['New', 'Pending Review', 'In Review', 'Awaiting Deposit Verification', 'Pending Quotation', 'Quotation Sent', 'Quotation Declined', 'Awaiting Deposit'])->sum(),
                'Confirmed' => (int) $statusRows->only(self::CONFIRMED_STATUSES)->sum(),
                'Completed' => (int) $statusRows->only(['Completed', 'Delivered'])->sum(),
                'Cancelled' => (int) ($statusRows['Cancelled'] ?? 0),
            ]),
            'monthly_bookings' => $monthlyBookings,
            'monthly_payments' => $monthlyPayments,
        ];
    }

    public function decorate(Collection $bookings): Collection
    {
        return $bookings->each(function (CateringBooking $booking): void {
            $booking->report_total = (float) ($booking->quote_amount ?? $booking->estimated_total ?? 0);
            $booking->report_required_deposit = round($booking->report_total * 0.30, 2);
            $booking->report_verified_deposit = (float) $booking->payments
                ->where('payment_type', 'deposit')
                ->where('status', 'Verified')
                ->sum(fn ($payment) => $payment->verified_amount ?? $payment->amount);
            $booking->report_final_payments = (float) $booking->payments
                ->where('payment_type', 'balance')
                ->where('status', 'Collected')
                ->sum('amount');
            $booking->report_total_collected = $booking->report_verified_deposit + $booking->report_final_payments;
            $booking->report_remaining = $booking->status === 'Cancelled'
                ? 0
                : max(0, $booking->report_total - $booking->report_total_collected);
            $booking->report_reference = 'CAT-'.str_pad((string) $booking->id, 6, '0', STR_PAD_LEFT);
            $booking->report_selected_items = $this->selectedItems($booking);
        });
    }

    public function decoratePaymentStatuses(Collection $bookings): Collection
    {
        return $bookings->each(function (CateringBooking $booking): void {
            $booking->payment_summary = $this->paymentSummary($booking);
        });
    }

    public function paymentSummary(CateringBooking $booking): array
    {
        $totalCents = $booking->service_type === 'Custom Catering' && $booking->quote_amount === null
            ? 0
            : $this->moneyCents($booking->quote_amount ?? $booking->estimated_total);
        $verifiedDepositCents = $booking->payments
            ->where('payment_type', 'deposit')
            ->where('status', 'Verified')
            ->sum(fn ($payment): int => $this->moneyCents($payment->verified_amount ?? $payment->amount));
        $collectedBalanceCents = $booking->payments
            ->where('payment_type', 'balance')
            ->where('status', 'Collected')
            ->sum(fn ($payment): int => $this->moneyCents($payment->amount));
        $collectedCents = $verifiedDepositCents + $collectedBalanceCents;
        $latestDeposit = $booking->payments
            ->where('payment_type', 'deposit')
            ->sortByDesc('id')
            ->first();

        $status = 'Unpaid';
        if ($totalCents > 0 && $collectedCents >= $totalCents) {
            $status = 'Fully Paid';
        } elseif ($verifiedDepositCents > 0) {
            $status = 'Deposit Paid (30%)';
        } elseif ($latestDeposit?->status === 'Pending Verification') {
            $status = 'Pending Verification';
        } elseif ($latestDeposit?->status === 'Rejected') {
            $status = 'Payment Rejected';
        }

        return [
            'status' => $status,
            'payment_required' => ! ($booking->service_type === 'Custom Catering' && $totalCents === 0),
            'total_cents' => $totalCents,
            'required_deposit_cents' => intdiv(($totalCents * 30) + 50, 100),
            'verified_deposit_cents' => $verifiedDepositCents,
            'collected_cents' => $collectedCents,
            'remaining_cents' => max(0, $totalCents - $collectedCents),
            'latest_payment' => $latestDeposit,
        ];
    }

    private function moneyCents(mixed $amount): int
    {
        $value = trim((string) ($amount ?? '0'));
        if (! preg_match('/^-?\d+(?:\.\d{1,})?$/', $value)) {
            return 0;
        }

        [$whole, $fraction] = array_pad(explode('.', $value, 2), 2, '');
        $fraction = str_pad(substr($fraction, 0, 2), 2, '0');

        return ((int) $whole * 100) + (int) $fraction;
    }

    public function exportRows(Collection $bookings): Collection
    {
        return $this->decorate($bookings)->map(function (CateringBooking $booking): array {
            return [
                $booking->report_reference,
                $booking->client_name,
                $booking->phone,
                $booking->service_type,
                $booking->package_preference ?: $this->selectedItems($booking),
                optional($booking->created_at)->timezone('Asia/Manila')->format('Y-m-d H:i'),
                optional($booking->event_date)->format('Y-m-d'),
                $booking->guest_count,
                $booking->report_total,
                $booking->report_required_deposit,
                $booking->report_verified_deposit,
                $booking->report_remaining,
                $booking->report_total_collected,
                $booking->payment_status,
                $booking->status,
            ];
        });
    }

    public function selectedItems(CateringBooking $booking): string
    {
        $items = collect($booking->menu_selections ?? [])
            ->flatten()
            ->filter()
            ->implode(', ');
        $trays = $booking->trayItems->map(fn ($item) => "{$item->dish} x{$item->quantity}")->implode(', ');

        return Str::limit(collect([$items, $trays])->filter()->implode('; '), 160, '...');
    }

    private function monthExpression(string $column): string
    {
        return DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', {$column})"
            : "DATE_FORMAT({$column}, '%Y-%m')";
    }
}
