<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CoverLetterController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\PublicResumeController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\ApiTokenController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'stats' => [
            'resumesCount' => \App\Models\Resume::count() + 10000,
            'templatesCount' => \App\Models\Template::count() ?: 6,
            'languagesCount' => \App\Models\Language::count() ?: 3,
        ]
    ]);
});

Route::get('/dashboard', [ResumeController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/privacy-policy', function () {
    return Inertia::render('Legal/Privacy');
})->name('privacy');

Route::get('/terms-of-service', function () {
    return Inertia::render('Legal/Terms');
})->name('terms');

Route::get('/about-us', function () {
    return Inertia::render('Legal/About');
})->name('about');

Route::get('/contact-us', function () {
    return Inertia::render('Legal/Contact');
})->name('contact');

Route::get('/pricing', [SubscriptionController::class, 'index'])->name('pricing');
Route::get('/business', function () {
    return Inertia::render('Business');
})->name('business');

Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index']);

// AI Copilot Chat (Public)
Route::post('/ai/copilot/chat', [AiController::class, 'copilotChat'])->name('ai.copilot.chat');
Route::post('/ai/copilot/upload', [AiController::class, 'uploadCopilotPhoto'])->name('ai.copilot.upload');

Route::middleware('auth')->group(function () {
    Route::post('/subscription/upgrade', [SubscriptionController::class, 'upgrade'])->name('subscription.upgrade');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // API Tokens
    Route::get('/profile/api-tokens', [ApiTokenController::class, 'index'])->name('api-tokens.index');
    Route::post('/profile/api-tokens', [ApiTokenController::class, 'store'])->name('api-tokens.store');
    Route::delete('/profile/api-tokens/{id}', [ApiTokenController::class, 'destroy'])->name('api-tokens.destroy');

    // Resumes
    Route::get('/resumes/browse', [ResumeController::class, 'browse'])->name('resumes.browse');
    Route::post('/resumes', [ResumeController::class, 'create'])->name('resumes.create');
    Route::get('/resumes/{resume}/onboarding', [ResumeController::class, 'onboarding'])->name('resumes.onboarding');
    Route::get('/resumes/{resume}/edit', [ResumeController::class, 'edit'])->name('resumes.edit');
    Route::match(['put', 'patch'], '/resumes/{resume}', [ResumeController::class, 'update'])->name('resumes.update');
    Route::delete('/resumes/{resume}', [ResumeController::class, 'destroy'])->name('resumes.destroy');
    Route::post('/resumes/{resume}/duplicate', [ResumeController::class, 'duplicate'])->name('resumes.duplicate');
    Route::get('/resumes/{resume}/download', [ResumeController::class, 'download'])->name('resumes.download');
    Route::get('/resumes/{resume}/print', [ResumeController::class, 'print'])->name('resumes.print');

    // Experience
    Route::post('/resumes/{resume}/experience', [ResumeController::class, 'addExperience'])->name('resumes.experience.store');
    Route::delete('/resumes/{resume}/experience/{id}', [ResumeController::class, 'deleteExperience'])->name('resumes.experience.destroy');

    // Education
    Route::post('/resumes/{resume}/education', [ResumeController::class, 'addEducation'])->name('resumes.education.store');
    Route::delete('/resumes/{resume}/education/{id}', [ResumeController::class, 'deleteEducation'])->name('resumes.education.destroy');

    // Skills
    Route::post('/resumes/{resume}/skills', [ResumeController::class, 'addSkill'])->name('resumes.skills.store');
    Route::delete('/resumes/{resume}/skills/{id}', [ResumeController::class, 'deleteSkill'])->name('resumes.skills.destroy');

    // Projects
    Route::post('/resumes/{resume}/projects', [ResumeController::class, 'addProject'])->name('resumes.projects.store');
    Route::delete('/resumes/{resume}/projects/{id}', [ResumeController::class, 'deleteProject'])->name('resumes.projects.destroy');

    // Languages
    Route::post('/resumes/{resume}/languages', [ResumeController::class, 'addLanguage'])->name('resumes.languages.store');
    Route::delete('/resumes/{resume}/languages/{id}', [ResumeController::class, 'deleteLanguage'])->name('resumes.languages.destroy');

    // Certifications
    Route::post('/resumes/{resume}/certifications', [ResumeController::class, 'addCertification'])->name('resumes.certifications.store');
    Route::delete('/resumes/{resume}/certifications/{id}', [ResumeController::class, 'deleteCertification'])->name('resumes.certifications.destroy');

    // AI
    Route::post('/ai/generate', [AiController::class, 'generate'])->name('ai.generate');
    Route::post('/ai/audit', [AiController::class, 'audit'])->name('ai.audit');

    // ─── Cover Letters ────────────────────────────────────────────
    Route::get('/cover-letters', [CoverLetterController::class, 'index'])->name('cover-letters.index');
    Route::get('/cover-letters/create', [CoverLetterController::class, 'create'])->name('cover-letters.create');
    Route::post('/cover-letters', [CoverLetterController::class, 'store'])->name('cover-letters.store');
    Route::get('/cover-letters/{coverLetter}/edit', [CoverLetterController::class, 'edit'])->name('cover-letters.edit');
    Route::match(['put', 'patch'], '/cover-letters/{coverLetter}', [CoverLetterController::class, 'update'])->name('cover-letters.update');
    Route::delete('/cover-letters/{coverLetter}', [CoverLetterController::class, 'destroy'])->name('cover-letters.destroy');
    Route::post('/cover-letters/ai-generate', [CoverLetterController::class, 'aiGenerate'])->name('cover-letters.ai-generate');

    // ─── Job Application Tracker (Disabled) ──────────────────────
    /*
    Route::get('/job-tracker', [JobApplicationController::class, 'index'])->name('job-tracker.index');
    Route::post('/job-tracker', [JobApplicationController::class, 'store'])->name('job-tracker.store');
    Route::match(['put', 'patch'], '/job-tracker/{jobApplication}', [JobApplicationController::class, 'update'])->name('job-tracker.update');
    Route::patch('/job-tracker/{jobApplication}/status', [JobApplicationController::class, 'updateStatus'])->name('job-tracker.status');
    Route::delete('/job-tracker/{jobApplication}', [JobApplicationController::class, 'destroy'])->name('job-tracker.destroy');
    */

    // ─── Sharing ──────────────────────────────────────────────────
    Route::patch('/resumes/{resume}/share/toggle', [PublicResumeController::class, 'toggleShare'])->name('resumes.share.toggle');
    Route::patch('/resumes/{resume}/share/regenerate', [PublicResumeController::class, 'regenerateToken'])->name('resumes.share.regenerate');
});

