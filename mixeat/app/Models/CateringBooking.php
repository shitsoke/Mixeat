<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CateringBooking extends Model
{
    protected $fillable = [
        'client_name',
        'user_id',
        'email',
        'phone',
        'company_name',
        'event_date',
        'event_time',
        'venue',
        'event_type',
        'event_type_details',
        'service_type',
        'package_preference',
        'package_price_per_pax',
        'menu_selections',
        'estimated_total',
        'submission_token',
        'guest_count',
        'budget_range',
        'special_requests',
        'campaign_source',
        'status',
        'payment_status',
        'quote_amount',
        'branch_id',
        'assigned_staff_id',
        'quotation_status',
        'quotation_version',
        'quotation_notes',
        'quotation_sent_at',
        'quotation_accepted_at',
        'quotation_declined_at',
        'quotation_sent_by',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'quote_amount' => 'decimal:2',
            'menu_selections' => 'array',
            'package_price_per_pax' => 'decimal:2',
            'estimated_total' => 'decimal:2',
            'quotation_sent_at' => 'datetime',
            'quotation_accepted_at' => 'datetime',
            'quotation_declined_at' => 'datetime',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }

    public function trayItems(): HasMany
    {
        return $this->hasMany(CateringTrayItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(CateringPayment::class, 'catering_booking_id');
    }

    public function quotationVersions(): HasMany
    {
        return $this->hasMany(CateringQuotationVersion::class, 'catering_booking_id');
    }
}
