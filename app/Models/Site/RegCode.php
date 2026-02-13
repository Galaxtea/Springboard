<?php

namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;

class RegCode extends Model
{
	// Traits


	// Model Settings
		protected $table = 'reg_codes';
		public $timestamps = true;

		protected $fillable = [
			'token', 'is_used', 'maker_id', 'user_id'
		];


	// Mutators


	// Accessors


	// Relations


	// Functions
}