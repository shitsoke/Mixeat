@forelse($orders as $order)
    <tr>
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
                <form action="{{ route('admin.supervisors.orders.accept', $order->id) }}" method="POST" class="inline-block me-1">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="rounded-full bg-[#28A745] px-4 py-1.5 text-xs font-bold text-white transition hover:bg-[#218838]">Accept</button>
                </form>
                <form action="{{ route('admin.supervisors.orders.decline', $order->id) }}" method="POST" class="inline-block">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="rounded-full border border-[#DC3545] px-4 py-1.5 text-xs font-bold text-[#DC3545] transition hover:bg-red-50">Decline</button>
                </form>
            @elseif($order->status === 'Preparing')
                <form action="{{ route('admin.supervisors.orders.ready', $order->id) }}" method="POST" class="inline-block">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="rounded-full bg-[#FFC60A] px-4 py-1.5 text-xs font-bold text-[#090909] shadow-sm transition hover:bg-[#E5A900]">Out for Pickup</button>
                </form>
            @elseif($order->status === 'Ready for Pickup')
                <form action="{{ route('admin.supervisors.orders.complete', $order->id) }}" method="POST" class="inline-block">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="rounded-full border border-[#28A745] px-4 py-1.5 text-xs font-bold text-[#28A745] transition hover:bg-green-50">Complete Order</button>
                </form>
            @else
                <span class="text-xs text-[#777777]">No actions</span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="py-6 text-center text-sm font-medium text-[#B8B8B8]">No orders logged for this branch.</td>
    </tr>
@endforelse
