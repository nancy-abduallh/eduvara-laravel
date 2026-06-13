<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\OnboardingController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\VideoController;
use App\Http\Controllers\Student\UploadController;
use App\Http\Controllers\Student\QuizController;
use App\Http\Controllers\Student\HistoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\VideoController as AdminVideoController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Student\ChatController;


// ─── Localization Route (must be outside locale group) ───
Route::get('locale/{locale}', [LocaleController::class, 'setLocale'])->name('locale.set');

// ─── Group all routes with localization ───────────────────
Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localize']
], function() {

    // ─── Public ────────────────────────────────────────────
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // ─── Custom Auth Routes ─────────────────────────────────
    Route::middleware('guest')->group(function () {
        Route::get('/login',    [LoginController::class, 'showLogin'])->name('login');
        Route::post('/login',   [LoginController::class, 'login']);
        Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
        Route::post('/register',[RegisterController::class, 'register']);
    });

    Route::post('/logout', [LoginController::class, 'logout'])
        ->middleware('auth')
        ->name('logout');

    // ─── Onboarding ────────────────────────────────────────
    Route::middleware(['auth'])->prefix('onboarding')->name('onboarding.')->group(function () {
        Route::get('/vark',  [OnboardingController::class, 'showVark'])->name('vark');
        Route::post('/vark', [OnboardingController::class, 'submitVark'])->name('vark.submit');
        Route::get('/result',[OnboardingController::class, 'showResult'])->name('result');
    });

    // ─── Student Area ───────────────────────────────────────
    // ─── Student Area ───────────────────────────────────────
    Route::middleware(['auth', 'onboarding'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/upload',  [UploadController::class, 'index'])->name('upload');
        Route::post('/upload', [UploadController::class, 'store'])->name('upload.store');

        Route::post('/videos/request', [VideoController::class, 'requestGeneration'])->name('videos.request');
        
        Route::get('/videos',               [VideoController::class, 'index'])->name('videos');
        Route::get('/videos/{video}',       [VideoController::class, 'show'])->name('videos.show');
        Route::get('/videos/{video}/status', [VideoController::class, 'status'])->name('videos.status');

        Route::get('/quiz/{video}',          [QuizController::class, 'show'])->name('quiz.show');
        Route::post('/quiz/{quiz}/submit',   [QuizController::class, 'submit'])->name('quiz.submit');
        Route::get('/quiz/result/{attempt}', [QuizController::class, 'result'])->name('quiz.result');

        Route::get('/history',        [HistoryController::class, 'index'])->name('history');
        Route::get('/history/chats',  [HistoryController::class, 'chats'])->name('history.chats');
        Route::get('/history/videos', [HistoryController::class, 'videos'])->name('history.videos');
        Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');

    });

    // ─── Admin Area ─────────────────────────────────────────
    Route::middleware(['auth', 'admin'])->prefix('control-panel')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('users',  AdminUserController::class);
        Route::resource('videos', AdminVideoController::class)->only(['index', 'show', 'destroy']);
        Route::get('/system',    [AdminDashboardController::class, 'system'])->name('system');
    });

});