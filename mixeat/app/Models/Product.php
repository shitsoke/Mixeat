<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $fillable = ['name', 'category', 'description', 'price', 'image', 'badge'];

    protected $casts = ['price' => 'decimal:2'];

    public function imageUrl(): string
    {
        if (! $this->image) {
            return asset('images/mixeat.jpg');
        }

        return filter_var($this->image, FILTER_VALIDATE_URL)
            ? $this->image
            : asset('storage/'.$this->image);
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class)->withPivot('available')->withTimestamps();
    }
}
