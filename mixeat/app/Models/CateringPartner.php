<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CateringPartner extends Model
{
    protected $fillable = [
        'name',
        'logo_path',
        'logo_url',
        'website_url',
        'is_visible',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function publicLogoUrl(): ?string
    {
        if ($this->logo_path) {
            return asset('storage/'.$this->logo_path);
        }

        return $this->logo_url;
    }
}
