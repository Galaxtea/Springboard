<?php

namespace App\Models\Site\Wordlist;

use Illuminate\Database\Eloquent\Model;

class LetterSubs extends Model
{
	// Traits


	// Model Settings
		protected $table = 'word_letter_subs';
		public $timestamps = false;

		protected $fillable = [
			'letter', 'subs', 'regex'
		];


	// Mutators


	// Accessors


	// Relations


	// Functions
}