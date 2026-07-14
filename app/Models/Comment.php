<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User\User;

use App\Traits\Reportable;

class Comment extends Model
{
	// Traits
		use SoftDeletes, Reportable;


	// Model Settings
		protected $table = 'comments';
		public $timestamps = true;
		protected $perPage = 25;

		protected $fillable = [
			'user_id', 'commentable_id', 'commentable_type', 'content_bbc', 'content_html',
		];

		protected function casts(): array {
			return [
				'created_at' => 'datetime',
				'updated_at' => 'datetime',
				'deleted_at' => 'datetime',
			];
		}


	// Mutators


	// Accessors
		public function getPostedAtAttribute() {
			return $this->created_at->tz(config('site_settings.site_time'))->format(config('site_settings.time_format'));
		}


	// Relations
		public function commentable() {
			return $this->morphTo();
		}
		public function poster() {
			return $this->belongsTo(User::class, 'user_id', 'id');
		}


	// Functions
}
