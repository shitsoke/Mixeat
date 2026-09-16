@extends('layouts.app')

@section('title', 'Register | MixEat')

@section('content')
    <section class="py-16">
        <div class="mixeat-shell grid items-center justify-center px-4">
            <div class="w-full max-w-xl rounded-[32px] border border-[#E8DFAF] bg-white p-8 shadow-[0_20px_60px_rgba(229,169,0,0.12)]">
                <div class="mb-8 text-center">
                    <img src="{{ asset('images/mixeat.jpg') }}" alt="MixEat" class="mx-auto mb-4 h-16 w-16 rounded-2xl object-contain shadow-lg shadow-[#E5A900]/25" />
                    <h1 class="text-3xl font-black text-[#090909]">Create your account</h1>
                </div>

                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                        <p class="font-semibold">Please check the following:</p>
                        <ul class="mt-1 list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ url('/register') }}" method="POST" class="grid gap-5 md:grid-cols-2">
                    @csrf
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#555555]">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" class="w-full rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm text-[#090909] focus:outline-none" required />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#555555]">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" class="w-full rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm text-[#090909] focus:outline-none" required />
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-[#555555]">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm text-[#090909] focus:outline-none" required />
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-[#555555]">Phone Number</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" class="w-full rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm text-[#090909] focus:outline-none" required />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#555555]">Password</label>
                        <input type="password" name="password" class="w-full rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm text-[#090909] focus:outline-none" required />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#555555]">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="w-full rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm text-[#090909] focus:outline-none" required />
                    </div>

                    <div class="md:col-span-2">
                        <button type="submit" class="w-full rounded-full bg-[#FFC60A] px-6 py-3.5 text-base font-semibold text-[#090909] shadow-lg shadow-[#E5A900]/25 transition hover:bg-[#E5A900]">
                            Create Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection


