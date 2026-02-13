<?php

namespace App\Models\Site\Wordlist;

use Illuminate\Database\Eloquent\Model;

class ContextBlocks extends Model
{
	// Traits


	// Model Settings
		protected $table = 'word_context_blocks';
		public $timestamps = false;

		protected $fillable = [
			'nickname', 'contexts', 'handle_hit'
		];


	// Mutators


	// Accessors


	// Relations


	// Functions
}