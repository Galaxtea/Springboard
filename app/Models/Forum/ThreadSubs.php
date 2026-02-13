<?php

namespace App\Models\Forum;

use Illuminate\Database\Eloquent\Model;

class ThreadSubs extends Model
{
	// Traits


	// Model Settings
		protected $table = 'forum_thread_subs';
		public $timestamps = false;

		protected $fillable = [
			'user_id', 'thread_id',
		];


	// Mutators


	// Accessors


	// Relations


	// Functions
}
