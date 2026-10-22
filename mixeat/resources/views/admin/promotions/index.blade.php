@extends('layouts.app')

@section('title', 'Manage Promotions | MixEat')

@section('content')
    <section class="py-12">
        <div class="mixeat-shell px-4">
            <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">Marketing</p>
                    <h1 class="mt-2 text-4xl font-black text-[#FFFFFF]">Manage Promotions</h1>
                    <p class="mt-3 text-[#B8B8B8]">Create and manage the offers shown on the Featured page.</p>
                </div>
                <a href="{{ route('admin.promotions.create') }}" class="inline-flex justify-center rounded-full bg-[#FBAB18] px-6 py-3 font-semibold text-[#FFFFFF] hover:bg-[#FBAB18]">Post Promotion</a>
            </div>

            @if (session('admin_status'))
                <div class="mb-6 rounded-2xl bg-[#201D19] px-4 py-3 font-semibold text-[#FFFFFF]" role="status">{{ session('admin_status') }}</div>
            @endif

            <div class="mb-6">
                <a href="{{ route('admin.promotion') }}" class="text-sm font-semibold text-[#FBAB18] underline">Manage homepage banner</a>
            </div>

            @if ($promotions->isEmpty())
                <div class="rounded-[28px] border border-[#333333] bg-[#201D19] p-8 text-center text-[#B8B8B8]">
                    No promotions have been posted yet.
                </div>
            @else
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($promotions as $promotion)
                        <article class="overflow-hidden rounded-[24px] border border-[#333333] bg-[#201D19] shadow-sm">
                            <img src="{{ $promotion->image ? asset('storage/'.$promotion->image) : asset('images/mixeat.jpg') }}" alt="{{ $promotion->title }}" class="aspect-[4/3] w-full object-cover">
                            <div class="p-5">
                                <div class="flex items-center justify-between gap-3">
                                    <h2 class="font-bold text-[#FFFFFF]">{{ $promotion->title }}</h2>
                                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $promotion->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $promotion->is_active ? 'Published' : 'Hidden' }}
                                    </span>
                                </div>
                                <p class="mt-2 line-clamp-3 text-sm text-[#B8B8B8]">{{ $promotion->description }}</p>
                                <div class="mt-5 flex items-center justify-between gap-3">
                                    <a href="{{ route('admin.promotions.edit', $promotion) }}" class="font-semibold text-[#FBAB18] underline">Edit</a>
                                    <form action="{{ route('admin.promotions.destroy', $promotion) }}" method="POST" onsubmit="return confirm('Delete this promotion?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-semibold text-red-600 underline">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection

