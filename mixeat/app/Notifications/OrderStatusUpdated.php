<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public Order $order, public string $previousStatus = '')
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $status = $this->order->status;
        $message = match ($status) {
            'Preparing' => "Your order {$this->order->order_number} has been accepted and is now being prepared.",
            'Ready for Pickup' => "Your order {$this->order->order_number} is ready for pickup at your selected MixEat branch.",
            'Completed' => "Your order {$this->order->order_number} has been completed. Thank you for choosing MixEat!",
            'Declined' => "Your order {$this->order->order_number} was declined. View your order details for more information.",
            'Cancelled' => "Your order {$this->order->order_number} has been cancelled.",
            default => "Your order {$this->order->order_number} status has been updated to {$status}.",
        };

        $orderId = (int) str_replace('MX-', '', $this->order->order_number);

        return [
            'type' => 'order_status',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'status' => $status,
            'previous_status' => $this->previousStatus,
            'branch_id' => $this->order->branch_id,
            'branch_name' => $this->order->branch?->name,
            'message' => $message,
            'order_url' => route('order-detail', $orderId),
        ];
    }
}
