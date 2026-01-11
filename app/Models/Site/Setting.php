<?php

namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
	// Model Settings
		protected $table = 'site_settings';
		public $timestamps = false;

		protected $fillable = [
			'ref_key', 'value', 'text',
		];


	// Mutators


	// Accessors


	// Relations


	// Functions
}