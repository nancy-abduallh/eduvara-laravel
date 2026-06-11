<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AiWebhookController;
use App\Http\Controllers\Api\VideoStatusController;

// AI Webhooks (secured by signature)
Route::prefix('webhooks')->name('api.webhook.')->group(function () {
    Route::post('/video-complete', [AiWebhookController::class, 'videoComplete'])->name('video');
    Route::post('/quiz-complete', [AiWebhookController::class, 'quizComplete'])->name('quiz');
    Route::post('/adaptive-complete', [AiWebhookController::class, 'adaptiveComplete'])->name('adaptive');
});

// Status Polling
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/videos/{video}/status', [VideoStatusController::class, 'show'])->name('api.video.status');
});
