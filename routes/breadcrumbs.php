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
        $trail->push($thread->name, route('thread', [$thread->orig_board_id, $thread->id]));
        $trail->parent('board', $thread->board);
    });




// Users
    Breadcrumbs::for('profile', function($trail, $username, $id) {
        $trail->push($username."'s Profile", route('profile', $id));
        $trail->parent('home');
    });