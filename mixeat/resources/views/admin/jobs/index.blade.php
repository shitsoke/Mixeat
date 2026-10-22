@extends('layouts.admin')
@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-white mb-4">Job Openings</h1>
    <a href="{{ route('admin.jobs.create') }}" class="bg-[#FBAB18] text-black px-4 py-2 rounded-full font-bold">Add Job</a>
    <table class="w-full mt-6 text-white">
        <thead><tr><th>Title</th><th>Location</th><th>Type</th><th>Active</th><th>Actions</th></tr></thead>
        <tbody>
        @foreach($jobs as $job)
            <tr class="border-t border-[#333]">
                <td>{{ $job->title }}</td>
                <td>{{ $job->location }}</td>
                <td>{{ $job->employment_type }}</td>
                <td>{{ $job->is_active ? 'Yes' : 'No' }}</td>
                <td>
                    <a href="{{ route('admin.jobs.edit', $job) }}" class="text-[#FBAB18]">Edit</a>
                    <form action="{{ route('admin.jobs.toggle', $job) }}" method="POST" class="inline">@csrf @method('PATCH')<button class="ml-2 text-white">Toggle</button></form>
                    <form action="{{ route('admin.jobs.destroy', $job) }}" method="POST" class="inline">@csrf @method('DELETE')<button class="ml-2 text-red-400">Delete</button></form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h2 class="text-xl font-bold text-white mt-10 mb-4">Applications</h2>
    <table class="w-full text-white">
        <thead><tr><th>Name</th><th>Email</th><th>Position</th><th>Resume</th></tr></thead>
        <tbody>
        @foreach($applications as $app)
            <tr class="border-t border-[#333]">
                <td>{{ $app->full_name }}</td>
                <td>{{ $app->email }}</td>
                <td>{{ $app->jobOpening->title ?? '' }}</td>
                <td><a href="{{ route('admin.jobs.resume', $app) }}" class="text-[#FBAB18]">Download</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
