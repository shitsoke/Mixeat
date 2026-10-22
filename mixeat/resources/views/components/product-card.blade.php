@props([
    'product',
    'compact' => false,
])

<div class="product-card group flex h-full min-w-0 flex-col overflow-hidden rounded-[28px] border border-[#333333] bg-[#201D19] shadow-[0_14px_40px_rgba(229,169,0,0.12)] transition duration-200 hover:-translate-y-1 hover:shadow-[0_20px_50px_rgba(229,169,0,0.16)]">
    <div class="product-image-frame relative overflow-hidden">
        <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" onerror="this.onerror=null; this.src='{{ asset('images/mixeat.jpg') }}';" class="object-cover transition duration-300 group-hover:scale-105">
        @if (!empty($product['badge']))
            <span class="absolute left-4 top-4 rounded-full bg-[#FBAB18] px-3 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-[#FFFFFF] shadow-md">
                {{ $product['badge'] }}
            </span>
        @endif
    </div>

    <div class="flex flex-1 flex-col space-y-4 p-5">
        <div class="flex min-w-0 items-start justify-between gap-4">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#FBAB18]">{{ $product['category'] }}</p>
                <h3 class="mt-2 break-words text-xl font-bold text-[#FFFFFF]">{{ $product['name'] }}</h3>
            </div>
            <div class="rounded-full bg-[#201D19] px-2.5 py-1 text-sm font-bold text-[#FBAB18]">
                @if($product['available'] ?? true)
                    Available
                @else
                    Sold Out
                @endif
            </div>
        </div>

        <p class="min-h-12 text-sm leading-6 text-[#B8B8B8]">{{ $product['description'] }}</p>

        <div class="mt-auto flex flex-wrap items-center justify-between gap-3 pt-2">
            <div class="text-2xl font-extrabold text-[#FBAB18]">&#8369;{{ number_format($product['price'], 2) }}</div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('product', ['id' => $product['id']]) }}" class="rounded-full border border-[#FBAB18] px-3 py-2 text-sm font-semibold text-[#FBAB18] transition hover:bg-[#201D19]">
                    View Details
                </a>
                @if ($product['available'] ?? true)
                    <form action="{{ route('cart.add', ['id' => $product['id']]) }}" method="POST">
                        @csrf
                        <button type="submit" class="rounded-full bg-[#FBAB18] px-4 py-2.5 text-sm font-semibold text-[#FFFFFF] shadow-lg shadow-[#FBAB18]/25 transition hover:bg-[#FBAB18]">
                            Add to Cart
                        </button>
                    </form>
                @else
                    <button type="button" disabled class="cursor-not-allowed rounded-full bg-[#333333] px-4 py-2.5 text-sm font-semibold text-[#B8B8B8]">
                        Sold Out
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>






