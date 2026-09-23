@extends('layouts.app')

@section('title', 'Assign Branch Supervisor | MixEat')

@section('content')
<section class="py-10">
    <div class="mixeat-shell px-4">
        <!-- Header -->
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#FFC60A]">Marketing Admin</p>
                <h1 class="mt-1 text-3xl font-black text-[#090909]">Assign Branch Supervisor</h1>
                <p class="mt-1 text-sm font-medium text-[#555555]">Enter an email address to assign a user as a supervisor for a specific branch.</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="inline-flex rounded-full border border-[#FFC60A] px-5 py-2.5 text-xs font-bold text-[#E5A900] transition hover:bg-[#FFF4BF]">
                Back to Dashboard
            </a>
        </div>

        @if(session('admin_status'))
            <div class="mb-6 rounded-2xl bg-[#FFF4BF] px-4 py-3 font-semibold text-[#090909]">
                {{ session('admin_status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-2xl bg-[#FCE8E6] px-4 py-3 font-semibold text-[#D93025]">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- 1. EMAIL ASSIGNMENT FORM -->
        <div class="mb-10 rounded-[28px] border border-[#E8DFAF] bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-bold text-[#090909]">Assign New Supervisor</h2>
            
            <form action="{{ route('admin.supervisors.assign') }}" method="POST" class="flex flex-col gap-4 md:flex-row md:items-end">
                @csrf
                
                <div class="flex-1">
                    <label for="email" class="mb-2 block text-xs font-bold uppercase tracking-wider text-[#777777]">User Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="e.g. salabat@gmail.com" required
                        class="w-full rounded-full border border-[#E8DFAF] bg-[#FFF9E6] px-5 py-3 text-sm font-bold text-[#090909] focus:border-[#FFC60A] focus:outline-none focus:ring-2 focus:ring-[#FFC60A]/30">
                </div>

                <div class="w-full md:w-64">
                    <label for="branch_id" class="mb-2 block text-xs font-bold uppercase tracking-wider text-[#777777]">Assigned Branch</label>
                    <select id="branch_id" name="branch_id" required
                        class="w-full rounded-full border border-[#E8DFAF] bg-[#FFF9E6] px-5 py-3 text-sm font-bold text-[#090909] focus:border-[#FFC60A] focus:outline-none focus:ring-2 focus:ring-[#FFC60A]/30">
                        <option value="">-- Select Branch --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="rounded-full bg-[#FFC60A] px-7 py-3 text-sm font-bold text-[#090909] shadow-md shadow-[#E5A900]/25 transition hover:bg-[#E5A900]">
                    Assign Supervisor
                </button>
            </form>
        </div>

        <!-- 2. CURRENTLY ASSIGNED SUPERVISORS LIST -->
        <div class="rounded-[28px] border border-[#E8DFAF] bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-bold text-[#090909]">Active Branch Supervisors</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#E8DFAF] text-xs font-bold uppercase tracking-wider text-[#777777]">
                            <th class="py-3 px-4">Supervisor Name</th>
                            <th class="py-3 px-4">Email</th>
                            <th class="py-3 px-4">Assigned Branch</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E8DFAF]/60 text-sm font-medium">
                        @forelse($supervisors as $supervisor)
                            <tr>
                                <td class="py-4 px-4 font-bold text-[#090909]">{{ $supervisor->name }}</td>
                                <td class="py-4 px-4 text-[#555555]">{{ $supervisor->email }}</td>
                                <td class="py-4 px-4">
                                    <span class="inline-block rounded-full bg-[#E6F4EA] px-3 py-1 text-xs font-bold text-[#1E8E3E]">
                                        {{ $supervisor->branch?->name ?? 'Unassigned' }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <form action="{{ route('admin.supervisors.remove', $supervisor->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Remove supervisor access for this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-bold text-[#DC3545] hover:underline">
                                            Remove Supervisor
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-sm font-medium text-[#777777]">No supervisors currently assigned. Use the form above to assign one.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection