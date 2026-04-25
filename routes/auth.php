<?php

use App\Http\Controllers\AuthController;

Route::controller(AuthController::class)->group(function() {
	Route::middleware(['guest'])->group(function() {
		Route::get('/login', 'getLogin')->name('login');
		Route::post('/login', 'postLogin');

		Route::get('/register', 'getRegister')->name('register');
		Route::post('/register', 'postRegister');

		Route::get('/forgot-password', 'getForgotPassword')->name('password.request');
		Route::post('/forgot-password', 'postForgotPassword');
		Route::get('/reset-password/{token}', 'getResetPassword')->name('password.reset');
		Route::post('/reset-password', 'postResetPassword')->name('password.update');
	});

	Route::middleware(['auth'])->group(function() {
		Route::post('/logout', 'postLogout');

		Route::get('/email/verify', 'getNeedEmailVerify')->name('verification.notice');
		Route::get('/email/verify/{id}/{hash}', 'getVerifyingEmail')->middleware(['signed'])->name('verification.verify');
		Route::post('/email/send-verify', 'postSendVerifyEmail')->middleware(['throttle:6,1'])->name('verification.send');
	});
});

