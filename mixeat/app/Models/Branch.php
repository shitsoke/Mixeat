<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Branch extends Model
{
    protected $fillable = ['name', 'address', 'opening_hours', 'distance'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withPivot('available')->withTimestamps();
    }
}
