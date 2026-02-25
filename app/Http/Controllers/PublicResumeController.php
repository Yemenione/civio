<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Resume;
use Illuminate\Support\Str;

class PublicResumeController extends Controller
{
    /**
     * Toggle public sharing on/off.
     * Generates a unique share token on first enable.
     */
    public function toggleShare(Request $request, Resume $resume)
    {
        abort_if($resume->user_id !== auth()->id(), 403);

        if ($resume->is_public) {
            $resume->update(['is_public' => false]);
            return back()->with('success', 'Public link disabled.');
        } else {
            $resume->update([
                'is_public'   => true,
                'share_token' => $resume->share_token ?? Str::random(48),
            ]);
            return back()->with('success', 'Public link enabled.');
        }
    }

    /**
     * Regenerate share token (invalidates old link).
     */
    public function regenerateToken(Resume $resume)
    {
        abort_if($resume->user_id !== auth()->id(), 403);
        $resume->update(['share_token' => Str::random(48)]);
        return back()->with('success', 'Share link regenerated.');
    }

    /**
     * Public view — no auth required.
     */
    public function show(string $token)
    {
        $resume = Resume::where('share_token', $token)
            ->where('is_public', true)
            ->with(['experiences', 'education', 'skills', 'projects', 'languages', 'certifications'])
            ->firstOrFail();

        return Inertia::render('Resume/PublicView', [
            'resume' => $resume,
        ]);
    }
}
