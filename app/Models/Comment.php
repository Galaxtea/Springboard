<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\Reportable;

class Comment extends Model
{
	// Traits
		use SoftDeletes, Reportable;


	// Model Settings
		protected $table = 'comments';
		public $timestamps = true;
		protected $perPage = 20;

		protected $fillable = [
			'poster_id', 'commentable_id', 'commentable_type', 'content_bbc', 'content_html',
		];


	// Mutators


	// Accessors


	// Relations
		public function commentable() {
			return $this->morphTo();
		}


	// Functions
}
