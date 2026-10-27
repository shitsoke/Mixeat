<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_order',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'branch_id' => $this->order->branch_id,
            'branch_name' => $this->order->branch?->name,
            'total' => $this->order->total,
            'order_type' => $this->order->type,
            'message' => 'New Order ' . $this->order->order_number,
        ];
    }
}
