<?php
// Main
	Breadcrumbs::for('home', function($trail) {
		$trail->push('Home', route('home'));
	});



// Misc
	Breadcrumbs::for('online', function($trail) {
		$trail->push('Online Users', route('online'));
		$trail->parent('home');
	});




// Forums
	Breadcrumbs::for('forums', function($trail) {
		$trail->push('Forums', route('forums'));
		$trail->parent('home');
	});

	Breadcrumbs::for('board', function($trail, $board) {
		$trail->push($board->name, route('board', $board->slug));
		if($board->parent) $trail->parent('board', $board->parent);
		else $trail->parent('forums');
	});

	Breadcrumbs::for('thread', function($trail, $thread) {
		$trail->push($thread->name, route('thread', [$thread->board->slug, $thread->id]));
		$trail->parent('board', $thread->board);
	});

	Breadcrumbs::for('post_history', function($trail, $thread) {
		$trail->push('Post Edit History');
		$trail->parent('thread', $thread);
	});

	Breadcrumbs::for('post_edit', function($trail, $thread) {
		$trail->push('Editing Post');
		$trail->parent('thread', $thread);
	});




// Users
	Breadcrumbs::for('profile', function($trail, $username, $id) {
		$trail->push($username."'s Profile", route('profile', $id));
		$trail->parent('home');
	});