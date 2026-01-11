<?php

namespace App\Models\Site\Wordlist;

use Illuminate\Database\Eloquent\Model;

class WordReport extends Model
{
	// Model Settings
		protected $table = 'word_report';
		public $timestamps = true;

		protected $fillable = [
			'hit_content', 'hit_as', 'source', 'review_status'
		];


	// Mutators


	// Accessors


	// Relations


	// Functions
}