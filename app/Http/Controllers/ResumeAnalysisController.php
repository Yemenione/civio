<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ResumeAnalysisController extends Controller
{
    public function analyze(Request $request)
    {
        $request->validate([
            'resume_text' => 'required|string', // Support text for now, can expand to PDF parsing
        ]);

        $apiKey = config('services.gemini.key');
        
        if (!$apiKey) {
            return response()->json(['error' => 'Gemini Assistant Misconfigured'], 500);
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}";

        $prompt = "You are a senior HR expert and ATS optimization specialist. Analyze the following resume text. 
        Provide a JSON response with:
        1. 'score' (out of 100)
        2. 'summary' (brief feedback)
        3. 'strengths' (array of bullet points)
        4. 'weaknesses' (array of bullet points)
        5. 'improvements' (actionable advice)
        6. 'extracted_data' (structured name, title, contact, skills, experience summary)
        
        Resume Text:
        " . $request->resume_text;

        try {
            $response = Http::post($url, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'response_mime_type' => 'application/json'
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $result = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
                return response()->json(json_decode($result, true));
            }

            Log::error('Gemini Analysis Error: ' . $response->body());
            return response()->json(['error' => 'Analysis Failed: ' . $response->status()], 500);
        } catch (\Exception $e) {
            Log::error('Gemini Analysis Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Analysis Service Exception'], 500);
        }
    }

    public function index()
    {
        return \Inertia\Inertia::render('Analysis/Dashboard');
    }
}
