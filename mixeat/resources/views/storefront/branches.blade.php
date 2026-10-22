@extends('layouts.app')

@section('title', 'Choose Your MixEat Branch')

@section('content')
    <section class="py-14">
        <div class="mixeat-shell px-4">
            <div class="mb-8 text-center">
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">Pick a branch</p>
                <h1 class="mt-2 text-4xl font-black text-[#FFFFFF]">Choose Your MixEat Branch</h1>
                <p class="mt-3 text-lg text-[#B8B8B8]">Select the branch closest to you.</p>
            </div>


            <div class="grid gap-6 lg:grid-cols-3">
                @foreach ($branches as $index => $branch)
                    <div class="rounded-[30px] border {{ $selectedBranch === $index ? 'border-[#FBAB18] ring-2 ring-[#FBAB18]/20' : 'border-[#333333]' }} bg-[#111111] p-6 shadow-[0_14px_40px_rgba(229,169,0,0.10)]">
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <h2 class="text-2xl font-black text-[#FFFFFF]">{{ $branch['name'] }}</h2>
                        </div>
                        <div class="space-y-3 text-[#B8B8B8]">
                            <p><span class="font-semibold text-[#FFFFFF]">Address:</span> {{ $branch['address'] }}</p>
                            <p><span class="font-semibold text-[#FFFFFF]">Opening Hours:</span> {{ $branch['opening_hours'] }}</p>
                            @if(!empty($branch['telephone_number']))<p><span class="font-semibold text-[#FFFFFF]">Telephone:</span> {{ $branch['telephone_number'] }}</p>@endif
                        </div>
                        <form action="{{ route('branches.select', ['branch' => $index]) }}" method="POST" class="mt-6">
                            @csrf
                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-[#FBAB18] px-5 py-3 text-base font-semibold text-[#FFFFFF] shadow-lg shadow-[#FBAB18]/25 transition hover:bg-[#FBAB18]">
                                {{ $selectedBranch === $index ? 'Selected Branch' : 'Select Branch' }}
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection




