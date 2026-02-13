<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Commentable;

class UserProfile extends Model
{
	// Traits
		use Commentable;


	// Model Settings
		protected $table = 'user_profiles';
		public $timestamps = false;
		protected $primaryKey = 'user_id';

		protected $fillable = [
			'user_id', 'content_bbc', 'content_html', 'allow_comments', 'comment_count'
		];


	// Mutators


	// Accessors


	// Relations


	// Functions
}