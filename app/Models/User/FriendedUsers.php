<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class FriendedUsers extends Model
{
	// Traits


	// Model Settings
		protected $table = 'user_friends';
		public $timestamps = false;
		public $primaryKey = 'friend_id';
		protected $with = ['isFriended', 'isRequesting'];

		protected $fillable = [
			'friend_id', 'friended_id', 'status'
		];


	// Mutators


	// Accessors


	// Relations
		public function isFriended() {
			return $this->belongsTo(User::class, 'friended_id', 'id');
		}
		public function isRequesting() {
			return $this->belongsTo(User::class, 'friend_id', 'id');
		}


	// Functions
}
