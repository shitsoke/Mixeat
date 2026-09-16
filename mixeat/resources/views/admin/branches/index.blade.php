@extends('layouts.app')

@section('title', 'Admin Branches | MixEat')

@section('content')
<section class="py-14"><div class="mixeat-shell px-4">
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div><p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FFC60A]">Admin</p><h1 class="mt-2 text-4xl font-black text-[#090909]">Manage Branches</h1></div>
        <a href="{{ route('admin.branches.create') }}" class="rounded-full bg-[#FFC60A] px-5 py-3 font-semibold text-[#090909]">Add Branch</a>
    </div>
    @if (session('admin_status'))<div class="mb-6 rounded-2xl bg-[#FFF4BF] px-4 py-3 font-semibold">{{ session('admin_status') }}</div>@endif
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @foreach($branches as $branch)
            <div class="rounded-[28px] border border-[#E8DFAF] bg-white p-6 shadow-sm">
                <h2 class="text-2xl font-black text-[#090909]">{{ $branch->name }}</h2>
                <div class="mt-4 space-y-2 text-sm text-[#555555]"><p>{{ $branch->address }}</p><p>{{ $branch->opening_hours }}</p><p>{{ $branch->distance }}</p></div>
                <div class="mt-6 flex gap-3"><a href="{{ route('admin.branches.edit', $branch) }}" class="rounded-full border border-[#FFC60A] px-4 py-2 font-semibold text-[#E5A900]">Edit</a><form action="{{ route('admin.branches.destroy', $branch) }}" method="POST">@csrf @method('DELETE')<button class="rounded-full border border-red-200 px-4 py-2 font-semibold text-[#DC3545]">Delete</button></form></div>
            </div>
        @endforeach
    </div>
</div></section>
@endsection
