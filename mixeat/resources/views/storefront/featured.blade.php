@extends('layouts.app')

@section('title', 'Featured Promotions | MixEat')

@section('content')
    <section class="py-12 md:py-16">
        <div class="mixeat-shell px-4">
            <div class="mb-8">
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">MixEat offers</p>
                <h1 class="mt-2 text-4xl font-black text-[#FFFFFF]">Featured Promotions</h1>
                <p class="mt-3 max-w-2xl text-base leading-7 text-[#B8B8B8]">Explore the latest offers from MixEat.</p>
            </div>

            @if ($promotions->isEmpty())
                <div class="rounded-[28px] border border-[#333333] bg-[#1A1A1A] px-6 py-12 text-center">
                    <h2 class="text-xl font-bold text-[#FFFFFF]">No promotions right now</h2>
                    <p class="mt-2 text-sm text-[#B8B8B8]">Please check back soon for new MixEat offers.</p>
                </div>
            @else
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($promotions as $promotion)
                        <article class="overflow-hidden rounded-[24px] border border-[#333333] bg-[#201D19] shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                            <img src="{{ $promotion->image ? asset('storage/'.$promotion->image) : asset('images/mixeat.jpg') }}" alt="{{ $promotion->title }}" class="aspect-[4/3] w-full object-cover">
                            <div class="flex min-h-48 flex-col p-5">
                                @if ($promotion->label)
                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-[#FBAB18]">{{ $promotion->label }}</p>
                                @endif
                                <div class="mt-1 flex items-start justify-between gap-3">
                                    <h2 class="text-lg font-bold text-[#FFFFFF]">{{ $promotion->title }}</h2>
                                    @if ($promotion->price !== null)
                                        <span class="shrink-0 font-bold text-[#FBAB18]">&#8369;{{ number_format((float) $promotion->price, 2) }}</span>
                                    @endif
                                </div>
                                <p class="mt-2 flex-1 whitespace-pre-line text-sm leading-6 text-[#B8B8B8]">{{ \Illuminate\Support\Str::limit($promotion->description, 140) }}</p>
                                <a href="{{ route('featured.show', $promotion) }}" class="mt-4 inline-flex self-start rounded-full border border-[#FBAB18] px-5 py-2.5 text-sm font-semibold text-[#FBAB18] transition hover:bg-[#201D19]">
                                    View details
                                </a>
                                @if ($promotion->button_text && $promotion->button_url)
                                    <a href="{{ $promotion->button_url }}" class="mt-3 inline-flex self-start rounded-full bg-[#FBAB18] px-5 py-2.5 text-sm font-semibold text-[#FFFFFF] transition hover:bg-[#FBAB18]">
                                        {{ $promotion->button_text }}
                                    </a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection

