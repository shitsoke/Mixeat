<?php

namespace App\Http\Controllers;

use App\Models\CateringBooking;
use App\Models\CateringMenuItem;
use App\Models\CateringPackage;
use App\Models\PackedMealPackage;
use App\Models\PartyTray;
use App\Models\CateringPayment;
use App\Models\Branch;
use App\Models\CateringQuotationVersion;
use App\Services\CateringPackageCatalog;
use App\Services\CateringNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CateringInquiryController extends Controller
{
    public function showQuotation(CateringBooking $booking)
    {
        abort_unless(request()->user() || request()->hasValidSignature(), 403, 'This quotation link is invalid or expired.');
        $this->authorizeCustomerBooking(request(), $booking);
        $quotation = $booking->quotationVersions()->where('version', $booking->quotation_version)->whereIn('status', ['Sent', 'Accepted', 'Declined'])->firstOrFail();

        return view('storefront.catering-quotation', compact('booking', 'quotation'));
    }

    public function acceptQuotation(Request $request, CateringBooking $booking, CateringNotificationService $notifications)
    {
        $this->authorizeCustomerBooking($request, $booking);
        $result = DB::transaction(function () use ($booking): string {
            $lockedBooking = CateringBooking::query()->lockForUpdate()->findOrFail($booking->id);
            $quotation = $lockedBooking->quotationVersions()->where('version', $lockedBooking->quotation_version)->where('status', 'Sent')->lockForUpdate()->firstOrFail();
            $lockedBooking->update([
                'quote_amount' => $quotation->amount,
                'estimated_total' => $quotation->amount,
                'quotation_status' => 'Accepted',
                'quotation_accepted_at' => now(),
                'status' => 'Awaiting Deposit',
                'payment_status' => 'Unpaid',
            ]);
            $quotation->update(['status' => 'Accepted', 'accepted_at' => now()]);

            return sprintf('MXC-%s-%06d', $lockedBooking->created_at->format('Y'), $lockedBooking->id);
        });

        $notifications->send($booking->fresh(), 'quotation_accepted');

        return redirect()->to($this->quotationUrl($booking))
            ->with('catering_status', "Quotation {$result} accepted. The 30% deposit can now be submitted.");
    }

    public function declineQuotation(Request $request, CateringBooking $booking, CateringNotificationService $notifications)
    {
        $this->authorizeCustomerBooking($request, $booking);
        DB::transaction(function () use ($booking): void {
            $lockedBooking = CateringBooking::query()->lockForUpdate()->findOrFail($booking->id);
            $quotation = $lockedBooking->quotationVersions()->where('version', $lockedBooking->quotation_version)->where('status', 'Sent')->lockForUpdate()->firstOrFail();
            $quotation->update(['status' => 'Declined', 'declined_at' => now()]);
            $lockedBooking->update([
                'quotation_status' => 'Declined',
                'quotation_declined_at' => now(),
                'status' => 'Quotation Declined',
            ]);
        });

        $notifications->send($booking->fresh(), 'quotation_declined');

        return redirect()->to($this->quotationUrl($booking))
            ->with('catering_status', 'The quotation was declined.');
    }

    public function submitQuotationDeposit(Request $request, CateringBooking $booking, CateringNotificationService $notifications)
    {
        $this->authorizeCustomerBooking($request, $booking);
        $data = $request->validate([
            'payment_reference' => ['required', 'string', 'max:64', 'regex:/^[A-Za-z0-9]+$/'],
            'payment_confirmation' => ['accepted'],
        ]);

        DB::transaction(function () use ($booking, $data): void {
            $lockedBooking = CateringBooking::query()->lockForUpdate()->findOrFail($booking->id);
            abort_unless($lockedBooking->service_type === 'Custom Catering' && $lockedBooking->status === 'Awaiting Deposit', 409, 'This quotation is not awaiting a deposit.');
            abort_unless(! $lockedBooking->payments()->where('payment_type', 'deposit')->whereIn('status', ['Pending Verification', 'Verified'])->exists(), 409, 'A deposit is already recorded for this quotation.');
            $totalCents = (int) round(((float) $lockedBooking->quote_amount) * 100);
            $depositAmount = number_format(intdiv(($totalCents * 30) + 50, 100) / 100, 2, '.', '');
            $duplicateReference = CateringPayment::query()
                ->whereRaw('LOWER(payment_reference) = ?', [strtolower($data['payment_reference'])])
                ->exists();
            if ($duplicateReference) {
                CateringPayment::query()->whereRaw('LOWER(payment_reference) = ?', [strtolower($data['payment_reference'])])->update(['duplicate_reference' => true]);
            }
            $lockedBooking->payments()->create([
                'payment_type' => 'deposit',
                'payment_method' => 'Security Bank QR',
                'amount' => $depositAmount,
                'payment_reference' => $data['payment_reference'],
                'status' => 'Pending Verification',
                'duplicate_reference' => $duplicateReference,
                'submitted_at' => now(),
            ]);
            $lockedBooking->update([
                'status' => 'Awaiting Deposit Verification',
                'payment_status' => 'Pending Verification',
            ]);
        });

        $notifications->send($booking->fresh(), 'deposit_submitted');

        return redirect()->route('catering.quotation', $booking)->with('catering_status', 'Deposit details submitted. Your payment will be reviewed by MixEat Commissary.');
    }
    public function showPaymentCorrection(Request $request, CateringBooking $booking)
    {
        $this->authorizeCustomerBooking($request, $booking);
        $payment = $booking->payments()->where('payment_type', 'deposit')->latest('id')->firstOrFail();
        abort_unless($payment->status === 'Rejected', 404);
        $totalCents = (int) round(((float) $booking->estimated_total) * 100);
        $depositCents = (int) round(((float) $payment->amount) * 100);

        return view('storefront.catering-payment-correction', [
            'booking' => $booking,
            'payment' => $payment,
            'bookingReference' => sprintf('MXC-%s-%06d', $booking->created_at->format('Y'), $booking->id),
            'estimatedTotal' => number_format($totalCents / 100, 2),
            'depositAmount' => number_format($depositCents / 100, 2),
            'remainingBalance' => number_format(($totalCents - $depositCents) / 100, 2),
            'correctionSubmitUrl' => URL::temporarySignedRoute(
                'catering.payments.correction.submit',
                Carbon::createFromTimestamp((int) request()->query('expires')),
                ['booking' => $booking->id]
            ),
        ]);
    }

    public function resubmitDeposit(Request $request, CateringBooking $booking, CateringNotificationService $notifications)
    {
        $this->authorizeCustomerBooking($request, $booking);
        if (is_string($request->input('payment_reference'))) {
            $request->merge(['payment_reference' => trim($request->input('payment_reference'))]);
        }
        $data = $request->validate([
            'payment_reference' => ['required', 'string', 'max:64', 'regex:/^[A-Za-z0-9]+$/'],
            'payment_confirmation' => ['accepted'],
        ]);

        $bookingReference = DB::transaction(function () use ($booking, $data): string {
            $lockedBooking = CateringBooking::query()->lockForUpdate()->findOrFail($booking->id);
            $latestDeposit = $lockedBooking->payments()
                ->where('payment_type', 'deposit')
                ->latest('id')
                ->lockForUpdate()
                ->firstOrFail();
            abort_unless($latestDeposit->status === 'Rejected', 409, 'This payment correction link has already been used.');

            $duplicateReference = CateringPayment::query()
                ->whereRaw('LOWER(payment_reference) = ?', [strtolower($data['payment_reference'])])
                ->exists();
            if ($duplicateReference) {
                CateringPayment::query()
                    ->whereRaw('LOWER(payment_reference) = ?', [strtolower($data['payment_reference'])])
                    ->update(['duplicate_reference' => true]);
            }

            $lockedBooking->payments()->create([
                'payment_type' => 'deposit',
                'payment_method' => 'Security Bank QR',
                'amount' => $latestDeposit->amount,
                'payment_reference' => $data['payment_reference'],
                'status' => 'Pending Verification',
                'duplicate_reference' => $duplicateReference,
                'submitted_at' => now(),
            ]);
            $lockedBooking->update([
                'status' => 'Awaiting Deposit Verification',
                'payment_status' => 'Pending Verification',
            ]);

            return sprintf('MXC-%s-%06d', $lockedBooking->created_at->format('Y'), $lockedBooking->id);
        });

        $notifications->send($booking->fresh(), 'deposit_submitted');

        return view('storefront.catering-payment-correction-complete', [
            'bookingReference' => $bookingReference,
        ]);
    }

    private function authorizeCustomerBooking(Request $request, CateringBooking $booking): void
    {
        if ($booking->user_id && (! $request->user() || (int) $booking->user_id !== (int) $request->user()->id)) {
            abort(403, 'This catering booking does not belong to your account.');
        }
    }

    private function quotationUrl(CateringBooking $booking): string
    {
        return URL::temporarySignedRoute(
            'catering.quotation',
            now()->addDays(14),
            ['booking' => $booking->id]
        );
    }

    public function store(Request $request, CateringNotificationService $notifications)
    {
        if (is_string($request->input('payment_reference'))) {
            $request->merge(['payment_reference' => trim($request->input('payment_reference'))]);
        }

        $isFullService = $request->input('service_type') === 'Full-Service Catering';
        $isPackedMeals = $request->input('service_type') === 'Packed Meals';
        $isPartyTrays = $request->input('service_type') === 'Party Trays';
        $isCustom = $request->input('service_type') === 'Custom Catering';
        $activePackages = CateringPackage::active()->get()->keyBy('code');
        $activePackedMeals = PackedMealPackage::active()->get()->keyBy('code');
        $activePartyTrays = PartyTray::active()->get()->keyBy('name');
        $paymentConfirmationRules = $isCustom ? ['nullable'] : ['required', 'accepted'];
        $rules = [
            'client_name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'min:7', 'max:30'],
            'company_name' => ['nullable', 'string', 'max:160'],
            'event_date' => ['required', 'date', 'after_or_equal:today'],
            'event_time' => ['required', 'date_format:H:i'],
            'venue' => ['required', 'string', 'max:255'],
            'event_type' => ['required', Rule::in(['Meeting', 'Wedding', 'Birthday', 'Anniversary', 'Graduation', 'Other', 'Corporate', 'Office Party', 'Celebration'])],
            'event_type_details' => [
                Rule::requiredIf($request->input('event_type') === 'Other'),
                'nullable',
                'string',
                'max:160',
            ],
            'service_type' => ['required', Rule::in(['Packed Meals', 'Party Trays', 'Full-Service Catering', 'Custom Catering'])],
            'package_preference' => ['nullable', 'string', 'max:255'],
            'guest_count' => ['required', 'integer', 'min:1', 'max:5000'],
            'budget_range' => ['nullable', 'string', 'max:100'],
            'special_requests' => ['nullable', 'string', 'max:2000'],
            'campaign_source' => ['nullable', 'string', 'max:100'],
            'payment_reference' => [Rule::requiredIf(! $isCustom), 'nullable', 'string', 'max:64', 'regex:/^[A-Za-z0-9]+$/'],
            'payment_confirmation' => $paymentConfirmationRules,
            'catering_package' => [
                Rule::requiredIf($isFullService),
                'nullable',
                Rule::in($activePackages->keys()->all()),
            ],
            'packed_meal_package' => [
                Rule::requiredIf($isPackedMeals),
                'nullable',
                'string',
                Rule::in($activePackedMeals->keys()->all()),
            ],
            'packed_meal_side' => [
                Rule::requiredIf($isPackedMeals),
                'nullable',
                'string',
                Rule::in($activePackedMeals->flatMap(fn (PackedMealPackage $meal) => $meal->included_dishes['sides'] ?? [])->unique()->values()->all()),
            ],
            'menu_selections' => [
                Rule::requiredIf($isFullService || $isCustom),
                'nullable',
                'array:meat,soup,vegetables,pasta,dessert,rice,drinks',
            ],
            'submission_token' => [
                Rule::requiredIf($isFullService || $isPackedMeals || $isPartyTrays || $isCustom),
                'nullable',
                'uuid',
                'unique:catering_bookings,submission_token',
            ],
        ];

        $customCategories = [
            'meat' => [],
            'vegetables' => [],
            'soup' => [],
            'pasta' => [],
            'dessert' => [],
            'rice' => [],
            'drinks' => [],
        ];

        if ($isCustom) {
            $activeMenuItems = CateringMenuItem::query()->active()->get()->groupBy('category');
            foreach ($customCategories as $category => &$validDishes) {
                $validDishes = $activeMenuItems->get($category, collect())->pluck('name')->all();
            }
            unset($validDishes);
            foreach ($customCategories as $category => $validDishes) {
                $rules["menu_selections.{$category}"] = ['nullable', 'array', 'max:20'];
                $rules["menu_selections.{$category}.*"] = ['required', 'string', 'distinct', Rule::in($validDishes)];
            }
        } else {
            $rules['menu_selections.meat'] = [
                Rule::requiredIf($isFullService),
                'nullable',
                'array',
                'min:1',
                'max:3',
            ];
            $rules['menu_selections.meat.*'] = ['required', 'string', 'distinct', Rule::in(CateringPackageCatalog::meatItems())];
            $rules['menu_selections.soup'] = ['required_if:service_type,Full-Service Catering', 'nullable', 'string', Rule::in(CateringPackageCatalog::MENU['soup']['items'])];
            $rules['menu_selections.vegetables'] = ['required_if:service_type,Full-Service Catering', 'nullable', 'string', Rule::in(CateringPackageCatalog::MENU['vegetables']['items'])];
            $rules['menu_selections.pasta'] = ['required_if:service_type,Full-Service Catering', 'nullable', 'string', Rule::in(CateringPackageCatalog::MENU['pasta']['items'])];
            $rules['menu_selections.dessert'] = ['required_if:service_type,Full-Service Catering', 'nullable', 'string', Rule::in(CateringPackageCatalog::MENU['dessert']['items'])];
            $rules['menu_selections.rice'] = ['required_if:service_type,Full-Service Catering', 'nullable', 'string', Rule::in(CateringPackageCatalog::MENU['rice']['items'])];
            $rules['menu_selections.drinks'] = ['required_if:service_type,Full-Service Catering', 'nullable', 'string', Rule::in(CateringPackageCatalog::MENU['drinks']['items'])];
        }

        if ($isPartyTrays) {
            $rules += [
                'tray_items' => ['required', 'array', 'min:1', 'max:50'],
                'tray_items.*' => ['required', 'array:category,dish,size,quantity'],
                'tray_items.*.category' => ['required', 'string', Rule::in($activePartyTrays->keys()->all())],
                'tray_items.*.dish' => ['required', 'string', Rule::in($activePartyTrays->flatMap(fn (PartyTray $tray) => $tray->dishes ?? [])->unique()->values()->all())],
                'tray_items.*.size' => ['required', 'string', Rule::in(['full', 'half'])],
                'tray_items.*.quantity' => ['required', 'integer', 'min:1', 'max:5000'],
            ];
        }

        $data = $request->validate($rules);
        $authenticatedCustomer = auth()->user();
        if ($authenticatedCustomer?->role === 'customer') {
            $data['email'] = $authenticatedCustomer->email;
        }
        $paymentReference = $data['payment_reference'] ?? null;
        unset($data['payment_reference'], $data['payment_confirmation']);

        $trayItems = [];

        if ($isFullService) {
            $package = $activePackages->get($data['catering_package']);
            $meatCount = (int) ($package->included_dishes['meat_count'] ?? 0);
            if (count($data['menu_selections']['meat']) !== $meatCount) {
                throw ValidationException::withMessages([
                    'menu_selections.meat' => "Choose exactly {$meatCount} meat dishes for {$package->name}.",
                ]);
            }

            $data['package_preference'] = $package->name.' (₱'.number_format((float) $package->price, 2).' per pax)';
            $data['package_price_per_pax'] = $package->price;
            $data['estimated_total'] = (float) $package->price * $data['guest_count'];
            unset($data['catering_package']);
        } elseif ($isPackedMeals) {
            $packageCode = $data['packed_meal_package'];
            $package = $activePackedMeals->get($packageCode);
            $mainDish = $package->name;

            $data['package_preference'] = "Package {$packageCode} - {$mainDish}";
            $data['package_price_per_pax'] = $package->price;
            $data['estimated_total'] = (float) $package->price * $data['guest_count'];
            $data['menu_selections'] = array_merge([
                'package' => "Package {$packageCode}",
                'main_dish' => $mainDish,
                'side' => $data['packed_meal_side'],
            ], $package->included_dishes['fixed'] ?? []);
            unset($data['packed_meal_package'], $data['packed_meal_side']);
        } elseif ($isPartyTrays) {
            foreach ($data['tray_items'] as $index => $item) {
                $category = $activePartyTrays->get($item['category']);
                if (! in_array($item['dish'], $category->dishes ?? [], true)) {
                    throw ValidationException::withMessages([
                        "tray_items.{$index}.dish" => 'Choose a dish from the selected tray category.',
                    ]);
                }

                $key = implode('|', [$item['category'], $item['dish'], $item['size']]);
                $unitPrice = $item['size'] === 'full' ? $category->full_price : $category->half_price;
                if (isset($trayItems[$key])) {
                    $trayItems[$key]['quantity'] += $item['quantity'];
                } else {
                    $trayItems[$key] = [
                        'category' => $item['category'],
                        'dish' => $item['dish'],
                        'tray_size' => $item['size'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $unitPrice,
                    ];
                }
            }

            $trayItems = array_values($trayItems);
            foreach ($trayItems as &$trayItem) {
                $trayItem['subtotal'] = $trayItem['unit_price'] * $trayItem['quantity'];
            }
            unset($trayItem);

            $data['package_preference'] = 'Food Trays & Buffet Setups ('.count($trayItems).' tray selections)';
            $data['package_price_per_pax'] = null;
            $data['estimated_total'] = array_sum(array_column($trayItems, 'subtotal'));
            unset(
                $data['catering_package'],
                $data['packed_meal_package'],
                $data['packed_meal_side'],
                $data['menu_selections'],
                $data['tray_items'],
            );
        } elseif ($isCustom) {
            $customCategories = [
                'meat' => [],
                'vegetables' => [],
                'soup' => [],
                'pasta' => [],
                'dessert' => [],
                'rice' => [],
                'drinks' => [],
            ];
            $activeMenuItems = CateringMenuItem::query()->active()->get()->groupBy('category');
            foreach ($customCategories as $category => &$validDishes) {
                $validDishes = $activeMenuItems->get($category, collect())->pluck('name')->all();
            }
            unset($validDishes);
            $selections = $data['menu_selections'] ?? [];
            $selectedTotal = 0;
            $details = [];
            foreach ($customCategories as $category => $items) {
                $selected = $selections[$category] ?? [];
                $selected = is_array($selected) ? array_values(array_unique($selected)) : [];
                if (array_diff($selected, $items)) {
                    throw ValidationException::withMessages([
                        "menu_selections.{$category}" => "Select valid {$category} dishes.",
                    ]);
                }
                $selections[$category] = $selected;
                $details[$category] = $activeMenuItems->get($category, collect())
                    ->whereIn('name', $selected)
                    ->map(fn (CateringMenuItem $item): array => [
                        'name' => $item->name,
                        'description' => $item->description,
                        'image' => $item->image,
                    ])
                    ->values()
                    ->all();
                $selectedTotal += count($selected);
            }
            if ($selectedTotal < 1) {
                throw ValidationException::withMessages([
                    'menu_selections' => 'Select at least one dish for a custom catering request.',
                ]);
            }
            $data['package_preference'] = 'Option 3 - Customize Your Own Package';
            $data['menu_selections'] = ['selections' => $selections, 'details' => $details];
            $data['package_price_per_pax'] = null;
            $data['estimated_total'] = null;
            unset($data['catering_package'], $data['packed_meal_package'], $data['packed_meal_side'], $data['tray_items'], $data['submission_token']);
        } else {
            unset($data['catering_package'], $data['packed_meal_package'], $data['packed_meal_side'], $data['menu_selections'], $data['tray_items'], $data['submission_token']);
        }

        if ($data['event_type'] !== 'Other') {
            $data['event_type_details'] = null;
        }

        $data['campaign_source'] = ($data['campaign_source'] ?? null) ?: 'direct';
        $data['branch_id'] = Branch::query()
            ->where(function ($query) {
                $query->where('name', 'like', '%MixEat Commissary%')
                    ->orWhere('address', 'like', '%Yap Fraternity%');
            })
            ->orderByRaw('CASE WHEN name LIKE \'%MixEat Commissary%\' THEN 0 ELSE 1 END')
            ->value('id');
        $data['status'] = $isCustom ? 'Pending Quotation' : 'Awaiting Deposit Verification';
        $data['payment_status'] = $isCustom ? 'Unpaid' : 'Pending Verification';
        $data['user_id'] = $authenticatedCustomer?->role === 'customer' ? $authenticatedCustomer->id : null;
        $paymentConfirmation = DB::transaction(function () use ($data, $trayItems, $isPartyTrays, $paymentReference, $isCustom): array {
            $booking = CateringBooking::create($data);
            if ($isPartyTrays) {
                $booking->trayItems()->createMany($trayItems);
            }

            if ($isCustom) {
                return [
                    'booking_id' => $booking->id,
                    'booking_reference' => sprintf('MXC-%s-%06d', $booking->created_at->format('Y'), $booking->id),
                ];
            }

            $totalCents = (int) round(((float) $booking->estimated_total) * 100);
            $depositCents = intdiv(($totalCents * 30) + 50, 100);
            $depositAmount = number_format($depositCents / 100, 2, '.', '');
            $duplicateReference = CateringPayment::query()
                ->whereRaw('LOWER(payment_reference) = ?', [strtolower($paymentReference)])
                ->exists();

            if ($duplicateReference) {
                CateringPayment::query()
                    ->whereRaw('LOWER(payment_reference) = ?', [strtolower($paymentReference)])
                    ->update(['duplicate_reference' => true]);
            }

            $booking->payments()->create([
                'payment_type' => 'deposit',
                'payment_method' => 'Security Bank QR',
                'amount' => $depositAmount,
                'payment_reference' => $paymentReference,
                'status' => 'Pending Verification',
                'duplicate_reference' => $duplicateReference,
                'submitted_at' => now(),
            ]);

            return [
                'booking_id' => $booking->id,
                'booking_reference' => sprintf('MXC-%s-%06d', $booking->created_at->format('Y'), $booking->id),
                'estimated_total' => number_format($totalCents / 100, 2),
                'deposit_amount' => number_format($depositCents / 100, 2),
                'remaining_balance' => number_format(($totalCents - $depositCents) / 100, 2),
            ];
        });

        $notifications->send(
            CateringBooking::findOrFail($paymentConfirmation['booking_id']),
            'inquiry_submitted'
        );

        return redirect()->to(route('catering').'#catering-inquiry')
            ->with('catering_status', $isCustom
                ? 'Your customized catering request has been submitted successfully. Our MixEat Commissary team will review your selected menu and provide a quotation.'
                : 'Your inquiry and deposit details were received. The booking is not confirmed until our team verifies your payment.')
            ->with('catering_payment_confirmation', $paymentConfirmation);
    }
}
