    // ─── Cover Letters ────────────────────────────────────────────
    Route::get('/cover-letters', [CoverLetterController::class, 'index'])->name('cover-letters.index');
    Route::get('/cover-letters/create', [CoverLetterController::class, 'create'])->name('cover-letters.create');
    Route::post('/cover-letters', [CoverLetterController::class, 'store'])->name('cover-letters.store');
    Route::get('/cover-letters/{coverLetter}/edit', [CoverLetterController::class, 'edit'])->name('cover-letters.edit');
    Route::put('/cover-letters/{coverLetter}', [CoverLetterController::class, 'update'])->name('cover-letters.update');
    Route::delete('/cover-letters/{coverLetter}', [CoverLetterController::class, 'destroy'])->name('cover-letters.destroy');
    Route::post('/cover-letters/ai-generate', [CoverLetterController::class, 'aiGenerate'])->name('cover-letters.ai-generate');

    // ─── Job Application Tracker ──────────────────────────────────
    Route::get('/job-tracker', [JobApplicationController::class, 'index'])->name('job-tracker.index');
    Route::post('/job-tracker', [JobApplicationController::class, 'store'])->name('job-tracker.store');
    Route::put('/job-tracker/{jobApplication}', [JobApplicationController::class, 'update'])->name('job-tracker.update');
    Route::patch('/job-tracker/{jobApplication}/status', [JobApplicationController::class, 'updateStatus'])->name('job-tracker.status');
    Route::delete('/job-tracker/{jobApplication}', [JobApplicationController::class, 'destroy'])->name('job-tracker.destroy');

    // ─── Public CV Share Links ────────────────────────────────────
    Route::patch('/resumes/{resume}/share/toggle', [PublicResumeController::class, 'toggleShare'])->name('resumes.share.toggle');
    Route::patch('/resumes/{resume}/share/regenerate', [PublicResumeController::class, 'regenerateToken'])->name('resumes.share.regenerate');
