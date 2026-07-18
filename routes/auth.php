<?php

use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\EmailVerifyController;


Route::controller(AuthController::class)->group(function() {
	Route::middleware(['guest'])->group(function() {
		Route::get('/login', 'getLogin')->name('login');
		Route::post('/login', 'postLogin')->middleware(['throttle:login']);

		Route::get('/register', 'getRegister')->name('register');
		Route::post('/register', 'postRegister')->middleware(['throttle:register']);

		Route::get('/forgot-password', 'getForgotPassword')->name('password.request');
		Route::post('/forgot-password', 'postForgotPassword')->middleware(['throttle:forgot-password']);
		Route::get('/reset-password/{token}', 'getResetPassword')->name('password.reset');
		Route::post('/reset-password', 'postResetPassword')->name('password.update');
	});

	Route::middleware(['auth'])->group(function() {
		Route::post('/logout', 'postLogout');
	});
});


Route::controller(EmailVerifyController::class)->middleware(['auth'])->group(function() {
		Route::get('/email/verify', 'getNeedEmailVerify')->name('verification.notice');
		Route::get('/email/verify/{id}/{hash}', 'getVerifyingEmail')->middleware(['signed'])->name('verification.verify');
		Route::post('/email/send-verify', 'postSendVerifyEmail')->middleware(['throttle:3,1'])->name('verification.send');
});