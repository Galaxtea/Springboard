<?php

namespace App\Models\Forum;

use Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
	// Model Settings
		use SoftDeletes;
		protected $table = 'forum_posts';
		protected $perPage = 20;

		protected $fillable = [
			'poster_id', 'editor_id', 'thread_id', 'content_bbc', 'content_html',
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
			return $this->created_at->tz(Config::get('site_settings.site_time'))->format('jS F, Y \a\t g:ia');
		}
		public function getEditedAtAttribute() {
			return $this->updated_at->tz(Config::get('site_settings.site_time'))->format('jS F, Y \a\t g:ia');
		}
		public function getIsDeletedAttribute() {
			return $this->deleted_at ? true : false;
		}
		public function getIsEditedAttribute() {
			return $this->editor_id ? true : false;
		}
		public function getLinkAttribute() {
			return "/forums/{$this->board->slug}/{$this->thread_id}/post_{$this->id}";
		}


	// Relations
		public function poster() {
			return $this->belongsTo('App\Models\User\User');
		}
		public function editor() {
			return $this->belongsTo('App\Models\User\User', 'editor_id');
		}
		public function edits() {
			return $this->hasMany('App\Models\Forum\PostEdit');
		}
		public function thread() {
			return $this->belongsTo('App\Models\Forum\Thread');
		}
		public function board() {
			return $this->thread->board();
		}


	// Functions
}
