<?php

namespace App\Models\Forum;

use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{
	// Traits


	// Model Settings
		protected $table = 'forums';
		public $timestamps = false;
		protected $with = ['boards', 'boards.subboards'];

		protected $fillable = [
			'name', 'description', 'sort',
		];


	// Mutators


	// Accessors


	// Relations
		public function boards() {
			return $this->hasMany('App\Models\Forum\Board', 'category')->orderBy('sort');
		}


	// Functions
}
