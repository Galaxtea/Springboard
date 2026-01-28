<?php # Forum Routes

use App\Models\Forum\Board;
use App\Http\Controllers\Forum\ForumController;
use App\Http\Controllers\Forum\PostController;
use App\Http\Controllers\Forum\ThreadController;

Route::controller(ForumController::class)->prefix('forums')->group(function () {
	Route::get('/', 'index')->name('forums');

	Route::get('/{board}', 'getBoard')->name('board')->whereIn('board', Board::pluck('slug')->toArray());


	Route::controller(ThreadController::class)->group(function () {
		Route::middleware(['auth', 'perms:can_msg_mod'])->group(function () {
			Route::get('/manage/{thread}', 'getManage')->whereNumber('thread');
			Route::post('/{thread}/edit', 'postEdit')->whereNumber('thread');
			Route::post('/{thread}/restore', 'postRestore')->whereNumber('thread');
			Route::post('/{thread}/move', 'postMove')->whereNumber('thread');
			Route::post('/{thread}/clone', 'postClone')->whereNumber('thread');
			Route::post('/{thread}/unclone', 'postUnclone')->whereNumber('thread');
		});

		Route::get('/{board}/{thread}', 'getThread')->name('thread')->whereIn('board', Board::pluck('slug')->toArray())->whereNumber('thread');

		Route::get('/{board}/new', 'getNew')->whereIn('board', Board::pluck('slug')->toArray());
		Route::post('/{board}/new', 'postNew')->whereIn('board', Board::pluck('slug')->toArray());

		Route::post('/thread/{thread}/sub', 'addSub')->whereNumber('thread');
		Route::post('/thread/{thread}/unsub', 'removeSub')->whereNumber('thread');
	});


	Route::controller(PostController::class)->group(function () {
		Route::post('/{thread}/post', 'postNew')->whereNumber('thread');

		Route::prefix('post')->group(function () {
			Route::get('/{id}', 'locatePost')->name('forum_post');

			Route::post('/preview', 'postPreview');

			Route::get('/{id}/edit', 'getEdit');
			Route::post('/{id}/edit', 'postEdit');

			Route::post('/{id}/delete', 'postDelete');

			Route::get('/{id}/history', 'getHistory');
		});
	});
});