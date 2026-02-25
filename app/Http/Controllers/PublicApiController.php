<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resume;

class PublicApiController extends Controller
{
    /**
     * List all resumes for the authenticated user via API.
     */
    public function resumes(Request $request)
    {
        $resumes = $request->user()->resumes()->latest()->get();
        return response()->json([
            'data' => $resumes->map(fn($r) => [
                'id' => $r->id,
                'title' => $r->title,
                'job_title' => $r->job_title,
                'last_updated' => $r->updated_at->toDateTimeString(),
            ])
        ]);
    }

    /**
     * Get specific resume details.
     */
    public function getResume(Request $request, Resume $resume)
    {
        if ($resume->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json(['data' => $resume]);
    }
}
