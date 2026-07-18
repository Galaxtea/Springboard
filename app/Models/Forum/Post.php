<?php

namespace App\Models\Forum;

use Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\Reportable;

class Post extends Model
{
	// Traits
		use SoftDeletes, Reportable;


	// Model Settings
		protected $table = 'forum_posts';
		protected $perPage = 20;
		protected $with = ['poster'];

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
			return $this->created_at->tz(Config::get('site_settings.site_time'))->format(Config::get('site_settings.time_format'));
		}
		public function getEditedAtAttribute() {
			return $this->updated_at->tz(Config::get('site_settings.site_time'))->format(Config::get('site_settings.time_format'));
		}
		public function getIsDeletedAttribute() {
			return $this->deleted_at ? true : false;
		}
		public function getIsEditedAttribute() {
			return $this->editor_id ? true : false;
		}
		public function getReportTypeAttribute() {
			return "forum_post";
		}
		public function link_URL($slug = null) {
			if(!$slug) $slug = $this->board->slug;
			return "/forums/{$slug}/{$this->thread_id}/post_{$this->id}";
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
