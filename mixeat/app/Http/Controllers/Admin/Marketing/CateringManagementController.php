<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CateringBooking;
use App\Models\CateringPartner;
use App\Models\CateringPayment;
use App\Models\CateringQuotationVersion;
use App\Models\User;
use App\Services\CateringReportService;
use App\Services\CateringNotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CateringManagementController extends Controller
{
    private const BOOKING_STATUSES = [
        'Pending Review',
        'Pending Quotation',
        'Quotation Sent',
        'Quotation Declined',
        'Awaiting Deposit',
        'New',
        'In Review',
        'Contacted',
        'Accepted',
        'Declined',
        'Approved',
        'Rejected',
        'In Preparation',
        'Dispatched',
        'Completed',
        'Converted',
        'Cancelled',
    ];

    public function dashboard()
    {
        $branchId = request()->user()->branch_id;
        abort_unless(request()->user()->isSupervisor() && $branchId, 403, 'Unauthorized access or no branch assigned.');

        return view('admin.marketing.dashboard', [
            'totalBookings' => CateringBooking::query()->where('branch_id', $branchId)->count(),
            'awaitingReview' => CateringBooking::query()
                ->where('branch_id', $branchId)
                ->whereIn('status', ['New', 'Pending Review', 'In Review', 'Awaiting Deposit Verification', 'Pending Quotation', 'Quotation Sent', 'Quotation Declined', 'Awaiting Deposit'])
                ->count(),
            'acceptedBookings' => CateringBooking::query()
                ->where('branch_id', $branchId)
                ->whereIn('status', ['Accepted', 'Approved', 'Confirmed', 'In Preparation', 'Preparing', 'Dispatched', 'Out for Delivery', 'Completed', 'Delivered'])
                ->count(),
            'visiblePartners' => 0,
        ]);
    }

    public function bookings(CateringReportService $reportService)
    {
        $user = request()->user();
        abort_unless($user->isSupervisor() && $user->branch_id, 403, 'Unauthorized access or no branch assigned.');

        $bookings = CateringBooking::query()
                ->where('branch_id', $user->branch_id)
                ->with(['branch', 'trayItems', 'payments.verifiedBy', 'payments.collectedBy', 'quotationVersions'])
                ->latest()
                ->paginate(20);
        $bookings->setCollection($reportService->decoratePaymentStatuses($bookings->getCollection()));

        return view('admin.marketing.catering-bookings', [
            'bookings' => $bookings,
            'statuses' => self::BOOKING_STATUSES,
        ]);
    }

    public function reports(Request $request, CateringReportService $reportService)
    {
        $user = $this->authorizeCommissarySupervisor($request);
        $filters = $reportService->filters($request);
        $query = $reportService->query($filters, (int) $user->branch_id);
        $summary = $reportService->summary($query);
        $charts = $reportService->charts($query, $filters);
        $sort = $request->validate([
            'sort' => ['nullable', Rule::in(['created_at', 'event_date', 'client_name', 'service_type', 'status'])],
            'direction' => ['nullable', Rule::in(['asc', 'desc'])],
        ]);
        $bookings = (clone $query)
            ->orderBy($sort['sort'] ?? 'created_at', $sort['direction'] ?? 'desc')
            ->paginate(20)
            ->withQueryString();
        $bookings->setCollection($reportService->decorate($bookings->getCollection()));

        return view('admin.marketing.catering-reports', compact(
            'bookings',
            'summary',
            'charts',
            'filters'
        ) + [
            'categories' => ['Packed Meals', 'Party Trays', 'Catering Packages'],
            'statuses' => self::BOOKING_STATUSES,
            'paymentStatuses' => ['Unpaid', 'Pending Verification', 'Deposit Verified / Partially Paid', 'Fully Paid', 'Rejected'],
        ]);
    }

    public function exportReports(Request $request, CateringReportService $reportService, string $format)
    {
        $user = $this->authorizeCommissarySupervisor($request);
        abort_unless(in_array($format, ['csv', 'xlsx', 'pdf', 'print'], true), 404);
        $filters = $reportService->filters($request);
        $query = $reportService->query($filters, (int) $user->branch_id);
        $summary = $reportService->summary($query);
        $bookings = $reportService->decorate((clone $query)->latest()->get());
        $rows = $reportService->exportRows($bookings);
        $title = 'MixEat Commissary Catering Report';

        if ($format === 'print' || $format === 'pdf') {
            $view = view('admin.marketing.catering-report-print', compact('bookings', 'summary', 'filters', 'title'))->render();
            if ($format === 'print') {
                return response($view);
            }

            return Pdf::loadView('admin.marketing.catering-report-print', compact('bookings', 'summary', 'filters', 'title'))
                ->setPaper('a4', 'landscape')
                ->download('mixeat-catering-report.pdf');
        }

        $headers = [
            'Booking Reference', 'Customer Name', 'Contact Number', 'Category',
            'Package or Selected Items', 'Booking Date', 'Event Date', 'Guests / Quantity',
            'Total Billing', 'Required Deposit', 'Verified Deposit Paid', 'Remaining Balance',
            'Total Collected', 'Payment Status', 'Booking Status',
        ];

        if ($format === 'csv') {
            return response()->streamDownload(function () use ($headers, $rows, $summary, $filters): void {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['MixEat Commissary Catering Report']);
                fputcsv($handle, [$this->reportDateRange($filters), 'Generated '.now('Asia/Manila')->format('Y-m-d H:i')]);
                fputcsv($handle, ['Total bookings', $summary['total_bookings'], 'Total collected', number_format($summary['total_collected'], 2, '.', '')]);
                fputcsv($handle, []);
                fputcsv($handle, $headers);
                foreach ($rows as $row) {
                    fputcsv($handle, $row);
                }
                fclose($handle);
            }, 'mixeat-catering-report.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        return $this->xlsxDownload($headers, $rows, $summary, $filters);
    }

    public function updateBookingStatus(Request $request, CateringBooking $booking, CateringNotificationService $notifications)
    {
        $this->authorizeCommissarySupervisor($request);
        $this->authorizeBranch($request, $booking);
        $previousStatus = $booking->status;
        $data = $request->validate([
            'status' => ['required', Rule::in(self::BOOKING_STATUSES)],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($booking->payments()->exists()) {
            if ($data['status'] !== 'Cancelled') {
                abort_unless(
                    $booking->payments()->where('payment_type', 'deposit')->where('status', 'Verified')->exists()
                        && in_array($data['status'], ['Approved', 'In Preparation', 'Dispatched', 'Completed'], true),
                    422,
                    'Verify the deposit before confirming or progressing this booking.'
                );
            }

        }

        $booking->update($data);
        $event = match ($data['status']) {
            'Confirmed' => 'confirmed',
            'Accepted', 'Approved' => 'approved',
            'Declined' => 'declined',
            'Cancelled' => 'cancelled',
            'Preparing', 'In Preparation' => 'preparing',
            'Out for Delivery', 'Dispatched' => 'out_for_delivery',
            'Delivered', 'Completed' => 'completed',
            default => null,
        };
        if ($event && $previousStatus !== $data['status']) {
            $notifications->send($booking->fresh(), $event, $data['reason'] ?? null);
        }

        return back()->with('admin_status', 'Catering booking status updated.');
    }

    public function sendQuotation(Request $request, CateringBooking $booking, CateringNotificationService $notifications)
    {
        $user = $this->authorizeCommissarySupervisor($request);
        abort_unless($booking->branch_id === $user->branch_id && $booking->service_type === 'Custom Catering', 403);
        $data = $request->validate([
            'quote_amount' => ['required', 'numeric', 'gt:0', 'max:99999999'],
            'quotation_notes' => ['nullable', 'string', 'max:5000'],
        ]);
        $isRevision = (int) $booking->quotation_version > 0;

        DB::transaction(function () use ($booking, $user, $data): void {
            $lockedBooking = CateringBooking::query()->lockForUpdate()->findOrFail($booking->id);
            abort_unless(in_array($lockedBooking->status, ['Pending Quotation', 'Quotation Sent', 'Quotation Declined'], true), 422, 'This quotation cannot be revised at its current status.');
            $nextVersion = ((int) $lockedBooking->quotation_version) + 1;
            $lockedBooking->quotationVersions()->whereIn('status', ['Sent', 'Accepted'])->update(['status' => 'Withdrawn']);
            $lockedBooking->quotationVersions()->create([
                'version' => $nextVersion,
                'amount' => $data['quote_amount'],
                'notes' => $data['quotation_notes'] ?? null,
                'status' => 'Sent',
                'sent_at' => now(),
                'created_by' => $user->id,
            ]);
            $lockedBooking->update([
                'quote_amount' => $data['quote_amount'],
                'quotation_notes' => $data['quotation_notes'] ?? null,
                'quotation_status' => 'Sent',
                'quotation_version' => $nextVersion,
                'quotation_sent_at' => now(),
                'quotation_sent_by' => $user->id,
                'quotation_accepted_at' => null,
                'quotation_declined_at' => null,
                'status' => 'Quotation Sent',
                'payment_status' => 'Unpaid',
            ]);
        });

        $notifications->send($booking->fresh(), $isRevision ? 'quotation_revised' : 'quotation_sent');

        return back()->with('admin_status', 'Custom catering quotation sent to the customer.');
    }

    public function calendar()
    {
        return view('admin.marketing.catering-calendar', [
            'bookings' => CateringBooking::query()
                ->with(['branch', 'assignedStaff', 'trayItems', 'payments'])
                ->orderBy('event_date')
                ->orderBy('event_time')
                ->get(),
            'branches' => Branch::query()->orderBy('name')->get(),
            'staffByBranch' => User::query()
                ->where('role', 'supervisor')
                ->with('branch')
                ->orderBy('name')
                ->get()
                ->groupBy('branch_id'),
            'statuses' => self::BOOKING_STATUSES,
        ]);
    }

    public function updateCalendarBooking(Request $request, CateringBooking $booking)
    {
        $hasPaymentWorkflow = $booking->payments()->exists();
        $rules = [
            'status' => [$hasPaymentWorkflow ? 'nullable' : 'required', $hasPaymentWorkflow
                ? Rule::in(['Confirmed', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled'])
                : Rule::in(self::BOOKING_STATUSES)],
            'quote_amount' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'branch_id' => ['nullable', 'integer', Rule::exists('branches', 'id')],
            'assigned_staff_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query
                    ->where('role', 'supervisor')
                    ->where('branch_id', $request->input('branch_id'))),
            ],
        ];
        if (! $hasPaymentWorkflow) {
            $rules['payment_status'] = ['required', Rule::in(['Unpaid', 'Deposit Paid', 'Fully Paid'])];
        }
        $data = $request->validate($rules);

        if ($hasPaymentWorkflow) {
            $depositVerified = $booking->payments()
                ->where('payment_type', 'deposit')
                ->where('status', 'Verified')
                ->exists();
            if (isset($data['status']) && $data['status'] !== 'Cancelled' && ! $depositVerified) {
                throw ValidationException::withMessages([
                    'status' => 'Verify the deposit before confirming or progressing this booking.',
                ]);
            }
            unset($data['payment_status']);
            if (! isset($data['status'])) {
                unset($data['status']);
            }
        }

        $booking->update($data);

        return back()->with('admin_status', "Catering booking for {$booking->client_name} updated.");
    }

    public function verifyDeposit(Request $request, CateringBooking $booking, CateringPayment $payment, CateringNotificationService $notifications)
    {
        $this->authorizeCommissarySupervisor($request);
        $this->authorizeBranch($request, $booking);
        DB::transaction(function () use ($request, $booking, $payment): void {
            $lockedBooking = CateringBooking::query()->lockForUpdate()->findOrFail($booking->id);
            $lockedPayment = $lockedBooking->payments()->whereKey($payment->id)->lockForUpdate()->firstOrFail();
            abort_unless($lockedPayment->payment_type === 'deposit' && $lockedPayment->status === 'Pending Verification', 422);
            abort_unless($lockedBooking->status !== 'Cancelled', 422, 'A cancelled booking cannot be confirmed.');

            $lockedPayment->update([
                'status' => 'Verified',
                'verified_amount' => $lockedPayment->amount,
                'verified_at' => now(),
                'verified_by' => $request->user()->id,
            ]);
            $lockedBooking->update([
                'payment_status' => 'Deposit Verified / Partially Paid',
                'status' => 'Confirmed',
            ]);
        });

        $notifications->send($booking->fresh(), 'deposit_verified', null, (string) $payment->id);

        return back()->with('admin_status', 'Deposit verified and booking confirmed.');
    }

    public function rejectDeposit(Request $request, CateringBooking $booking, CateringPayment $payment, CateringNotificationService $notifications)
    {
        $this->authorizeCommissarySupervisor($request);
        $this->authorizeBranch($request, $booking);
        $data = $request->validate([
            'verification_remarks' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        DB::transaction(function () use ($request, $booking, $payment, $data): void {
            $lockedBooking = CateringBooking::query()->lockForUpdate()->findOrFail($booking->id);
            $lockedPayment = $lockedBooking->payments()->whereKey($payment->id)->lockForUpdate()->firstOrFail();
            abort_unless($lockedPayment->payment_type === 'deposit' && $lockedPayment->status === 'Pending Verification', 422);

            $lockedPayment->update([
                'status' => 'Rejected',
                'verified_at' => now(),
                'verified_by' => $request->user()->id,
                'verification_remarks' => $data['verification_remarks'],
            ]);
            $lockedBooking->update(['payment_status' => 'Rejected']);
        });

        $notifications->send($booking->fresh(), 'deposit_rejected', $data['verification_remarks'], (string) $payment->id);

        return back()->with('admin_status', 'Deposit rejected. Share the secure correction link with the customer.');
    }

    public function collectBalance(Request $request, CateringBooking $booking, CateringNotificationService $notifications)
    {
        $this->authorizeCommissarySupervisor($request);
        $this->authorizeBranch($request, $booking);
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0', 'max:99999999'],
            'payment_method' => ['required', Rule::in(['Cash', 'Bank Transfer', 'Security Bank QR'])],
        ]);

        $paymentId = DB::transaction(function () use ($request, $booking, $data): int {
            $lockedBooking = CateringBooking::query()->lockForUpdate()->findOrFail($booking->id);
            abort_unless($lockedBooking->status !== 'Cancelled', 422, 'A cancelled booking cannot accept payment.');
            $deposit = $lockedBooking->payments()
                ->where('payment_type', 'deposit')
                ->where('status', 'Verified')
                ->first();
            abort_unless($deposit, 422, 'The deposit must be verified before collecting the balance.');

            $paidCents = (int) round(((float) $deposit->verified_amount) * 100);
            $paidCents += (int) round(((float) $lockedBooking->payments()
                ->where('payment_type', 'balance')
                ->where('status', 'Collected')
                ->sum('amount')) * 100);
            $totalCents = (int) round(((float) $lockedBooking->estimated_total) * 100);
            $amountCents = (int) round(((float) $data['amount']) * 100);
            if ($amountCents > $totalCents - $paidCents) {
                throw ValidationException::withMessages(['amount' => 'The collected amount cannot exceed the remaining balance.']);
            }

            $payment = $lockedBooking->payments()->create([
                'payment_type' => 'balance',
                'payment_method' => $data['payment_method'],
                'amount' => number_format($amountCents / 100, 2, '.', ''),
                'status' => 'Collected',
                'collected_at' => now(),
                'collected_by' => $request->user()->id,
            ]);

            $isFullyPaid = $amountCents + $paidCents >= $totalCents;
            $lockedBooking->update([
                'payment_status' => $isFullyPaid ? 'Fully Paid' : 'Deposit Verified / Partially Paid',
            ]);

            return (int) $payment->id;
        });

        $notifications->send($booking->fresh(), 'payment_confirmed', null, (string) $paymentId);

        return back()->with('admin_status', 'Payment collection recorded.');
    }

    public function partners()
    {
        return view('admin.marketing.catering-partners', [
            'partners' => CateringPartner::query()->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function storePartner(Request $request)
    {
        $data = $this->validatedPartner($request, true);
        unset($data['logo']);
        $data['logo_path'] = $request->file('logo')->store('catering-partners', 'public');
        $data['logo_url'] = null;

        CateringPartner::create($data);

        return back()->with('admin_status', 'Catering partner added.');
    }

    public function updatePartner(Request $request, CateringPartner $partner)
    {
        $data = $this->validatedPartner($request, false);
        unset($data['logo']);
        $oldLogo = $partner->logo_path;

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('catering-partners', 'public');
            $data['logo_url'] = null;
        }

        $partner->update($data);

        if ($request->hasFile('logo') && $oldLogo) {
            Storage::disk('public')->delete($oldLogo);
        }

        return back()->with('admin_status', 'Catering partner updated.');
    }

    public function deletePartner(CateringPartner $partner)
    {
        $logoPath = $partner->logo_path;
        $partner->delete();

        if ($logoPath) {
            Storage::disk('public')->delete($logoPath);
        }

        return back()->with('admin_status', 'Catering partner deleted.');
    }

    private function validatedPartner(Request $request, bool $requireLogo): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'website_url' => ['nullable', 'url', 'max:2048'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'is_visible' => ['nullable', 'boolean'],
            'logo' => [
                $requireLogo ? 'required' : 'nullable',
                'file',
                'mimes:png,jpg,jpeg,svg,webp',
                'max:5120',
            ],
        ]) + ['is_visible' => $request->boolean('is_visible')];
    }

    private function authorizeCommissarySupervisor(Request $request): User
    {
        $user = $request->user();
        abort_unless(
            $user
            && $user->isSupervisor()
            && ($user->is_active ?? true)
            && $user->branch_id
            && $user->branch
            && $user->branch->name === 'MixEat Commissary',
            403,
            'Only an active MixEat Commissary Supervisor may access catering reports.'
        );

        return $user;
    }

    private function reportDateRange(array $filters): string
    {
        if (! $filters['date_from'] && ! $filters['date_to']) {
            return 'All dates';
        }

        return 'Date range: '.($filters['date_from'] ?: 'Beginning').' to '.($filters['date_to'] ?: 'Present')
            .' ('.($filters['date_basis'] === 'created' ? 'booking creation date' : 'event date').')';
    }

    private function xlsxDownload(array $headers, \Illuminate\Support\Collection $rows, array $summary, array $filters)
    {
        $path = tempnam(sys_get_temp_dir(), 'mixeat-report-');
        $zip = new \ZipArchive();
        abort_unless($path !== false && $zip->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true, 500, 'Unable to create Excel report.');
        $xml = static fn (string $value): string => htmlspecialchars($value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
        $sheetRows = [
            ['MixEat Commissary Catering Report'],
            [$this->reportDateRange($filters), 'Generated '.$this->nowManila()->format('Y-m-d H:i')],
            ['Total bookings', (string) $summary['total_bookings'], 'Total collected', number_format($summary['total_collected'], 2, '.', '')],
            [],
            $headers,
        ];
        foreach ($rows as $row) {
            $sheetRows[] = array_map(fn ($value) => (string) $value, $row);
        }
        $sheet = '';
        foreach ($sheetRows as $rowIndex => $row) {
            $sheet .= '<row r="'.($rowIndex + 1).'">';
            foreach (array_values($row) as $columnIndex => $value) {
                $cellRef = $this->excelColumn($columnIndex + 1).($rowIndex + 1);
                $numeric = is_numeric($value) && $value !== '';
                $sheet .= '<c r="'.$cellRef.'"'.($numeric ? '' : ' t="inlineStr"').'>'
                    .($numeric ? '<v>'.$xml($value).'</v>' : '<is><t>'.$xml($value).'</t></is>')
                    .'</c>';
            }
            $sheet .= '</row>';
        }
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>');
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Catering Report" sheetId="1" r:id="rId1"/></sheets></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>');
        $zip->addFromString('xl/worksheets/sheet1.xml', '<?xml version="1.0" encoding="UTF-8"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>'.$sheet.'</sheetData></worksheet>');
        $zip->close();

        return response()->download($path, 'mixeat-catering-report.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private function excelColumn(int $number): string
    {
        $column = '';
        while ($number > 0) {
            $remainder = ($number - 1) % 26;
            $column = chr(65 + $remainder).$column;
            $number = intdiv($number - 1, 26);
        }

        return $column;
    }

    private function nowManila(): \Carbon\Carbon
    {
        return \Carbon\Carbon::now('Asia/Manila');
    }

    private function authorizeBranch(Request $request, CateringBooking $booking): void
    {
        $user = $request->user();
        abort_unless($user && $user->isSupervisor() && $user->branch_id, 403, 'Unauthorized access or no branch assigned.');
        abort_unless((int) $booking->branch_id === (int) $user->branch_id, 403, 'This catering booking belongs to another branch.');
    }
}
