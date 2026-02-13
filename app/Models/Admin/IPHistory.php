<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class IPHistory extends Model
{
	// Traits


	// Model Settings
		protected $table = 'ip_history';
		public $timestamps = true;

		protected $fillable = [
			'user_id', 'ip_address'
		];

		protected function casts(): array {
			return [
				'created_at' => 'datetime',
				'updated_at' => 'datetime',
			];
		}


	// Mutators


	// Accessors
		public function getUsesAttribute() {
			return self::where('ip_address', $this->ip_address)->get();
		}
		public function getOverlapsAttribute() {
			$query = self::where('ip_address', $this->ip_address)->where(function ($query) {
				$query->where('created_at', '>=', $this->created_at)->where('created_at', '<=', $this->updated_at)->orWhere(function ($query) {
					$query->where('updated_at', '>=', $this->created_at)->where('updated_at', '<=', $this->updated_at);
				});
			})->get();
			return $query;
		}


	// Relations
		public function user() {
			return $this->belongsTo(User::class);
		}


	// Functions
}
