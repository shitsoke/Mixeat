@forelse($orders as $order)
    <tr data-order-id="{{ $order->id }}" data-order-status="{{ $order->status }}">
        <td class="py-4 px-4 order-number">{{ $order->order_number }}</td>
        <td class="py-4 px-4 order-type">
            @if ($order->user?->name)
                <p class="order-customer">{{ $order->user->name }}</p>
            @endif
            <p>{{ $order->type }}</p>
            @if ($order->type === 'Pickup' && $order->pickup_at)
                <p class="mt-1 text-xs order-muted">Pickup: {{ $order->pickup_at->format('M j, Y g:i A') }}</p>
            @elseif ($order->type === 'Delivery')
                <p class="mt-1 text-xs order-muted">{{ $order->delivery_method }}</p>
                @if ($order->delivery_address)
                    <p class="mt-1 max-w-xs text-xs order-muted">{{ $order->delivery_address }}</p>
                @endif
            @endif
            @if ($order->payment_method)
                <p class="mt-1 text-xs order-muted">Payment: {{ $order->payment_method }}</p>
            @endif
            @if ($order->gcash_reference_number)
                <p class="mt-1 text-xs order-muted">GCash reference: {{ $order->gcash_reference_number }}</p>
            @endif
        </td>
        <td class="py-4 px-4 order-total">&#8369;{{ number_format($order->total, 2) }}</td>
        <td class="py-4 px-4 text-center">
            @if($order->status === 'Pending')
                <span class="inline-block rounded-full bg-[#FFF3CD] px-3 py-1 text-xs font-bold text-[#856404]">Pending</span>
            @elseif($order->status === 'Preparing')
                <span class="inline-block rounded-full bg-[#E6F4EA] px-3 py-1 text-xs font-bold text-[#1E8E3E]">Preparing</span>
            @elseif($order->status === 'Ready for Pickup')
                <span class="inline-block rounded-full bg-[#CCE5FF] px-3 py-1 text-xs font-bold text-[#004085]">Ready for Pickup</span>
            @elseif($order->status === 'Completed')
                <span class="inline-block rounded-full bg-[#D4EDDA] px-3 py-1 text-xs font-bold text-[#155724]">Completed</span>
            @elseif($order->status === 'Declined')
                <span class="inline-block rounded-full bg-[#FCE8E6] px-3 py-1 text-xs font-bold text-[#D93025]">Declined</span>
            @else
                <span class="inline-block rounded-full bg-gray-100 px-3 py-1 text-xs font-bold text-gray-700">{{ $order->status }}</span>
            @endif
        </td>
        <td class="py-4 px-4 text-right">
            @if($order->status === 'Pending')
                <form action="{{ route('admin.supervisors.orders.accept', $order->id) }}" method="POST" class="inline-block me-1" data-live-order-action data-auto-receipt="true">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="rounded-full bg-[#28A745] px-4 py-1.5 text-xs font-bold text-white transition hover:bg-[#218838]">Accept</button>
                </form>
                <form action="{{ route('admin.supervisors.orders.decline', $order->id) }}" method="POST" class="inline-block" data-live-order-action>
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="rounded-full border border-[#DC3545] px-4 py-1.5 text-xs font-bold text-[#DC3545] transition hover:bg-red-50">Decline</button>
                </form>
            @elseif(in_array($order->status, ['Preparing', 'Ready for Pickup'], true))
                <a href="{{ route('admin.supervisors.orders.receipt', ['order' => $order->id, 'copy' => 'customer', 'paper' => 80, 'auto' => 1, 'reprint' => 1]) }}" target="_blank" rel="noopener" data-receipt-print class="inline-block rounded-full border border-[#FBAB18] px-3 py-1.5 text-xs font-bold text-[#FBAB18] transition hover:bg-[#FBAB18] hover:text-[#111111]">Print Receipt</a>
                @if($order->type === 'Delivery')
                    <a href="{{ route('admin.supervisors.orders.receipt', ['order' => $order->id, 'copy' => 'rider', 'paper' => 80, 'auto' => 1, 'reprint' => 1]) }}" target="_blank" rel="noopener" data-receipt-print class="inline-block rounded-full border border-[#777777] px-3 py-1.5 text-xs font-bold text-[#FFFFFF] transition hover:bg-[#333333]">Rider Copy</a>
                @endif
                @if($order->status === 'Preparing')
                    <form action="{{ route('admin.supervisors.orders.ready', $order->id) }}" method="POST" class="inline-block">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="rounded-full bg-[#FFC60A] px-4 py-1.5 text-xs font-bold text-[#090909] shadow-sm transition hover:bg-[#E5A900]">Out for Pickup</button>
                    </form>
                @else
                    <form action="{{ route('admin.supervisors.orders.complete', $order->id) }}" method="POST" class="inline-block">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="rounded-full border border-[#28A745] px-4 py-1.5 text-xs font-bold text-[#28A745] transition hover:bg-green-50">Complete Order</button>
                    </form>
                @endif
            @else
                <span class="text-xs text-[#777777]">No actions</span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="py-6 text-center text-sm font-medium text-[#B8B8B8]">No active orders for this branch.</td>
    </tr>
@endforelse
