<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class UserStats extends Model
{
	// Traits


	// Model Settings
		protected $table = 'user_stats';
		public $timestamps = false;
		protected $primaryKey = 'user_id';

		protected $fillable = [
		];


	// Mutators


	// Accessors


	// Relations


	// Functions
}