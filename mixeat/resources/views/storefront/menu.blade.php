@extends('layouts.app')

@section('title', 'Menu | MixEat')

@section('content')
    <section class="py-14">
        <div class="mixeat-shell px-4">
            <div class="mb-8 text-center">
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">Our Menu</p>
                <h1 class="mt-2 text-4xl font-black text-white">Rice Bowl Menu</h1>
                <p class="mt-2 text-[#B8B8B8]">Pick from our chef signature bowls or build your customized flavor combination!</p>
            </div>

            <div class="mb-6 flex flex-wrap justify-center gap-3">
                @foreach (['All Bowls','Chicken','Pork','Beef','Fish','Best Seller'] as $cat)
                    @php($active = ($filters['category'] ?? 'All') === $cat || (($filters['category'] ?? '') === 'Food Bowls' && $cat === 'All Bowls'))
                    <a href="{{ route('menu', ['category' => $cat]) }}" class="rounded-full px-5 py-2 text-sm font-semibold {{ $active ? 'bg-[#FBAB18] text-[#111111]' : 'bg-[#201D19] text-white border border-[#333333]' }}">{{ $cat }}</a>
                @endforeach
            </div>

            <div>
                @if (count($products))
                    <div class="product-grid">
                        @foreach ($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>
                @else
                    <div class="rounded-[28px] border border-[#333333] bg-[#201D19] p-8 text-center">
                        <h2 class="text-xl font-bold text-white">No products found</h2>
                        <p class="mt-2 text-sm text-[#B8B8B8]">Try another category or adjust your filters.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection