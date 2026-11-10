<?php

namespace App\Services;

use Carbon\Carbon;

class OrderingHours
{
    public function isOpen(): bool
    {
        return $this->status()['open'];
    }

    public function status(): array
    {
        $timezone = (string) config('ordering.timezone');
        $now = Carbon::now($timezone);
        $opensAt = $now->copy()->setTimeFromTimeString((string) config('ordering.opens_at'));
        $closesAt = $now->copy()->setTimeFromTimeString((string) config('ordering.closes_at'));
        $isOpen = $now->greaterThanOrEqualTo($opensAt) && $now->lessThan($closesAt);

        if ($isOpen) {
            $nextTransition = $closesAt;
            $nextOpening = $opensAt->copy()->addDay();
        } elseif ($now->lessThan($opensAt)) {
            $nextTransition = $opensAt;
            $nextOpening = $opensAt;
        } else {
            $nextOpening = $opensAt->copy()->addDay();
            $nextTransition = $nextOpening;
        }

        $openingLabel = $opensAt->format('g:i A');
        $closingLabel = $closesAt->format('g:i A');

        return [
            'open' => $isOpen,
            'timezone' => $timezone,
            'label' => $isOpen
                ? "Online Ordering Open • Until {$closingLabel}"
                : 'Online Ordering Closed • Opens at '.$nextOpening->format('g:i A'),
            'message' => $isOpen ? null : (string) config('ordering.closed_message'),
            'next_opening_label' => $nextOpening->format('g:i A'),
            'next_transition_at' => $nextTransition->toIso8601String(),
            'next_transition_in_ms' => max(0, (int) $now->diffInMilliseconds($nextTransition, false)),
            'hours_label' => "{$openingLabel} to {$closingLabel} daily",
        ];
    }
}
