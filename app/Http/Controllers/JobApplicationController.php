<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\JobApplication;
use App\Models\Resume;

class JobApplicationController extends Controller
{
    public function index()
    {
        $applications = JobApplication::where('user_id', auth()->id())
            ->with('resume:id,title')
            ->latest()
            ->get();

        $resumes = Resume::where('user_id', auth()->id())->select('id', 'title')->get();

        // Count by status for the Kanban summary bar
        $counts = $applications->groupBy('status')->map->count();

        return Inertia::render('JobTracker/Index', [
            'applications' => $applications,
            'resumes'      => $resumes,
            'counts'       => $counts,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name'    => 'required|string|max:200',
            'job_title'       => 'required|string|max:200',
            'location'        => 'nullable|string|max:200',
            'job_url'         => 'nullable|url|max:500',
            'status'          => 'required|in:saved,applied,interview,offer,rejected,withdrawn',
            'applied_at'      => 'nullable|date',
            'interview_at'    => 'nullable|date',
            'salary_min'      => 'nullable|numeric|min:0',
            'salary_max'      => 'nullable|numeric|min:0',
            'salary_currency' => 'nullable|string|max:3',
            'notes'           => 'nullable|string|max:2000',
            'excitement'      => 'integer|min:1|max:5',
            'resume_id'       => 'nullable|exists:resumes,id',
        ]);

        JobApplication::create(array_merge($validated, ['user_id' => auth()->id()]));
        return back()->with('success', 'Application added!');
    }

    public function update(Request $request, JobApplication $jobApplication)
    {
        abort_if($jobApplication->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'company_name'    => 'required|string|max:200',
            'job_title'       => 'required|string|max:200',
            'location'        => 'nullable|string|max:200',
            'job_url'         => 'nullable|url|max:500',
            'status'          => 'required|in:saved,applied,interview,offer,rejected,withdrawn',
            'applied_at'      => 'nullable|date',
            'interview_at'    => 'nullable|date',
            'salary_min'      => 'nullable|numeric|min:0',
            'salary_max'      => 'nullable|numeric|min:0',
            'salary_currency' => 'nullable|string|max:3',
            'notes'           => 'nullable|string|max:2000',
            'excitement'      => 'integer|min:1|max:5',
            'resume_id'       => 'nullable|exists:resumes,id',
        ]);

        $jobApplication->update($validated);
        return back()->with('success', 'Application updated!');
    }

    public function updateStatus(Request $request, JobApplication $jobApplication)
    {
        abort_if($jobApplication->user_id !== auth()->id(), 403);
        $request->validate(['status' => 'required|in:saved,applied,interview,offer,rejected,withdrawn']);
        $jobApplication->update(['status' => $request->status]);
        return back();
    }

    public function destroy(JobApplication $jobApplication)
    {
        abort_if($jobApplication->user_id !== auth()->id(), 403);
        $jobApplication->delete();
        return back()->with('success', 'Application deleted.');
    }
}
