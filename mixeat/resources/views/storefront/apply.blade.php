@extends('layouts.app')

@section('title', 'Apply - ' . $job->title)

@section('content')
<section class="py-14">
    <div class="mixeat-shell px-4 max-w-2xl mx-auto">
        <h1 class="text-3xl font-black text-[#FFFFFF] text-center">Apply for {{ $job->title }}</h1>
        <form action="{{ route('careers.apply', $job) }}" method="POST" enctype="multipart/form-data" class="mt-8 space-y-4">
            @csrf
            <input type="text" name="full_name" placeholder="Full Name" required class="w-full rounded-xl border border-[#333333] bg-[#1E1D1B] p-3 text-white" />
            <input type="email" name="email" placeholder="Email Address" required class="w-full rounded-xl border border-[#333333] bg-[#1E1D1B] p-3 text-white" />
            <input type="text" name="contact_number" placeholder="Contact Number" required class="w-full rounded-xl border border-[#333333] bg-[#1E1D1B] p-3 text-white" />
            <div class="rounded-xl border border-[#333333] bg-[#1E1D1B] p-3 text-[#B8B8B8]">{{ $job->title }}</div>
            <input type="file" name="resume" accept=".pdf,.doc,.docx" required class="w-full text-white" />
            <textarea name="cover_letter" placeholder="Cover Letter (optional)" class="w-full rounded-xl border border-[#333333] bg-[#1E1D1B] p-3 text-white"></textarea>
            <button type="submit" class="w-full rounded-full bg-[#FBAB18] py-3 font-bold text-[#111111]">Submit Application</button>
        </form>
        @if($errors->any())
            <div class="mt-4 text-red-400">@foreach($errors->all() as $e){{ $e }}<br>@endforeach</div>
        @endif
    </div>
</section>
@endsection
