<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartyTray extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'full_price', 'half_price', 'sizes', 'dishes', 'image', 'is_active'];

    protected $casts = ['full_price' => 'decimal:2', 'half_price' => 'decimal:2', 'sizes' => 'array', 'dishes' => 'array', 'is_active' => 'boolean'];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function imageUrl(): ?string
    {
        return $this->image ? asset('storage/'.$this->image) : null;
    }
}
