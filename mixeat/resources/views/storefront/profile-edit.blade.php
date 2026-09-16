@extends('layouts.app')

@section('title', 'Edit Profile | MixEat')

@section('content')
    <section class="py-14">
        <div class="mixeat-shell px-4">
            <div class="mx-auto max-w-2xl rounded-[32px] border border-[#E8DFAF] bg-white p-8 shadow-[0_20px_60px_rgba(229,169,0,0.12)]">
                <div class="mb-8">
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FFC60A]">Account Settings</p>
                    <h1 class="mt-2 text-4xl font-black text-[#090909]">Edit Profile</h1>
                    <p class="mt-3 text-[#555555]">Keep your MixEat contact information up to date.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-[#DC3545]/30 bg-red-50 px-4 py-3 text-sm text-[#DC3545]" role="alert">
                        <ul class="list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST" class="grid gap-5 md:grid-cols-2">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#555555]">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required class="w-full rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm text-[#090909] focus:outline-none" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#555555]">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required class="w-full rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm text-[#090909] focus:outline-none" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-[#555555]">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm text-[#090909] focus:outline-none" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-[#555555]">Phone Number</label>
                        <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" required class="w-full rounded-2xl border border-[#E8DFAF] bg-[#FFF9E6] px-4 py-3 text-sm text-[#090909] focus:outline-none" />
                    </div>
                    <div class="flex flex-col gap-3 pt-2 sm:col-span-2 sm:flex-row sm:justify-end">
                        <a href="{{ route('profile') }}" class="inline-flex items-center justify-center rounded-full border border-[#FFC60A] bg-white px-6 py-3 text-sm font-semibold text-[#FFC60A] transition hover:bg-[#FFF4BF]">Cancel</a>
                        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-[#FFC60A] px-6 py-3 text-sm font-semibold text-[#090909] shadow-lg shadow-[#E5A900]/25 transition hover:bg-[#E5A900]">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
