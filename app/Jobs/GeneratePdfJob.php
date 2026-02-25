<?php

namespace App\Jobs;

use App\Models\Resume;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class GeneratePdfJob implements ShouldQueue
{
    use Queueable;

    protected $resume;
    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct(Resume $resume, User $user)
    {
        $this->resume = $resume;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Starting PDF generation for Resume ID: {$this->resume->id}");

        // In a real queue worker, you can't rely on 'localhost' being correct if the worker is in a different container.
        // APP_URL should be set correctly in .env
        $signedUrl = URL::temporarySignedRoute(
            'resumes.pdf-view',
            now()->addMinutes(10),
            ['resume' => $this->resume->id]
        );

        $url = $signedUrl . '&pdf=1';
        $filename = 'resume-' . $this->resume->id . '-' . time() . '.pdf';
        $tempPath = storage_path('app/public/' . $filename);
        $scriptPath = base_path('scripts/pdf.js');

        $command = "node \"$scriptPath\" \"$url\" \"$tempPath\"";

        try {
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                Log::error('PDF Generation failed: ' . implode("\n", $output));
                throw new \Exception('Node script failed with exit code ' . $returnCode);
            }

            // Here you would typically upload to S3 or notify the user
            // For now, we'll just log success. Ideally, you'd send an email or a notification.
            Log::info("PDF generated successfully at: $tempPath");

            // Example: Notify user (Pseudo-code)
            // $this->user->notify(new PdfReadyNotification($filename));

        } catch (\Exception $e) {
            Log::error('PDF Generation Exception: ' . $e->getMessage());
            $this->fail($e);
        }
    }
}
