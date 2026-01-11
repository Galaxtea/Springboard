<?php

namespace App\Models\Site\Wordlist;

use Illuminate\Database\Eloquent\Model;

class ContextBlock extends Model
{
	// Model Settings
		protected $table = 'word_context_block';
		public $timestamps = false;

		protected $fillable = [
			'nickname', 'contexts'
		];


	// Mutators


	// Accessors


	// Relations


	// Functions
}