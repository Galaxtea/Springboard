<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class UserSettings extends Model
{
	// Traits


	// Model Settings
		protected $table = 'user_settings';
		public $timestamps = false;
		protected $primaryKey = 'user_id';

		protected $fillable = [
			'user_id', 'email', 'password', 'birthday', 'timezone', 'reg_step', 'was_referred',
			'display_active', 'allow_messages', 'allow_friends', 'friend_code', 'private_profile'
		];

		protected $hidden = [
			'password',
		];

		protected function casts(): array {
			return [
				'email_verified_at' => 'datetime',
				'password' => 'hashed',
				'birthday' => 'date',
			];
		}


	// Mutators


	// Accessors


	// Relations


	// Functions
}