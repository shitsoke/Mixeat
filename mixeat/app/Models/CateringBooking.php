<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CateringBooking extends Model
{
    protected $fillable = [
        'client_name',
        'email',
        'phone',
        'company_name',
        'event_date',
        'event_time',
        'venue',
        'event_type',
        'service_type',
        'package_preference',
        'guest_count',
        'budget_range',
        'special_requests',
        'campaign_source',
        'status',
        'payment_status',
        'quote_amount',
        'branch_id',
        'assigned_staff_id',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'quote_amount' => 'decimal:2',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }
}
