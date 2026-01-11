<?php

use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

use App\Models\User\User;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\User\UserController;


Route::controller(HomeController::class)->group(function() {
    Route::get('/', 'index')->name('home');
    Route::get('/online', 'online')->name('online');
});

Route::controller(UserController::class)->group(function() {
    Route::get('/user/{user_id}', 'getProfile')->name('profile')->where('user_id', '[0-9]+');

    Route::get('/settings', 'getSettings')->name('settings');
    Route::post('/settings', 'updateSettings');

    Route::get('/blocks', 'blockList');
    Route::post('/block/{user_id}', 'blockUser')->where('user_id', '[0-9]+');
    Route::post('/unblock/{user_id}', 'unblockUser')->where('user_id', '[0-9]+');

    Route::get('/friends', 'friendList');
    Route::post('/friend/{user_id}', 'friendUser')->where('user_id', '[0-9]+');
    Route::post('/unfriend/{user_id}', 'unfriendUser')->where('user_id', '[0-9]+');


});









require_once __DIR__.'/forums.php';














// This needs to stay at the very bottom
Route::fallback(function() {
    abort(404);
});