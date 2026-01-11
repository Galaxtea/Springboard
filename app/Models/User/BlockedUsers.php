<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class BlockedUsers extends Model
{
	// Model Settings
		protected $table = 'user_blocks';
		public $timestamps = false;

		protected $fillable = [
			'blocked_id', 'self_note',
		];


	// Mutators


	// Accessors


	// Relations
		public function blockedBy() {
			return $this->belongsTo(User::class, 'blocker_id', 'id');
		}
		public function isBlocking() {
			return $this->belongsTo(User::class, 'blocked_id', 'id');
		}


	// Functions
}
