<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class RankPerms extends Model
{
	// Model Settings
		protected $table = 'rank_permissions';
		public $timestamps = false;

		protected $fillable = [
			'rank_id', 'permission_id',
		];


	// Mutators


	// Accessors


	// Relations


	// Functions
}
