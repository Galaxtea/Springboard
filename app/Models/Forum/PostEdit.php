<?php

namespace App\Models\Forum;

use Config;
use Illuminate\Database\Eloquent\Model;

class PostEdit extends Model
{
	// Traits


	// Model Settings
		protected $table = 'forum_post_edits';
		public $timestamps = false;
		protected $perPage = 20;

		protected $fillable = [
			'editor_id', 'post_id', 'content_bbc', 'content_html', 'created_at',
		];

		protected $casts = [
			'created_at' => 'datetime',
		];


	// Mutators


	// Accessors
		public function getContentAttribute() {
			return $this->content_bbc;
		}
		public function getDisplayContentAttribute() {
			return $this->content_html;
		}
		public function getPostedAtAttribute() {
			return $this->created_at->addHours(Config::get('site_settings.adjust'))->format('jS F, Y \a\t g:ia');
		}


	// Relations
		public function post() {
			return $this->belongsTo('App\Models\Forum\Post');
		}
		public function editor() {
			return $this->belongsTo('App\Models\User\User');
		}


	// Functions
}
