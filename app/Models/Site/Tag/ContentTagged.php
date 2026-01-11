<?php

namespace App\Models\Site\Tag;

use Illuminate\Database\Eloquent\Model;

class ContentTagged extends Model
{
	// Model Settings
		protected $table = 'content_tagged';
		public $timestamps = false;

		protected $fillable = [
			'content_type', 'content_id', 'tag_id'
		];


	// Mutators


	// Accessors
		public function getDisplayAttribute() {
			return $this->tag->display;
		}


	// Relations
		public function tag() {
			return $this->belongsTo('App\Models\Site\Tag\ContentTag', 'tag_id');
		}


	// Functions
}