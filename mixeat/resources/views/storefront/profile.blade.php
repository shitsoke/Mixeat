@extends('layouts.app')

@section('title', 'My Profile | MixEat')

@section('content')
    <section class="py-14">
        <div class="mixeat-shell px-4">
            <div class="mx-auto max-w-3xl rounded-[32px] border border-[#E8DFAF] bg-white p-8 shadow-[0_20px_60px_rgba(229,169,0,0.12)]">
                @if (session('profile_status'))
                    <div class="mb-6 rounded-2xl border border-[#E8DFAF] bg-[#FFF4BF] px-4 py-3 text-sm font-semibold text-[#090909]" role="status">
                        {{ session('profile_status') }}
                    </div>
                @endif
                <div class="mb-8 flex flex-col items-center gap-4 text-center md:flex-row md:text-left">
                    <div class="flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-[#FFC60A] to-[#E5A900] text-2xl font-black text-white shadow-lg shadow-[#E5A900]/25">
                        {{ strtoupper(substr((string) ($user->first_name ?: $user->name), 0, 1) . substr((string) $user->last_name, 0, 1)) }}
                    </div>
                    <div>
                        <h1 class="text-3xl font-black text-[#090909]">{{ $user->name }}</h1>
                        <p class="text-sm font-medium text-[#777777]">
                            @if ($user->isMarketing() || $user->isSupervisor() || $user->is_admin)
                                Admin Account
                            @else
                                Customer Account
                            @endif
                        </p>
                    </div>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="rounded-[24px] bg-[#FFF9E6] p-5">
                        <p class="text-sm font-bold uppercase tracking-[0.12em] text-[#555555]">Profile Information</p>
                        <dl class="mt-4 space-y-3 text-sm text-[#555555]">
                            <div class="flex items-center justify-between gap-3"><dt>Name</dt><dd class="font-semibold text-[#090909]">{{ $user->name }}</dd></div>
                            <div class="flex items-center justify-between gap-3"><dt>Email</dt><dd class="font-semibold text-[#090909]">{{ $user->email }}</dd></div>
                            <div class="flex items-center justify-between gap-3"><dt>Phone</dt><dd class="font-semibold text-[#090909]">{{ $user->phone ?: 'Not provided' }}</dd></div>
                        </dl>
                    </div>

                    <div class="rounded-[24px] bg-[#FFF9E6] p-5 flex flex-col justify-center">
                        <p class="text-sm font-bold uppercase tracking-[0.12em] text-[#555555]">Account Actions</p>
                        <div class="mt-4 space-y-3">
                            <a href="{{ route('profile.edit') }}" class="block w-full rounded-full border border-[#FFC60A] bg-white px-5 py-3 text-center text-sm font-semibold text-[#FFC60A] transition hover:bg-[#FFF4BF]">Edit Profile</a>
                            
                            @if (! $user->isMarketing())
                                <a href="{{ route('password.change') }}" class="block w-full rounded-full border border-[#FFC60A] bg-white px-5 py-3 text-center text-sm font-semibold text-[#FFC60A] transition hover:bg-[#FFF4BF]">Change Password</a>
                            @else
                                <button type="button" disabled class="block w-full cursor-not-allowed rounded-full border border-gray-200 bg-gray-100 px-5 py-3 text-center text-sm font-semibold text-gray-400">Unavailable</button>
                            @endif

                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full rounded-full bg-[#FFC60A] px-5 py-3 text-sm font-semibold text-[#090909] shadow-lg shadow-[#E5A900]/25 transition hover:bg-[#E5A900]">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection