<?php # Forum Routes

use App\Http\Controllers\Forum\ForumController;
use App\Http\Controllers\Forum\PostController;
use App\Http\Controllers\Forum\ThreadController;

Route::controller(ForumController::class)->prefix('forums')->group(function () {
	Route::get('/', 'index')->name('forums');

	Route::get('/{board}', 'getBoard')->name('board')->where('board', '[a-z_]+');


	Route::controller(ThreadController::class)->group(function () {
		Route::middleware(['auth', 'perms:can_msg_mod'])->group(function () {
			Route::get('/manage/{thread}', 'getManage')->where('thread', '[0-9]+');
			Route::post('/{thread}/edit', 'postEdit')->where('thread', '[0-9]+');
			Route::post('/{thread}/restore', 'postRestore')->where('thread', '[0-9]+');
			Route::post('/{thread}/move', 'postMove')->where('thread', '[0-9]+');
			Route::post('/{thread}/clone', 'postClone')->where('thread', '[0-9]+');
			Route::post('/{thread}/unclone', 'postUnclone')->where('thread', '[0-9]+');
		});

		Route::get('/{board}/{thread}', 'getThread')->name('thread')->where('board', '[a-z_]+')->where('thread', '[0-9]+');

		Route::get('/{board}/new', 'getNew')->where('board', '[a-z_]+');
		Route::post('/{board}/new', 'postNew')->where('board', '[a-z_]+');

		Route::post('/thread/{thread}/sub', 'addSub')->where('thread', '[0-9]+');
		Route::post('/thread/{thread}/unsub', 'removeSub')->where('thread', '[0-9]+');
	});


	Route::controller(PostController::class)->group(function () {
		Route::post('/{thread}/post', 'postNew')->where('thread', '[0-9]+');

		Route::prefix('post')->group(function () {
			Route::get('/{id}/edit', 'getEdit')->where('id', '[0-9]+');
			Route::post('/{id}/edit', 'postEdit')->where('id', '[0-9]+');

			Route::post('/{id}/delete', 'postDelete')->where('id', '[0-9]+');

			Route::get('/{id}/history', 'getHistory')->where('id', '[0-9]+');
		});
	});
});