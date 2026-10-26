<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    public function __construct(public Order )
    {
    }

    public function via(object ): array
    {
        return ['database'];
    }

    public function toArray(object ): array
    {
        return [
            'type' => 'new_order',
            'order_id' => ->order->id,
            'order_number' => ->order->order_number,
            'branch_id' => ->order->branch_id,
            'branch_name' => ->order->branch?->name,
            'total' => ->order->total,
            'order_type' => ->order->type,
            'message' => 'New order received!',
        ];
    }
}
