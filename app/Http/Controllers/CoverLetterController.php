<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\CoverLetter;
use App\Models\Resume;
use App\Http\Controllers\AiController;

class CoverLetterController extends Controller
{
    public function index()
    {
        $coverLetters = CoverLetter::where('user_id', auth()->id())
            ->with('resume:id,title')
            ->latest()
            ->get();

        $resumes = Resume::where('user_id', auth()->id())
            ->select('id', 'title')
            ->get();

        return Inertia::render('CoverLetters/Index', [
            'coverLetters' => $coverLetters,
            'resumes'      => $resumes,
        ]);
    }

    public function create()
    {
        $resumes = Resume::where('user_id', auth()->id())->select('id', 'title')->get();
        return Inertia::render('CoverLetters/Create', ['resumes' => $resumes]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'           => 'required|string|max:200',
            'resume_id'       => 'nullable|exists:resumes,id',
            'recipient_name'  => 'nullable|string|max:150',
            'recipient_title' => 'nullable|string|max:150',
            'company_name'    => 'nullable|string|max:150',
            'job_title'       => 'nullable|string|max:150',
            'body'            => 'required|string',
            'tone'            => 'required|in:professional,friendly,confident',
            'language'        => 'required|string|max:5',
        ]);

        $cl = CoverLetter::create(array_merge($validated, ['user_id' => auth()->id()]));
        return redirect()->route('cover-letters.edit', $cl)->with('success', 'Cover letter created!');
    }

    public function edit(CoverLetter $coverLetter)
    {
        abort_if($coverLetter->user_id !== auth()->id(), 403);
        $resumes = Resume::where('user_id', auth()->id())->select('id', 'title')->get();
        return Inertia::render('CoverLetters/Edit', [
            'coverLetter' => $coverLetter,
            'resumes'     => $resumes,
        ]);
    }

    public function update(Request $request, CoverLetter $coverLetter)
    {
        abort_if($coverLetter->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'title'           => 'required|string|max:200',
            'resume_id'       => 'nullable|exists:resumes,id',
            'recipient_name'  => 'nullable|string|max:150',
            'recipient_title' => 'nullable|string|max:150',
            'company_name'    => 'nullable|string|max:150',
            'job_title'       => 'nullable|string|max:150',
            'body'            => 'required|string',
            'tone'            => 'required|in:professional,friendly,confident',
            'language'        => 'required|string|max:5',
        ]);

        $coverLetter->update($validated);
        return back()->with('success', 'Cover letter saved!');
    }

    public function destroy(CoverLetter $coverLetter)
    {
        abort_if($coverLetter->user_id !== auth()->id(), 403);
        $coverLetter->delete();
        return redirect()->route('cover-letters.index')->with('success', 'Cover letter deleted.');
    }

    public function aiGenerate(Request $request)
    {
        $request->validate([
            'job_title'    => 'required|string',
            'company_name' => 'required|string',
            'tone'         => 'required|in:professional,friendly,confident',
            'resume_id'    => 'nullable|exists:resumes,id',
            'language'     => 'nullable|string',
        ]);

        $lang = $request->input('language', 'English');
        $resumeSummary = '';
        if ($request->resume_id) {
            $resume = Resume::with(['experiences', 'skills'])->find($request->resume_id);
            if ($resume) {
                $resumeSummary = "Name: {$resume->fullname}. Job title: {$resume->jobtitle}. Summary: {$resume->summary}.";
                if ($resume->experiences?->count()) {
                    $resumeSummary .= ' Experience: ' . $resume->experiences->pluck('jobtitle')->join(', ') . '.';
                }
                if ($resume->skills?->count()) {
                    $resumeSummary .= ' Skills: ' . $resume->skills->pluck('name')->join(', ') . '.';
                }
            }
        }

        $tone = match ($request->tone) {
            'friendly'  => 'warm and friendly',
            'confident' => 'confident and assertive',
            default     => 'professional and formal',
        };

        $prompt = "Write a {$tone} cover letter for the position of \"{$request->job_title}\" at \"{$request->company_name}\". "
            . ($resumeSummary ? "Candidate background: {$resumeSummary} " : '')
            . "The letter MUST BE WRITTEN IN {$lang}. "
            . "The letter should have 3-4 paragraphs: opening, skills match, motivation, and closing. Return only the letter body, no subject line.";

        try {
            $aiController = app(AiController::class);
            $fakeRequest = new \Illuminate\Http\Request();
            $fakeRequest->merge([
                'prompt'   => $prompt, 
                'context'  => 'cover_letter', 
                'variants' => 3,
                'language' => $lang
            ]);
            $response = $aiController->generate($fakeRequest);
            $data = json_decode($response->getContent(), true);
            
            return response()->json([
                'content'  => $data['variants'][0] ?? $data['result'] ?? '',
                'variants' => $data['variants'] ?? []
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'AI generation failed.'], 422);
        }
    }
}
