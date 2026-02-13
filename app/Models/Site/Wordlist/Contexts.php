<?php

namespace App\Models\Site\Wordlist;

use Illuminate\Database\Eloquent\Model;

class Contexts extends Model
{
	// Traits


	// Model Settings
		protected $table = 'word_contexts';
		public $timestamps = false;

		protected $fillable = [
			'context', 'words', 'subbed'
		];


	// Mutators


	// Accessors


	// Relations


	// Functions
}