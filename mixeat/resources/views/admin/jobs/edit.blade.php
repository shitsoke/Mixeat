@extends('layouts.admin')
@section('content')
<div class="p-6 max-w-xl">
    <h1 class="text-2xl font-bold text-white mb-4">Edit Job</h1>
    <form action="{{ route('admin.jobs.update', $job) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')
        <input name="title" value="{{ $job->title }}" required class="w-full p-3 rounded-xl bg-[#1E1D1B] text-white border border-[#333]" />
        <input name="location" value="{{ $job->location }}" required class="w-full p-3 rounded-xl bg-[#1E1D1B] text-white border border-[#333]" />
        <input name="employment_type" value="{{ $job->employment_type }}" required class="w-full p-3 rounded-xl bg-[#1E1D1B] text-white border border-[#333]" />
        <label class="text-white"><input type="checkbox" name="is_active" {{ $job->is_active ? 'checked' : '' }} /> Active</label>
        <button class="bg-[#FBAB18] text-black px-4 py-2 rounded-full font-bold">Update</button>
    </form>
</div>
@endsection
