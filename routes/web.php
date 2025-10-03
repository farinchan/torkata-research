<?php

use App\Http\Controllers\Front\HomeController;
use Illuminate\Support\Facades\Route;


// Route::get('/locale/{locale}', LocaleController::class)->name('locale.change');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/visit', [HomeController::class, 'vistWebsite'])->name('visit.ajax')->middleware('TrustProxies');
Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy.policy');
Route::get('/terms-of-service', [HomeController::class, 'termsOfService'])->name('terms.service');
