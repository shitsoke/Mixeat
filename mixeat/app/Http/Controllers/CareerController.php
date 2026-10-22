<?php

namespace App\Http\Controllers;

use App\Models\JobOpening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CareerController extends Controller
{
    public function index()
    {
        $jobs = JobOpening::where('is_active', true)->orderBy('created_at', 'desc')->get();
        return view('storefront.careers', compact('jobs'));
    }

    public function showApply(JobOpening $job)
    {
        return view('storefront.apply', compact('job'));
    }

    public function apply(Request $request, JobOpening $job)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contact_number' => 'required|string|max:50',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:5120',
            'cover_letter' => 'nullable|string',
        ]);

        $path = $request->file('resume')->store('resumes', 'local');

        \App\Models\JobApplication::create([
            'job_opening_id' => $job->id,
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'contact_number' => $data['contact_number'],
            'resume_path' => $path,
            'cover_letter' => $data['cover_letter'] ?? null,
        ]);

        return redirect()->route('careers')->with('success', 'Application submitted successfully.');
    }
}
