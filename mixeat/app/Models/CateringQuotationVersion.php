<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CateringQuotationVersion extends Model
{
    protected $fillable = [
        'catering_booking_id',
        'version',
        'amount',
        'notes',
        'status',
        'sent_at',
        'accepted_at',
        'declined_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'sent_at' => 'datetime',
            'accepted_at' => 'datetime',
            'declined_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(CateringBooking::class, 'catering_booking_id');
    }
}
