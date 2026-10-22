@extends('layouts.app')

@section('title', 'Careers')

@section('content')
<section class="py-14">
    <div class="mixeat-shell px-4 text-center">
        <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">CAREER OPPORTUNITIES</p>
        <h1 class="mt-3 text-4xl font-black text-[#FFFFFF]">Join The MixEat Team</h1>
        <p class="mt-3 text-lg text-[#B8B8B8]">Grow your career in a fast-growing food service & event catering company.</p>

        @if(session('success'))
            <div class="mt-6 rounded-xl border border-[#333333] bg-[#1E1D1B] p-4 text-[#FBAB18]">{{ session('success') }}</div>
        @endif

        <div class="mt-10 space-y-4">
            @forelse($jobs as $job)
                <div class="flex items-center justify-between gap-4 rounded-2xl border border-[#333333] bg-[#1E1D1B] p-6">
                    <div class="text-left">
                        <h2 class="text-xl font-bold text-[#FFFFFF]">{{ $job->title }}</h2>
                        <div class="mt-2 flex items-center gap-2 text-sm text-[#B8B8B8]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#FBAB18]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21s-6-4.35-6-10a6 6 0 1112 0c0 5.65-6 10-6 10z"/><circle cx="12" cy="11" r="2"/></svg>
                            {{ $job->location }}
                        </div>
                        <p class="mt-1 text-sm text-[#B8B8B8]">{{ $job->employment_type }}</p>
                    </div>
                    <a href="{{ route('careers.apply.form', $job) }}" class="rounded-full bg-[#FBAB18] px-5 py-2 text-sm font-bold text-[#111111]">APPLY NOW</a>
                </div>
            @empty
                <p class="text-[#B8B8B8]">No open positions at the moment.</p>
            @endforelse
        </div>
    </div>
</section>
@endsection
