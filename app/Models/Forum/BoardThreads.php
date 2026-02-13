<?php

namespace App\Models\Forum;

use Illuminate\Database\Eloquent\Model;

class BoardThreads extends Model
{
	// Traits


	// Model Settings
		protected $table = 'forum_board_threads';
		public $timestamps = false;
		public $incrementing = false;

		protected $fillable = [
			'thread_id', 'board_id', 'is_sticky',
		];


	// Mutators


	// Accessors


	// Relations
		public function board() {
			return $this->belongsTo('App\Models\Forum\Board');
		}
		public function thread() {
			return $this->belongsTo('App\Models\Forum\Thread');
		}


	// Functions
}