// Public Share View
Route::get('/share/{token}', [PublicResumeController::class, 'show'])->name('resumes.show');


Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');

    // Users
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::put('/users/{user}/role', [AdminController::class, 'toggleRole'])->name('users.toggleRole');
    Route::put('/users/{user}/active', [AdminController::class, 'toggleActive'])->name('users.toggleActive');
    Route::put('/users/{user}/plan', [AdminController::class, 'changePlan'])->name('users.changePlan');

    // Subscriptions
    Route::get('/subscriptions', [AdminController::class, 'subscriptions'])->name('subscriptions.index');
    Route::patch('/subscriptions/{user}/extend', [AdminController::class, 'extendSubscription'])->name('subscriptions.extend');
    Route::delete('/subscriptions/{user}/cancel', [AdminController::class, 'cancelSubscription'])->name('subscriptions.cancel');

    // Payments
    Route::get('/payments', [AdminController::class, 'payments'])->name('payments.index');
    Route::patch('/payments/{payment}/refund', [AdminController::class, 'refundPayment'])->name('payments.refund');
    Route::get('/payments/export', [AdminController::class, 'exportPaymentsCsv'])->name('payments.export');

    // Templates
    Route::get('/templates', [AdminController::class, 'templates'])->name('templates.index');
    Route::post('/templates', [AdminController::class, 'storeTemplate'])->name('templates.store');
    Route::put('/templates/{template}', [AdminController::class, 'updateTemplate'])->name('templates.update');
    Route::delete('/templates/{template}', [AdminController::class, 'destroyTemplate'])->name('templates.destroy');
    Route::patch('/templates/{template}/toggle', [AdminController::class, 'toggleTemplateActive'])->name('templates.toggle');

    // Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings.index');
    Route::patch('/settings/ai', [AdminController::class, 'updateAiSettings'])->name('settings.ai.update');
    Route::patch('/settings/google', [AdminController::class, 'updateGoogleSettings'])->name('settings.google.update');
    Route::patch('/settings/stripe', [AdminController::class, 'updateStripeSettings'])->name('settings.stripe.update');

    // Coupons
    Route::get('/coupons', [AdminController::class, 'coupons'])->name('coupons.index');
    Route::post('/coupons', [AdminController::class, 'storeCoupon'])->name('coupons.store');
    Route::delete('/coupons/{coupon}', [AdminController::class, 'destroyCoupon'])->name('coupons.destroy');

    // Plans Management (Phase 9)
    Route::get('/plans', [AdminController::class, 'plans'])->name('plans.index');
    Route::post('/plans', [AdminController::class, 'storePlan'])->name('plans.store');
    Route::put('/plans/{plan}', [AdminController::class, 'updatePlan'])->name('plans.update');
    Route::delete('/plans/{plan}', [AdminController::class, 'destroyPlan'])->name('plans.destroy');
    Route::patch('/plans/{plan}/toggle', [AdminController::class, 'togglePlanActive'])->name('plans.toggle');

    // User Impersonation (Phase 9)
    Route::post('/users/{user}/impersonate', [AdminController::class, 'impersonate'])->name('users.impersonate');
    Route::post('/impersonate/leave', [AdminController::class, 'leaveImpersonation'])->name('impersonate.leave');

    // SEO & Content (Phase 11)
    Route::get('/seo', [AdminController::class, 'seo'])->name('seo.index');
    Route::post('/seo', [AdminController::class, 'updateSeo'])->name('seo.update');
    Route::post('/settings/identity', [AdminController::class, 'updateIdentity'])->name('settings.identity.update');

    // AI Operations (Phase 12)
    Route::get('/ai-ops', [AdminController::class, 'aiOps'])->name('ai-ops.index');
    Route::post('/ai-ops/config', [AdminController::class, 'updateAiConfig'])->name('ai-ops.config.update');
    Route::post('/ai-ops/prompts', [AdminController::class, 'updateAiPrompts'])->name('ai-ops.prompts.update');

    // Marketing & Systems (Phase 13)
    Route::get('/newsletter', [AdminController::class, 'newsletter'])->name('newsletter.index');
    Route::post('/newsletter/export', [AdminController::class, 'exportSubscribers'])->name('newsletter.export');
    Route::get('/logs', [AdminController::class, 'logs'])->name('logs.index');
    Route::post('/maintenance/toggle', [AdminController::class, 'toggleMaintenance'])->name('maintenance.toggle');
});

// Stripe Checkout
Route::middleware('auth')->group(function () {
    Route::post('/stripe/checkout', [\App\Http\Controllers\StripeController::class, 'checkout'])->name('stripe.checkout');
    Route::get('/stripe/success', [\App\Http\Controllers\StripeController::class, 'success'])->name('subscription.success');
    Route::post('/stripe/validate-coupon', [\App\Http\Controllers\StripeController::class, 'validateCoupon'])->name('stripe.validate-coupon');

    // AI Resume Analysis
    Route::get('/analysis', [\App\Http\Controllers\ResumeAnalysisController::class, 'index'])->name('analysis.index');
    Route::post('/analysis/analyze', [\App\Http\Controllers\ResumeAnalysisController::class, 'analyze'])->name('analysis.analyze');
});


// Stripe Webhook (must be outside CSRF middleware — Stripe can't send CSRF tokens)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->name('stripe.webhook')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

require __DIR__.'/auth.php';
