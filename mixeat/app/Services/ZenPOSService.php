<?php

namespace App\Services;

class ZenPOSService
{
    /**
     * Placeholder service for future ZenPOS integration.
     *
     * The real implementation will send customer orders to the MixEat branch POS
     * after the order is created in Laravel.
     */
    public function createOrder(array $order): array
    {
        return [
            'status' => 'pending',
            'external_reference' => $order['reference'] ?? null,
            'message' => 'ZenPOS integration is not implemented yet.',
        ];
    }
}
