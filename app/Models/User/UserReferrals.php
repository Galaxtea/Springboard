<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class UserReferrals extends Model
{
	// Traits


	// Model Settings
		protected $table = 'user_referrals';
		public $timestamps = false;
		protected $primaryKey = 'user_id';

		protected $fillable = [
			'user_id', 'referred_by'
		];


	// Mutators


	// Accessors


	// Relations


	// Functions
}