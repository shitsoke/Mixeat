@extends('layouts.app')

@section('title', 'Menu | MixEat')

@section('content')
    <section class="py-14">
        <div class="mixeat-shell px-4">
            <div class="mb-8 text-center md:text-left">
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FFC60A]">Our Menu</p>
                <h1 class="mt-2 text-4xl font-black text-[#090909]">Fresh picks for every craving.</h1>
            </div>

            <div class="grid gap-8 lg:grid-cols-[280px_minmax(0,1fr)]">
                <aside class="rounded-[30px] border border-[#E8DFAF] bg-white p-5 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                    <form action="{{ route('menu') }}" method="GET">
                    <div class="mb-6">
                        <label class="mb-2 block text-sm font-bold uppercase tracking-[0.12em] text-[#555555]">Search</label>
                        <div class="rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3">
                            <input type="search" name="q" value="{{ $filters['search'] }}" placeholder="Search products" class="w-full bg-transparent text-sm text-[#090909] placeholder:text-[#999] focus:outline-none" />
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="mb-3 text-sm font-bold uppercase tracking-[0.12em] text-[#555555]">Categories</h3>
                        <div class="space-y-2">
                            @foreach($categories as $category)
                                <a href="{{ route('menu', array_merge(request()->query(), ['category' => $category])) }}" class="flex w-full items-center justify-between rounded-2xl px-3 py-2 text-left text-sm font-medium {{ $filters['category'] === $category ? 'bg-[#FFF4BF] text-[#FFC60A]' : 'text-[#090909] hover:bg-[#FFF9E6]' }}">
                                    <span>{{ $category }}</span>
                                    @if($loop->first)
                                        <span class="rounded-full bg-[#FFC60A] px-2 py-0.5 text-[10px] font-bold text-[#090909]">12</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="mb-2 block text-sm font-bold uppercase tracking-[0.12em] text-[#555555]">Price</label>
                            <select name="price" class="w-full rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm text-[#090909] focus:outline-none">
                                <option value="any" @selected($filters['price'] === 'any')>Any price</option>
                                <option value="under-100" @selected($filters['price'] === 'under-100')>Under &#8369;100</option>
                                <option value="100-200" @selected($filters['price'] === '100-200')>&#8369;100 - &#8369;200</option>
                                <option value="200-300" @selected($filters['price'] === '200-300')>&#8369;200 - &#8369;300</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-bold uppercase tracking-[0.12em] text-[#555555]">Availability</label>
                            <select name="availability" class="w-full rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm text-[#090909] focus:outline-none">
                                <option value="all" @selected($filters['availability'] === 'all')>All items</option>
                                <option value="available" @selected($filters['availability'] === 'available')>Available now</option>
                                <option value="sold-out" @selected($filters['availability'] === 'sold-out')>Sold out</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-bold uppercase tracking-[0.12em] text-[#555555]">Sort by</label>
                            <select name="sort" class="w-full rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm text-[#090909] focus:outline-none">
                                <option value="featured" @selected($filters['sort'] === 'featured')>Featured</option>
                                <option value="price-low" @selected($filters['sort'] === 'price-low')>Price: Low to High</option>
                                <option value="price-high" @selected($filters['sort'] === 'price-high')>Price: High to Low</option>
                                <option value="newest" @selected($filters['sort'] === 'newest')>Newest</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full rounded-full bg-[#FFC60A] px-5 py-3 text-sm font-semibold text-[#090909] transition hover:bg-[#E5A900]">Apply Filters</button>
                    </div>
                    </form>
                </aside>

                <div>
                    <div class="mb-6 flex flex-col gap-4 rounded-[28px] border border-[#E8DFAF] bg-white p-4 shadow-[0_14px_40px_rgba(229,169,0,0.10)] md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-[#555555]">Showing {{ count($products) }} products</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-semibold text-[#555555]">Filter:</span>
                            <a href="{{ route('menu') }}" class="rounded-full bg-[#FFF4BF] px-4 py-2 text-sm font-semibold text-[#FFC60A]">All</a>
                            <a href="{{ route('menu', array_merge(request()->query(), ['category' => 'Meals'])) }}" class="rounded-full border border-[#E8DFAF] px-4 py-2 text-sm font-semibold text-[#555555]">Meals</a>
                        </div>
                    </div>

                    <div class="product-grid">
                        @foreach ($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


