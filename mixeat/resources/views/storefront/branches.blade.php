@extends('layouts.app')

@section('title', 'Choose Your MixEat Branch')

@section('content')
    <section class="py-14">
        <div class="mixeat-shell px-4">
            <div class="mb-8 text-center">
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FFC60A]">Pick a branch</p>
                <h1 class="mt-2 text-4xl font-black text-[#090909]">Choose Your MixEat Branch</h1>
                <p class="mt-3 text-lg text-[#555555]">Select the branch closest to you.</p>
            </div>

            <div class="mb-8 flex justify-center">
                <button type="button" class="inline-flex items-center gap-2 rounded-full bg-[#FFC60A] px-6 py-3.5 text-base font-semibold text-[#090909] shadow-lg shadow-[#E5A900]/25 transition hover:bg-[#E5A900]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 20s-7-4.35-7-10A4 4 0 0112 9a4 4 0 014-3 4 4 0 013 7c0 5.65-7 10-7 10z" />
                    </svg>
                    Use My Location
                </button>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                @foreach ($branches as $index => $branch)
                    <div class="rounded-[30px] border {{ $selectedBranch === $index ? 'border-[#FFC60A] ring-2 ring-[#FFC60A]/20' : 'border-[#E8DFAF]' }} bg-white p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <h2 class="text-2xl font-black text-[#090909]">{{ $branch['name'] }}</h2>
                            <span class="rounded-full bg-[#FFF4BF] px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-[#FFC60A]">Nearest</span>
                        </div>
                        <div class="space-y-3 text-[#555555]">
                            <p><span class="font-semibold text-[#090909]">Address:</span> {{ $branch['address'] }}</p>
                            <p><span class="font-semibold text-[#090909]">Opening Hours:</span> {{ $branch['opening_hours'] }}</p>
                            <p><span class="font-semibold text-[#090909]">Distance:</span> {{ $branch['distance'] }}</p>
                        </div>
                        <form action="{{ route('branches.select', ['branch' => $index]) }}" method="POST" class="mt-6">
                            @csrf
                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-[#FFC60A] px-5 py-3 text-base font-semibold text-[#090909] shadow-lg shadow-[#E5A900]/25 transition hover:bg-[#E5A900]">
                                {{ $selectedBranch === $index ? 'Selected Branch' : 'Select Branch' }}
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection


