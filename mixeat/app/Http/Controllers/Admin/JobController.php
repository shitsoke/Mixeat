<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobOpening;
use App\Models\JobApplication;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index()
    {
        $jobs = JobOpening::orderBy('created_at', 'desc')->get();
        $applications = JobApplication::with('jobOpening')->orderBy('created_at', 'desc')->get();
        return view('admin.jobs.index', compact('jobs', 'applications'));
    }

    public function create()
    {
        return view('admin.jobs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'employment_type' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        JobOpening::create($data);
        return redirect()->route('admin.jobs.index')->with('success', 'Job created.');
    }

    public function edit(JobOpening $job)
    {
        return view('admin.jobs.edit', compact('job'));
    }

    public function update(Request $request, JobOpening $job)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'employment_type' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $job->update($data);
        return redirect()->route('admin.jobs.index')->with('success', 'Job updated.');
    }

    public function destroy(JobOpening $job)
    {
        $job->delete();
        return redirect()->route('admin.jobs.index')->with('success', 'Job deleted.');
    }

    public function toggle(JobOpening $job)
    {
        $job->update(['is_active' => ! $job->is_active]);
        return back()->with('success', 'Status updated.');
    }

    public function downloadResume(JobApplication $application)
    {
        return \Illuminate\Support\Facades\Storage::disk('local')->download($application->resume_path);
    }
}
