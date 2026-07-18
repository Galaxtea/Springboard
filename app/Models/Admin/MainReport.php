<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Commentable;
use App\Models\User\User;

class MainReport extends Model
{
	// Traits
		use Commentable;


	// Model Settings
		protected $table = 'reports';
		public $timestamps = true;
		protected $with = ['reporter', 'reportable'];

		protected $fillable = [
			'reporter_id', 'reported_id', 'category', 'title', 'content', 'status', 'claimed_by', 'action_taken',
			'reportable_type', 'reportable_id',
		];


	// Mutators


	// Accessors
		public function getUsernameAttribute() {
			return $this->reporter->username;
		}
		public function getLinkSourceAttribute() {
			return $this->reportable->link_url;
		}
		public function getLinkAttribute() {
			return "/report/{$this->id}";
		}


	// Relations
		public function reporter() {
			return $this->belongsTo(User::class);
		}
		public function reportable() {
			return $this->morphTo();
		}


	// Functions
}
