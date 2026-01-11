<?php

namespace App\Models\Site\Wordlist;

use Illuminate\Database\Eloquent\Model;

class Blacklist extends Model
{
	// Model Settings
		protected $table = 'word_blacklist';
		public $timestamps = false;

		protected $fillable = [
			'word', 'filter_type', 'subbed'
		];


	// Mutators


	// Accessors


	// Relations


	// Functions
}