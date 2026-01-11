<?php

namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;

class SiteNews extends Model
{
	// Model Settings
		protected $table = 'site_news';
		public $timestamps = false;
		public $primaryKey = 'thread_id';
		public $perPage = 5;

		protected $fillable = [
			'thread_id', 'content_html',
		];


	// Mutators


	// Accessors
		// Display
			public function getDisplayAttribute() {
				return '<div><h2>'.$this->display_name.'</h2>'.$this->content_html.$this->link_more.'</div>';
			}

		// Additions
			public function getLinkMoreAttribute() {
				return '<div class="text-center"><a href="'.$this->link.'"><b>Read the full announcement!</b></a></div>';
			}
			public function getLinkAttribute() {
				return '/forums/'.$this->thread->board->slug.'/'.$this->thread_id;
			}

			public function getDisplayNameAttribute() {
				return $this->thread->display_name;
			}
			public function getPostedAtAttribute() {
				return $this->thread->first->posted_at;
			}


	// Relations
		public function thread() {
			return $this->belongsTo('App\Models\Forum\Thread');
		}


	// Functions
}