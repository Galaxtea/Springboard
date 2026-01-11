<?php

namespace App\Models\Site\Wordlist;

use Illuminate\Database\Eloquent\Model;

class Context extends Model
{
	// Model Settings
		protected $table = 'word_context';
		public $timestamps = false;

		protected $fillable = [
			'context', 'words', 'subbed'
		];


	// Mutators


	// Accessors


	// Relations


	// Functions
}