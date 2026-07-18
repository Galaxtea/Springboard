<?php

namespace App\Traits;

use App\Models\Comment;

trait Commentable
{
	public function comments() {
		return $this->morphMany(Comment::class, 'commentable')->orderByDesc('id');
	}

	public function showComments() {
		$query = $this->comments();
		if(auth()->user()?->perms('can_msg_mod')) $query = $query->withTrashed();
		return $query->paginate();
	}
}
