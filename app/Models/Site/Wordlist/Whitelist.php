<?php

namespace App\Models\Site\Wordlist;

use Illuminate\Database\Eloquent\Model;

class Whitelist extends Model
{
	// Model Settings
		protected $table = 'word_whitelist';
		public $timestamps = false;

		protected $fillable = [
			'word'
		];


	// Mutators


	// Accessors


	// Relations


	// Functions
}