<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CateringMenuItem extends Model
{
    use SoftDeletes;

    public const CATEGORIES = [
        'meat' => 'Meat Dishes',
        'vegetables' => 'Vegetables',
        'rice' => 'Rice',
        'pasta' => 'Noodles',
        'soup' => 'Soup',
        'dessert' => 'Desserts',
        'drinks' => 'Drinks',
    ];

    protected $fillable = [
        'name',
        'description',
        'category',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function imageUrl(): ?string
    {
        return $this->image ? asset('storage/'.$this->image) : null;
    }
}
