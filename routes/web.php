<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Front\AccountController;
use App\Http\Controllers\Front\AnnouncementController;
use App\Http\Controllers\Front\ContactController;
use App\Http\Controllers\Front\EventController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\JournalController;
use App\Http\Controllers\Front\MenuProfilController;
use App\Http\Controllers\Front\NewsController;
use App\Http\Controllers\Front\PaymentController;
use App\Http\Controllers\Front\TeamController;
use Illuminate\Support\Facades\Route;


// Route::get('/locale/{locale}', LocaleController::class)->name('locale.change');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/visit', [HomeController::class, 'vistWebsite'])->name('visit.ajax')->middleware('TrustProxies');
Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy.policy');
Route::get('/terms-of-service', [HomeController::class, 'termsOfService'])->name('terms.service');

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'index'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

// Forgot Password Routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');

Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

Route::prefix('account')->name('account.')->group(function () {
    Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
    Route::put('/profile/update', [AccountController::class, 'updateProfile'])->name('profile.update');
    Route::get('/profile/password', [AccountController::class, 'password'])->name('password');
    Route::put('/profile/password/update', [AccountController::class, 'passwordUpdate'])->name('password.update');
});

Route::get('/welcome', [HomeController::class, 'welcomeSpeech'])->name('welcome.speech');

Route::prefix('event')->name('event.')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('index');
    Route::get('/{slug}', [EventController::class, 'show'])->name('show');
    Route::post('/{slug}/register', [EventController::class, 'register'])->name('register');

    Route::get('/eticket/{uuid}', [EventController::class, 'eticket'])->name('eticket');

    Route::get('/presence/{code}', [EventController::class, 'presence'])->name('presence')->middleware('auth');
    Route::post('/presence/{code}/store', [EventController::class, 'presenceStore'])->name('presence.store')->middleware('auth');
});

Route::prefix('announcement')->name('announcement.')->group(function () {
    Route::get('/', [AnnouncementController::class, 'index'])->name('index');
    Route::get('/{slug}', [AnnouncementController::class, 'show'])->name('show');
});

Route::prefix('profil')->name('profil.')->group(function () {
    // Route::get('/', [MenuProfilController::class, 'index'])->name('index');
    Route::get('/{slug}', [MenuProfilController::class, 'show'])->name('show');
});

Route::prefix('team')->name('team.')->group(function () {
    Route::get('/editor', [TeamController::class, 'editor'])->name('editor');
    Route::get('/reviewer', [TeamController::class, 'reviewer'])->name('reviewer');
});

Route::prefix('news')->name('news.')->group(function () {
    Route::get('/', [NewsController::class, 'index'])->name('index');
    Route::get('/{slug}', [NewsController::class, 'detail'])->name('detail');

    Route::get('/category/{slug}', [NewsController::class, 'category'])->name('category');
    Route::post('/comment', [NewsController::class, 'comment'])->name('comment');

    Route::get('/visit/alt', [NewsController::class, 'visit'])->name('visit')->middleware('TrustProxies');
});

Route::prefix('journal')->name('journal.')->group(function () {
    Route::get('/', [JournalController::class, 'index'])->name('index');
    Route::get('/{journal_path}', [JournalController::class, 'detail'])->name('detail');
});

Route::prefix('payment')->name('payment.')->group(function () {
    Route::get('/', [PaymentController::class, 'index'])->name('index');
    Route::get('/{journal_path}/submission/{submission_id}', [PaymentController::class, 'submission'])->name('submission');
    Route::get('/{journal_path}/submission/{submission_id}/pay', [PaymentController::class, 'pay'])->name('pay');
    Route::post('/{journal_path}/submission/{submission_id}/pay', [PaymentController::class, 'payStore'])->name('pay.store');
});

Route::prefix('contact')->name('contact.')->group(function () {
    Route::get('/', [ContactController::class, 'index'])->name('index');
    Route::post('/', [ContactController::class, 'send'])->name('send');
});
