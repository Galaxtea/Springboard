<?php

namespace App\Models\Forum;

use Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Thread extends Model
{
	// Model Settings
		use SoftDeletes;
		protected $table = 'forum_threads';
		protected $perPage = 25;

		protected $fillable = [
			'name', 'board_id', 'poster_id', 'orig_board_id',
			'first_post_id', 'last_post_id', 'post_count',
			'is_sticky', 'is_locked', 'has_clones',
		];


	// Mutators


	// Accessors
		public function getPostCounterAttribute() {
			$return = number_format($this->post_count) . ' Post';
			if($this->post_count != 1) $return = $return.'s';
			return $return;
		}

		public function getDisplayIconAttribute() {
			$icon = 'thread';
			if($this->is_sticky) $icon = $icon . '_sticky';
			if($this->is_locked) $icon = $icon . '_locked';

			return '<img src="/images/forums/threads/'.$icon.'.png">';
		}
		public function getIsDeletedAttribute() {
			return $this->deleted_at ? true : false;
		}
		public function getRemovedAtAttribute() {
			return $this->deleted_at->addHours(Config::get('site_settings.adjust'))->format('jS F, Y \a\t g:ia');
		}
		public function getPostedAtAttribute() {
			return $this->created_at->addHours(Config::get('site_settings.adjust'))->format('jS F, Y \a\t g:ia');
		}
		public function getDisplayNameAttribute() {
			return '<a href="'.$this->link.'">'.$this->name.'</a>';
		}
		public function getLinkAttribute() {
			return '/forums/'.$this->board->slug.'/'.$this->id;
		}
		public function getTaggedAttribute() {
			return array_column($this->tags->toArray(), 'tag_id');
		}

		public function subbedBy($user_id) {
			return $this->subList()->where('user_id', $user_id)->first();
		}


	// Relations
		public function poster() {
			return $this->belongsTo('App\Models\User\User');
		}
		public function first() {
			return $this->hasOne('App\Models\Forum\Post', 'id', 'first_post_id');
		}
		public function board() {
			return $this->belongsTo('App\Models\Forum\Board', 'orig_board_id');
		}
		public function boards() {
			return $this->hasManyThrough('App\Models\Forum\Board', 'App\Models\Forum\BoardThreads', 'thread_id', 'id', 'id', 'board_id');
		}
		public function posts() {
			return $this->hasMany('App\Models\Forum\Post');
		}
		public function latest() {
			return $this->hasOne('App\Models\Forum\Post', 'id', 'last_post_id');
		}

		public function tags() {
			return $this->morphMany('App\Models\Site\Tag\ContentTagged', 'content');
		}

		public function subList() {
			return $this->hasMany(ThreadSubs::class);
		}
		public function subscribers() {
			return $this->hasManyThrough(User::class, ThreadSubs::class, 'thread_id', 'id', 'id', 'user_id');
		}


	// Functions
}
