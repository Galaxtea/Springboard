<?php

namespace App\Models\Site\Wordlist;

use Illuminate\Database\Eloquent\Model;

class FilterList extends Model
{
	// Traits


	// Model Settings
		protected $table = 'word_lists';
		public $timestamps = false;

		protected $fillable = [
			'word', 'filter_type', 'regex', 'handle_hit', 'endings'
		];


	// Mutators


	// Accessors


	// Relations


	// Functions
}