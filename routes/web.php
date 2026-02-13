<?php

use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

use App\Models\User\User;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\User\UserController;


Route::controller(HomeController::class)->group(function() {
	Route::get('/', 'index')->withoutMiddleware(['auth'])->name('home');
	Route::get('/online', 'online')->name('online');
});

Route::controller(UserController::class)->group(function() {
	Route::get('/user/{user_id}', 'getProfile')->withoutMiddleware(['auth'])->name('profile')->whereNumber('user_id');

	Route::get('/settings', 'getSettings')->name('settings');
	Route::post('/settings', 'updateSettings');

	Route::get('/blocks', 'blockList');
	Route::post('/block/{user_id}', 'blockUser')->whereNumber('user_id');
	Route::post('/unblock/{user_id}', 'unblockUser')->whereNumber('user_id');

	Route::get('/friends', 'friendList');
	Route::post('/friend/{user_id}', 'friendUser')->whereNumber('user_id');
	Route::post('/unfriend/{user_id}', 'unfriendUser')->whereNumber('user_id');


});









require_once __DIR__.'/admin.php';
require_once __DIR__.'/forums.php';














// This needs to stay at the very bottom
Route::fallback(function() {
	abort(404);
});