<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\PdfController;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

Route::middleware('guest')->group(function () {
    Route::get('/otp/verify', [OtpController::class, 'showVerifyForm'])->name('otp.verify');
    Route::post('/otp/verify', [OtpController::class, 'verify']);
    Route::post('/otp/resend', [OtpController::class, 'resend'])->name('otp.resend');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/book', [BookController::class, 'index'])->name('book.index');
    Route::get('/book/create', [BookController::class, 'create'])->name('book.create');
    Route::post('/book', [BookController::class, 'store'])->name('book.store');
    Route::get('/category', [CategoryController::class, 'index'])->name('category.index');
    Route::get('/category/create', [CategoryController::class, 'create'])->name('category.create');
    Route::post('/category', [CategoryController::class, 'store'])->name('category.store');
    Route::get('/pdf/sertifikat', [PdfController::class, 'certificate'])->name('pdf.sertifikat');
    Route::get('/pdf/undangan', [PdfController::class, 'invitation'])->name('pdf.undangan');

});
Auth::routes();
