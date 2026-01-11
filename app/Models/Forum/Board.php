<?php

namespace App\Models\Forum;

use Auth;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
	// Model Settings
		protected $table = 'forum_boards';
		public $timestamps = false;

		protected $fillable = [
			'name', 'slug', 'description', 'category', 'parent_board', 'thread_count',
			'is_public', 'taggable_type', 'icon',
			'can_read', 'can_post', 'can_new',
		];


	// Mutators


	// Accessors
		public function getThreadCounterAttribute() {
			$return = number_format($this->thread_count) . ' Thread';
			if($this->thread_count != 1) $return = $return.'s';
			return $return;
		}

		public function getTaggableAttribute() {
			$return = ['staff'];
			if($this->taggable_type) array_push($return, $this->taggable_type);
			return $return;
		}

		public function getDisplayIconAttribute() {
			return '<img src="/images/forums/'.$this->icon.'.png">';
		}
		public function getDisplayNameAttribute() {
			return '<a href="'.$this->link.'">'.$this->name.'</a>';
		}
		public function getLinkAttribute() {
			return '/forums/'.$this->slug;
		}


	// Relations
		public function parent() {
			return $this->belongsTo('App\Models\Forum\Board', 'parent_board');
		}
		public function subboards() {
			return $this->hasMany('App\Models\Forum\Board', 'parent_board');
		}
		public function threads() {
			$threads = $this->hasManyThrough('App\Models\Forum\Thread', 'App\Models\Forum\BoardThreads', 'board_id', 'id', 'id', 'thread_id')->orderBy('is_sticky', 'DESC');
			if($this->id !== 2) $threads->orderBy('updated_at', 'DESC');

			return $threads->with('tags');
		}


	// Functions
		public function checkPerms($perms = ['can_read'], $user = null) {
			if(!$user && Auth::check()) $user = Auth::user();

			foreach($perms as $perm) {
				switch($perm) {
					case 'can_read':
						if(!$user && !$this->is_public) return false;
						break;
					default:
						if(!$user) return false;
				}
				if(!$this->$perm && !$user->perms('forum_boost')) return false;
			}

			return true;
		}
}
