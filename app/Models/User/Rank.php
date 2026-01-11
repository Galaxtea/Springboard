<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Rank extends Model
{
	// Model Settings
		protected $table = 'ranks';
		public $timestamps = false;

		protected $fillable = [
			'name', 'slug', 'color', 'description', 'is_staff', 'is_admin', 'symbol',
		];


	// Mutators


	// Accessors


	// Relations
		public function powers() {
			return $this->hasManyThrough(Permissions::class, RankPerms::class, 'rank_id',  'id',  'id',  'permission_id');
		}


	// Functions
}
