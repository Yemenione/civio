<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicApiController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('v1')->group(function () {
        Route::get('/resumes', [PublicApiController::class, 'resumes']);
        Route::get('/resumes/{resume}', [PublicApiController::class, 'getResume']);
    });
});
