<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobOpening extends Model
{
    protected $fillable = ['title', 'location', 'employment_type', 'is_active'];

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }
}
