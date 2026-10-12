@extends('layouts.app')

@section('title', 'Manage Promotions | MixEat')

@section('content')
    <section class="py-12">
        <div class="mixeat-shell px-4">
            <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#E5A900]">Marketing</p>
                    <h1 class="mt-2 text-4xl font-black text-[#090909]">Manage Promotions</h1>
                    <p class="mt-3 text-[#555555]">Create and manage the offers shown on the Featured page.</p>
                </div>
                <a href="{{ route('admin.promotions.create') }}" class="inline-flex justify-center rounded-full bg-[#FFC60A] px-6 py-3 font-semibold text-[#090909] hover:bg-[#E5A900]">Post Promotion</a>
            </div>

            @if (session('admin_status'))
                <div class="mb-6 rounded-2xl bg-[#FFF4BF] px-4 py-3 font-semibold text-[#090909]" role="status">{{ session('admin_status') }}</div>
            @endif

            <div class="mb-6">
                <a href="{{ route('admin.promotion') }}" class="text-sm font-semibold text-[#E5A900] underline">Manage homepage banner</a>
            </div>

            @if ($promotions->isEmpty())
                <div class="rounded-[28px] border border-[#E8DFAF] bg-white p-8 text-center text-[#555555]">
                    No promotions have been posted yet.
                </div>
            @else
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($promotions as $promotion)
                        <article class="overflow-hidden rounded-[24px] border border-[#E8DFAF] bg-white shadow-sm">
                            <img src="{{ $promotion->image ? asset('storage/'.$promotion->image) : asset('images/mixeat.jpg') }}" alt="{{ $promotion->title }}" class="aspect-[4/3] w-full object-cover">
                            <div class="p-5">
                                <div class="flex items-center justify-between gap-3">
                                    <h2 class="font-bold text-[#090909]">{{ $promotion->title }}</h2>
                                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $promotion->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $promotion->is_active ? 'Published' : 'Hidden' }}
                                    </span>
                                </div>
                                <p class="mt-2 line-clamp-3 text-sm text-[#555555]">{{ $promotion->description }}</p>
                                <div class="mt-5 flex items-center justify-between gap-3">
                                    <a href="{{ route('admin.promotions.edit', $promotion) }}" class="font-semibold text-[#A87900] underline">Edit</a>
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
