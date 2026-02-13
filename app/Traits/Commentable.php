<?php

namespace App\Traits;

trait Commentable
{
	public function comments() {
		return $this->morphMany(Comment::class, 'commentable');
	}
}
