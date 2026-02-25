<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ResumeController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user() ?? $request->user();

        if (session()->has('pending_ai_resume')) {
            $data = session('pending_ai_resume');
            
            $resume = $user->resumes()->create([
                'title' => 'AI Generated Resume',
                'first_name' => $data['first_name'] ?? $user->name,
                'last_name' => $data['last_name'] ?? null,
                'email' => $data['email'] ?? $user->email,
                'phone' => $data['phone'] ?? null,
                'photo' => $data['photo'] ?? null,
                'job_title' => $data['job_title'] ?? null,
                'summary' => $data['summary'] ?? null,
                'template' => 'modern',
                'theme' => 'modern',
            ]);

            if (isset($data['experiences']) && is_array($data['experiences'])) {
                foreach ($data['experiences'] as $exp) {
                    $resume->experiences()->create([
                        'company' => $exp['company'] ?? 'Unknown',
                        'job_title' => $exp['position'] ?? 'Employee',
                        'start_date' => $exp['start_date'] ?? null,
                        'end_date' => $exp['end_date'] ?? null,
                        'description' => $exp['description'] ?? null,
                    ]);
                }
            }
            if (isset($data['education']) && is_array($data['education'])) {
                foreach ($data['education'] as $edu) {
                    $resume->education()->create([
                        'institution' => $edu['institution'] ?? 'Unknown',
                        'degree' => $edu['degree'] ?? 'Degree',
                        'start_date' => $edu['start_date'] ?? null,
                        'end_date' => $edu['end_date'] ?? null,
                    ]);
                }
            }
            if (isset($data['skills']) && is_array($data['skills'])) {
                foreach ($data['skills'] as $skillName) {
                    $resume->skills()->create(['name' => $skillName]);
                }
            }

            session()->forget('pending_ai_resume');

            return to_route('resumes.edit', $resume);
        }

        $resumes = $user->resumes()->latest()->get();
        $templates = \App\Models\Template::where('is_active', true)->orderBy('sort_order')->take(10)->get(); // Only need a sample for the dashboard
        return Inertia::render('Dashboard', [
            'resumes' => $resumes,
            'templates' => $templates
        ]);
    }

    public function browse()
    {
        $templates = \App\Models\Template::where('is_active', true)->orderBy('sort_order')->get();
        return Inertia::render('Resume/BrowseTemplates', [
            'templates' => $templates
        ]);
    }

    public function create(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user() ?? $request->user();

        $resume = $user->resumes()->create([
            'title' => 'My Resume',
            'first_name' => $user->name,
            'email' => $user->email,
            'template' => $request->template ?? 'classic',
            'theme' => $request->template ?? 'classic',
        ]);
        
        return to_route('resumes.onboarding', $resume);
    }

    public function edit(Resume $resume)
    {
        if ($resume->user_id != Auth::id()) {
            return to_route('dashboard')->with('error', 'You do not have permission to edit this resume.');
        }
        
        $resume->load(['experiences', 'education', 'skills', 'projects', 'languages', 'certifications']);
        
        $templates = \App\Models\Template::where('is_active', true)->orderBy('sort_order')->get();

        return Inertia::render('Resume/Edit', [
            'resume' => $resume,
            'templates' => $templates
        ]);
    }

    public function onboarding(Resume $resume)
    {
        if ($resume->user_id != Auth::id()) abort(403);
        return Inertia::render('Resume/Onboarding', [
            'resume' => $resume
        ]);
    }

    public function update(Request $request, Resume $resume)
    {
        if ($resume->user_id != Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:500',
            'twitter' => 'nullable|string|max:500',
            'github' => 'nullable|string|max:500',
            'portfolio' => 'nullable|string|max:500',
            'website' => 'nullable|string|max:500',
            'birth_date' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'summary' => 'nullable|string',
            'job_title' => 'nullable|string|max:255',
            'theme' => 'nullable|string',
            'template' => 'nullable|string',
            'language' => 'nullable|string',
            'photo' => 'nullable|string',
            'design_options' => 'nullable|array',
        ]);

        $resume->update($validated);

        return redirect()->back();
    }

    public function destroy(Resume $resume)
    {
        if ($resume->user_id != Auth::id()) {
            abort(403);
        }
        
        $resume->delete();
        
        return to_route('dashboard');
    }

    public function duplicate(Resume $resume)
    {
        if ($resume->user_id != Auth::id()) abort(403);

        $resume->load(['experiences', 'education', 'skills', 'projects', 'languages', 'certifications']);

        $newResume = $resume->replicate();
        $newResume->title = $resume->title . ' (Copy)';
        $newResume->save();

        foreach ($resume->experiences as $item) {
            $newResume->experiences()->create($item->toArray());
        }
        foreach ($resume->education as $item) {
            $newResume->education()->create($item->toArray());
        }
        foreach ($resume->skills as $item) {
            $newResume->skills()->create($item->toArray());
        }
        foreach ($resume->projects as $item) {
            $newResume->projects()->create($item->toArray());
        }
        foreach ($resume->languages as $item) {
            $newResume->languages()->create($item->toArray());
        }
        foreach ($resume->certifications as $item) {
            $newResume->certifications()->create($item->toArray());
        }

        return to_route('resumes.edit', $newResume);
    }
    
    // Experience
    public function addExperience(Request $request, Resume $resume)
    {
        if ($resume->user_id != Auth::id()) abort(403);
        
        $resume->experiences()->create($request->validate([
            'company' => 'required|string',
            'job_title' => 'required|string',
            'start_date' => 'nullable|string',
            'end_date' => 'nullable|string',
            'description' => 'nullable|string',
            'current' => 'boolean'
        ]));
        
        return back();
    }

    public function deleteExperience(Resume $resume, $experienceId)
    {
        if ($resume->user_id != Auth::id()) abort(403);
        $resume->experiences()->where('id', $experienceId)->delete();
        return back();
    }

    // Education
    public function addEducation(Request $request, Resume $resume)
    {
        if ($resume->user_id != Auth::id()) abort(403);
        
        $resume->education()->create($request->validate([
            'institution' => 'required|string',
            'degree' => 'required|string',
            'start_date' => 'nullable|string',
            'end_date' => 'nullable|string',
            'description' => 'nullable|string',
        ]));
        
        return back();
    }
    
    public function deleteEducation(Resume $resume, $educationId)
    {
        if ($resume->user_id != Auth::id()) abort(403);
        $resume->education()->where('id', $educationId)->delete();
        return back();
    }

    // Skills
    public function addSkill(Request $request, Resume $resume)
    {
        if ($resume->user_id != Auth::id()) abort(403);
        
        $resume->skills()->create($request->validate([
            'name' => 'required|string',
            'level' => 'nullable|string',
        ]));
        
        return back();
    }

    public function deleteSkill(Resume $resume, $skillId)
    {
        if ($resume->user_id != Auth::id()) abort(403);
        $resume->skills()->where('id', $skillId)->delete();
        return back();
    }
    
    // Projects
    public function addProject(Request $request, Resume $resume)
    {
        if ($resume->user_id != Auth::id()) abort(403);
        
        $resume->projects()->create($request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'link' => 'nullable|string',
        ]));
        
        return back();
    }

    public function deleteProject(Resume $resume, $projectId)
    {
        if ($resume->user_id != Auth::id()) abort(403);
        $resume->projects()->where('id', $projectId)->delete();
        return back();
    }

    // Languages
    public function addLanguage(Request $request, Resume $resume)
    {
        if ($resume->user_id != Auth::id()) abort(403);

        $resume->languages()->create($request->validate([
            'name' => 'required|string|max:100',
            'proficiency' => 'nullable|string|max:50',
        ]));

        return back();
    }

    public function deleteLanguage(Resume $resume, $languageId)
    {
        if ($resume->user_id != Auth::id()) abort(403);
        $resume->languages()->where('id', $languageId)->delete();
        return back();
    }

    // Certifications
    public function addCertification(Request $request, Resume $resume)
    {
        if ($resume->user_id != Auth::id()) abort(403);

        $resume->certifications()->create($request->validate([
            'name' => 'required|string|max:255',
            'issued_by' => 'nullable|string|max:255',
            'issue_date' => 'nullable|string',
            'expiry_date' => 'nullable|string',
            'credential_url' => 'nullable|url|max:500',
        ]));

        return back();
    }

    public function deleteCertification(Resume $resume, $certificationId)
    {
        if ($resume->user_id != Auth::id()) abort(403);
        $resume->certifications()->where('id', $certificationId)->delete();
        return back();
    }

    // PDF Download
    public function download(Resume $resume)
    {
        if ($resume->user_id != Auth::id()) {
            abort(403);
        }

        // Ensure share token exists for the script to access
        if (!$resume->share_token) {
            $resume->update(['share_token' => Str::random(48)]);
        }

        // Temporary enable public if not already (or just use the token regardless of is_public for the script)
        // Note: Our PublicResumeController@show checks is_public=true. 
        // For security, we'll use a specific route or just toggle is_public temporarily.
        $wasPublic = $resume->is_public;
        if (!$wasPublic) {
            $resume->update(['is_public' => true]);
        }

        $url = route('resumes.show', ['token' => $resume->share_token]) . '?pdf=1';
        // If running locally with artisan serve, we might need to ensure the host is correct
        // But route() should handle it based on APP_URL.

        $tempPath = storage_path('app/public/resume-' . $resume->id . '.pdf');
        $scriptPath = base_path('scripts/pdf.js');
        
        // Command to run node script
        $command = "node \"$scriptPath\" \"$url\" \"$tempPath\"";
        
        try {
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                Log::error('PDF Generation failed: ' . implode("\n", $output));
                throw new \Exception('Node script failed with exit code ' . $returnCode);
            }

            // Restore public status
            if (!$wasPublic) {
                $resume->update(['is_public' => false]);
            }

            return response()->download($tempPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            // Restore public status on error
            if (!$wasPublic) {
                $resume->update(['is_public' => false]);
            }
            Log::error('PDF Generation Exception: ' . $e->getMessage());
            return back()->with('error', 'Could not generate high-quality PDF. ' . $e->getMessage());
        }
    }

    public function print(Resume $resume)
    {
        if ($resume->user_id != Auth::id()) {
            abort(403);
        }

        $resume->load(['experiences', 'education', 'skills', 'projects', 'languages', 'certifications']);

        return Inertia::render('Resume/Print', [
            'resume' => $resume
        ]);
    }
}
