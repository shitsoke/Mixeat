<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CateringPayment extends Model
{
    protected $fillable = [
        'payment_type',
        'payment_method',
        'amount',
        'payment_reference',
        'status',
        'duplicate_reference',
        'submitted_at',
        'verified_amount',
        'verified_at',
        'verified_by',
        'collected_at',
        'collected_by',
        'verification_remarks',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'verified_amount' => 'decimal:2',
            'duplicate_reference' => 'boolean',
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
            'collected_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(CateringBooking::class, 'catering_booking_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function collectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }
}
