<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackedMealPackage extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'name', 'description', 'price', 'included_dishes', 'image', 'is_active'];

    protected $casts = ['price' => 'decimal:2', 'included_dishes' => 'array', 'is_active' => 'boolean'];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function imageUrl(): ?string
    {
        return $this->image ? asset('storage/'.$this->image) : null;
    }
}
