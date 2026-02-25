<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AiController extends Controller
{
    public function audit(Request $request)
    {
        $request->validate([
            'resume'   => 'required|array',
            'language' => 'nullable|string',
        ]);

        $apiKey = config('services.groq.key');
        if (!$apiKey) {
            return response()->json(['error' => 'AI not configured. Please add GROQ_API_KEY.'], 500);
        }

        $resume = $request->input('resume');
        $lang = $request->input('language', 'English');

        $systemPrompt = "You are a world-class Executive Resume Auditor and ATS (Applicant Tracking System) Expert. 
Analyze the provided resume data and return a JSON object containing:
1. 'score': A numerical score (0-100) reflecting overall polish and ATS readiness.
2. 'feedback': A short, encouraging sentence about the resume's current state.
3. 'ats': An object containing:
   - 'score': (0-100) specifically for ATS compatibility.
   - 'parsing_issues': Array of strings (e.g., 'Complex layout might break parsing').
   - 'missing_keywords': Array of 5-8 relevant keywords for the job title.
   - 'action_verb_check': A brief comment on the use of impact-driven verbs.
4. 'sections': Suggestions for improvements.
   - 'summary': suggestion (string) and tips (array of strings).
   - 'experiences': An array of objects, each with 'id' (from original), 'suggestion' (improved description), and 'tips' (array).
   - 'skills': suggestion (string) and tips (array).

IMPORTANT: Return ONLY raw JSON. No markdown, no preamble. Use the SAME LANGUAGE as requested ({$lang}).";

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type'  => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'       => 'llama-3.3-70b-versatile',
                'temperature' => 0.5,
                'messages'    => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user',   'content' => json_encode($resume)],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['choices'][0]['message']['content'] ?? '{}';
                
                // Cleanup potentially returned markdown
                $text = preg_replace('/^```json\s*/', '', $text);
                $text = preg_replace('/\s*```$/', '', $text);
                $text = trim($text);

                return response()->json(json_decode($text, true));
            }

            return response()->json(['error' => 'AI Audit failed'], 500);
        } catch (\Exception $e) {
            Log::error('Audit Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Service Unavailable'], 500);
        }
    }

    public function generate(Request $request)
    {
        $request->validate([
            'prompt'   => 'required|string',
            'context'  => 'nullable|string',
            'variants' => 'nullable|integer|min:1|max:3',
        ]);

        $apiKey = config('services.groq.key');

        if (!$apiKey) {
            return response()->json(['error' => 'AI not configured. Please add GROQ_API_KEY in settings.'], 500);
        }

        $variantsCount = $request->input('variants', 1);
        $context = $request->input('context', 'general');
        $lang = $request->input('language', 'English');
        $systemPrompt = $this->buildSystemPrompt($context, $lang, $variantsCount > 1);

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type'  => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'       => 'llama-3.3-70b-versatile',
                'temperature' => 0.8, // Slightly higher for creativity in variants
                'messages'    => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user',   'content' => $request->prompt],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['choices'][0]['message']['content'] ?? '';

                if ($variantsCount > 1) {
                    $variants = explode('[[VARIANT_SEPARATOR]]', $text);
                    $variants = array_map('trim', $variants);
                    $variants = array_filter($variants);
                    // Ensure we only return the requested count
                    $variants = array_slice($variants, 0, $variantsCount);
                    return response()->json(['variants' => $variants]);
                }

                return response()->json(['result' => trim($text)]);
            }

            Log::error('Groq API Error: ' . $response->body());
            return response()->json(['error' => 'AI service error. Please try again.'], 500);

        } catch (\Exception $e) {
            Log::error('Groq Exception: ' . $e->getMessage());
            return response()->json(['error' => 'AI service unavailable. Please try again.'], 500);
        }
    }

    public function copilotChat(Request $request)
    {
        $request->validate([
            'messages' => 'required|array',
            'messages.*.role' => 'required|string|in:user,assistant',
            'messages.*.content' => 'required|string',
        ]);

        $apiKey = config('services.groq.key');
        if (!$apiKey) {
            return response()->json(['message' => 'AI service is not configured. Please add GROQ_API_KEY.']);
        }

        $systemPrompt = "You are Civio AI, an expert resume builder and friendly career coach.
Your job is to chat with the user to build a professional resume from scratch.
CRITICAL MANDATE: You MUST reply in the exact SAME LANGUAGE that the user is speaking to you (e.g., if they speak Arabic, reply in Arabic; if French, reply in French). DO NOT speak English unless the user speaks English.

Ask ONE brief question at a time to gather the following: 
1. Target Job Title 
2. Professional Summary (or generate one based on their title) 
3. Work Experience 
4. Education 
5. Skills
6. Profile Photo (Briefly let them know they can upload a professional photo using the attachment icon next to the send button if they want one on their CV).

Be super conversational, encouraging, and extremely brief. DO NOT give long instructions.

IMPORTANT INSTRUCTION:
Once you have gathered sufficient information to build a decent resume (Job Title, some summary, at least 1 experience or 1 skill, and you've asked about the photo), you MUST stop asking questions.
Instead, reply ONLY with a valid JSON object formatting the resume, with the exact structure below. DO NOT include ANY other conversational text, markdown formatting like ```json, just the raw JSON object starting with { and ending with }.

{
  \"action\": \"finish\",
  \"resume\": {
      \"first_name\": \"John\",
      \"last_name\": \"Doe\",
      \"email\": \"\",
      \"phone\": \"\",
      \"photo\": \"URL_OF_THE_UPLOADED_PHOTO_IF_ANY\",
      \"job_title\": \"Software Engineer\",
      \"summary\": \"A short professional summary based on the chat...\",
      \"experiences\": [ { \"company\": \"ABC Corp\", \"position\": \"Developer\", \"start_date\": \"Jan 2020\", \"end_date\": \"Present\", \"description\": \"Built cool things...\" } ],
      \"education\": [ { \"institution\": \"University X\", \"degree\": \"BSc Computer Science\", \"start_date\": \"2016\", \"end_date\": \"2020\" } ],
      \"skills\": [\"PHP\", \"Vue.js\"]
  }
}

If you still need more information, simply reply with a normal conversational question. Note: If the user uploads a photo, the system will inject a message like '[System: User uploaded photo at URL]'. You must remember this URL and place it in the \"photo\" field of the final JSON.";

        $messages = $request->input('messages', []);
        
        // Remove the original frontend welcome message to save tokens, or leave it. We'll leave it but add system prompt
        array_unshift($messages, ['role' => 'system', 'content' => $systemPrompt]);

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type'  => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'       => 'llama-3.3-70b-versatile',
                'temperature' => 0.6,
                'messages'    => $messages,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['choices'][0]['message']['content'] ?? '';
                
                $text = trim($text);
                
                // Sometimes LLM adds ```json around it even if instructed not to
                $text = preg_replace('/```json\s*/', '', $text);
                $text = preg_replace('/```\s*/', '', $text);
                $text = trim($text);
                
                if (str_starts_with($text, '{') && str_ends_with($text, '}')) {
                    $parsed = json_decode($text, true);
                    if (isset($parsed['action']) && $parsed['action'] === 'finish' && isset($parsed['resume'])) {
                        // Success! Store in session
                        session(['pending_ai_resume' => $parsed['resume']]);
                        
                        return response()->json([
                            'action' => 'finish',
                            'redirect_url' => Auth::check() ? route('dashboard') : route('register')
                        ]);
                    }
                }

                return response()->json(['message' => $text]);
            }

            Log::error('Groq AI Chat Error: ' . $response->body());
            return response()->json(['message' => 'I am having trouble connecting. Please try again later.']);

        } catch (\Exception $e) {
            Log::error('Groq Chat Exception: ' . $e->getMessage());
            return response()->json(['message' => 'Service is momentarily unavailable.']);
        }
    }

    public function uploadCopilotPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:5120', // 5MB max
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('copilot-photos', 'public');
            $url = Storage::url($path);
            
            return response()->json([
                'url' => $url
            ]);
        }

        return response()->json(['message' => 'No file uploaded'], 400);
    }

    private function buildSystemPrompt(string $context, string $language = 'English', bool $multiple = false): string
    {
        $base = "Expert resume writer. Language: {$language}. Style: Ultra-concise, high impact, telegram-style. 
        MANDATE: Output MUST be under 30 words AND under 300 characters. No preamble. No explanation. Just the content.";

        if ($multiple) {
            $base .= " PROVIDE 3 VARIANTS. Separate with [[VARIANT_SEPARATOR]].";
        }

        return match ($context) {
            'summary'     => $base . " Task: Create a high-density, ultra-short ATS summary. Use keywords and impact-driven fragments.",
            'description' => $base . " Task: Generate 2-3 very short bullet points. Limit: 1 line per bullet.",
            'job_title'    => "Expert Recruitment. Task: Optimize job title to be professional and short in {$language}. Return ONLY the title.",
            'cover_letter' => $base . " Task: Small, high-impact cover letter body (max 2 short paragraphs).",
            'improve'      => "Expert CV Editor. Task: Rewrite for extreme impact and brevity in {$language}. Reply ONLY with short result.",
            'translate'    => "Expert Translator. Task: Professional translation to {$language}. Reply ONLY with short result.",
            'skills'       => "Expert Career Consultant. Task: List 8-10 essential ATS skills for {$language} resume. Role provided in user prompt. Format: Comma-separated list only.",
            default       => $base,
        };
    }
}
