@extends('layouts.app')

@section('title', 'MixEat | Good Food. Great Moments.')

@section('content')
    <section class="bg-[radial-gradient(circle_at_top_left,_rgba(229,169,0,0.12),_transparent_30%),linear-gradient(135deg,_#FFF9E6_0%,_#FFF9E6_100%)] py-12 md:py-20">
        <div class="mixeat-shell grid items-center gap-10 px-4 md:grid-cols-2">
            <div>
                <span class="inline-flex rounded-full border border-[#E8DFAF] bg-[#FFF4BF] px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-[#FFC60A]">Fresh from our kitchen</span>
                <h1 class="mt-6 text-4xl font-black leading-tight text-[#090909] md:text-5xl lg:text-6xl">
                    Good Food. Great Moments. <span class="text-[#FFC60A]">MixEat.</span>
                </h1>
                <p class="mt-5 max-w-xl text-lg leading-8 text-[#555555]">
                    Order your favorites online and enjoy delicious meals from your nearest MixEat branch.
                </p>
                <div class="mt-8 flex flex-col gap-4 sm:flex-row">
                    <a href="{{ route('menu') }}" class="inline-flex items-center justify-center rounded-full bg-[#FFC60A] px-7 py-4 text-base font-semibold text-[#090909] shadow-lg shadow-[#E5A900]/25 transition hover:bg-[#E5A900]">
                        Order Now
                    </a>
                    <a href="{{ route('featured') }}" class="inline-flex items-center justify-center rounded-full border border-[#FFC60A] bg-white px-7 py-4 text-base font-semibold text-[#FFC60A] transition hover:bg-[#FFF4BF]">
                        View Menu
                    </a>
                </div>
            </div>

            <div class="relative">
                <div class="absolute -left-7 top-10 h-28 w-28 rounded-full bg-[#FFC60A]/20 blur-2xl"></div>
                <div class="absolute -right-4 bottom-10 h-32 w-32 rounded-full bg-[#E5A900]/20 blur-2xl"></div>
                
                <div class="relative overflow-hidden rounded-[32px] border border-[#E8DFAF] bg-white p-4 shadow-[0_20px_70px_rgba(229,169,0,0.18)]">
                    <!-- Edge-to-Edge Image Box -->
                    <div class="overflow-hidden rounded-[24px]">
                        <img src="{{ $promotion['image'] }}" alt="{{ $promotion['title'] }}" class="w-full h-auto object-cover">
                    </div>
                    
                    <!-- Information Bar Placed Directly Below Image (Desktop & Mobile) -->
                    <div class="mt-4 rounded-[20px] border border-[#E8DFAF]/80 bg-[#FFF9E6] p-4 shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                @if(!empty($promotion['label']))
                                    <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-[#E5A900]">{{ $promotion['label'] }}</p>
                                @endif
                                <h3 class="mt-0.5 text-lg font-bold text-[#090909]">{{ $promotion['title'] }}</h3>
                            </div>
                            
                            @if(!empty($promotion['price']) && is_numeric($promotion['price']) && (float) $promotion['price'] > 0)
                                <div class="text-xl font-black text-[#FFC60A]">&#8369;{{ number_format((float) $promotion['price'], 2) }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="border-y border-[#E8DFAF] bg-white py-10">
        <div class="mixeat-shell px-4">
            <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FFC60A]">Order from your nearest branch</p>
                    <h2 class="mt-2 text-3xl font-black text-[#090909]">Choose your branch</h2>
                </div>
                <a href="{{ route('branches') }}" class="text-sm font-semibold text-[#E5A900]">View all branches</a>
            </div>

            <form action="{{ route('branches.select-dropdown') }}" method="POST" class="flex flex-col gap-4 rounded-[24px] border border-[#E8DFAF] bg-[#FFF9E6] p-5 sm:flex-row sm:items-end">
                @csrf
                <label class="grid flex-1 gap-2 text-sm font-bold text-[#090909]" for="home-branch">Your branch
                    <select id="home-branch" name="branch" class="min-w-0 w-full rounded-full border border-[#E8DFAF] bg-white px-5 py-3 text-base font-semibold text-[#090909] focus:border-[#FFC60A] focus:outline-none focus:ring-2 focus:ring-[#FFC60A]/30">
                        @foreach ($branches as $index => $branch)
                            <option value="{{ $index }}" @selected($selectedBranch === $index)>{{ $branch['name'] }} - {{ $branch['address'] }}</option>
                        @endforeach
                    </select>
                </label>
                <button type="submit" class="rounded-full bg-[#FFC60A] px-6 py-3 font-semibold text-[#090909] shadow-lg shadow-[#E5A900]/25 transition hover:bg-[#E5A900]">Select Branch</button>
            </form>
        </div>
    </section>

    <section class="py-16 md:py-20">
        <div class="mixeat-shell px-4">
            <div class="mb-8 flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FFC60A]">Featured Products</p>
                    <h2 class="mt-2 text-3xl font-black text-[#090909]">Try our customers' favorites.</h2>
                </div>
                <a href="{{ route('menu') }}" class="hidden rounded-full border border-[#FFC60A] px-5 py-3 text-sm font-semibold text-[#FFC60A] transition hover:bg-[#FFF4BF] md:inline-flex">Browse All</a>
            </div>

            <div class="mb-10 overflow-x-auto pb-2 md:overflow-visible">
                <div class="flex min-w-max gap-3 md:flex-wrap md:justify-start">
                    @foreach($categories as $category)
                        <a href="{{ route('menu', ['category' => $category]) }}" class="rounded-full border border-[#E8DFAF] bg-white px-4 py-2 text-sm font-semibold text-[#555555] transition hover:border-[#FFC60A] hover:bg-[#FFF4BF] hover:text-[#FFC60A] {{ $loop->first ? 'border-[#FFC60A] bg-[#FFF4BF] text-[#FFC60A]' : '' }}">
                            {{ $category }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="product-grid">
                @foreach ($featuredProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </div>
    </section>
@endsection