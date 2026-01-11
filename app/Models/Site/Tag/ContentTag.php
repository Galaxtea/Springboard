<?php

namespace App\Models\Site\Tag;

use Illuminate\Database\Eloquent\Model;

class ContentTag extends Model
{
	// Model Settings
		protected $table = 'content_tags';
		public $timestamps = false;

		protected $fillable = [
			'name', 'display_name', 'type',
			'req_perm', 'req_rank', 'color', 'description'
		];


	// Mutators


	// Accessors
		public function getDisplayAttribute() {
			return '<span class="'.($this->class).'" title="'.$this->name.': '.$this->description.'">'.$this->display_name.'</span>';
		}
		public function getDisplayFullAttribute() {
			return '<span class="'.($this->class).'" title="'.$this->name.': '.$this->description.'">'.$this->name.'</span>';
		}

		public function getClassAttribute() {
			return "tag".($this->color ? " tag-{$this->color}" : null);
		}


	// Relations


	// Scopes
		public function scopeUsable($query, $user) {
			return $query->where(function($q) use ($user) {
				$q->where('req_rank', $user->rank->slug);
			})->orWhere(function($q) use ($user) {
				$q->whereNull('req_rank')->whereIn('req_perm', $user->perm_list);
			})->orWhere(function($q) {
				$q->whereNull('req_rank')->whereNull('req_perm');
			});
		}


	// Functions
}