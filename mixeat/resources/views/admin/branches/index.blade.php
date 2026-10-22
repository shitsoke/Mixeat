@extends('layouts.app')

@section('title', 'Admin Branches | MixEat')

@section('content')
<section class="py-14"><div class="mixeat-shell px-4">
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div><p class="text-sm font-bold uppercase tracking-[0.18em] text-[#FBAB18]">Admin</p><h1 class="mt-2 text-4xl font-black text-[#FFFFFF]">Manage Branches</h1></div>
        <a href="{{ route('admin.branches.create') }}" class="rounded-full bg-[#FBAB18] px-5 py-3 font-semibold text-[#FFFFFF]">Add Branch</a>
    </div>
    @if (session('admin_status'))<div class="mb-6 rounded-2xl bg-[#201D19] px-4 py-3 font-semibold">{{ session('admin_status') }}</div>@endif
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @foreach($branches as $branch)
            <div class="rounded-[28px] border border-[#333333] bg-[#201D19] p-6 shadow-sm">
                <h2 class="text-2xl font-black text-[#FFFFFF]">{{ $branch->name }}</h2>
                <div class="mt-4 space-y-2 text-sm text-[#B8B8B8]"><p>{{ $branch->address }}</p><p>{{ $branch->opening_hours }}</p><p>{{ $branch->distance }}</p></div>
                <div class="mt-6 flex gap-3"><a href="{{ route('admin.branches.edit', $branch) }}" class="rounded-full border border-[#FBAB18] px-4 py-2 font-semibold text-[#FBAB18]">Edit</a><form action="{{ route('admin.branches.destroy', $branch) }}" method="POST">@csrf @method('DELETE')<button class="rounded-full border border-red-200 px-4 py-2 font-semibold text-[#DC3545]">Delete</button></form></div>
            </div>
        @endforeach
    </div>
</div></section>
@endsection

