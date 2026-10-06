<?php

namespace App\Services;

use App\Models\SiteSetting;
use App\Models\CateringPartner;

class CateringContent
{
    public function get(): array
    {
        $settings = SiteSetting::query()
            ->whereIn('key', ['catering_packages', 'catering_banner_title', 'catering_banner_copy', 'catering_highlights'])
            ->pluck('value', 'key');
        $partners = CateringPartner::query()
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (CateringPartner $partner) => [
                'name' => $partner->name,
                'logo_url' => $partner->publicLogoUrl(),
                'website_url' => $partner->website_url,
            ])
            ->all();

        return [
            'packages' => $this->decodeList($settings->get('catering_packages'), [
                ['name' => 'Packed Meal Boxes', 'price' => null, 'description' => 'Individually packed meals for teams, meetings, and gatherings.'],
                ['name' => 'Party Tray Spread', 'price' => null, 'description' => 'Shareable MixEat favorites for parties and family celebrations.'],
                ['name' => 'Full Buffet Service', 'price' => null, 'description' => 'A complete buffet experience with delivery and event setup.'],
            ]),
            'partners' => $partners,
            'banner_title' => $settings->get('catering_banner_title', 'Seasonal Catering'),
            'banner_copy' => $settings->get('catering_banner_copy', 'Make your next gathering a little more delicious with MixEat.'),
            'highlights' => $this->decodeList($settings->get('catering_highlights'), [
                'Packed Meals & Meal Boxes',
                'Party Trays & Buffet Setups',
                'On-Site Event Delivery',
            ]),
        ];
    }

    private function decodeList(?string $value, array $fallback): array
    {
        $decoded = $value ? json_decode($value, true) : null;

        return is_array($decoded) ? $decoded : $fallback;
    }
}
