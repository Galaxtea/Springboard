<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Permissions extends Model
{
	// Traits


	// Model Settings
		protected $table = 'permissions';
		public $timestamps = false;

		protected $fillable = [
			'name', 'slug', 'description',
		];


	// Mutators


	// Accessors


	// Relations


	// Functions
}
